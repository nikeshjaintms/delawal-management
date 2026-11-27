<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\BrokerCommission;
use App\Models\ContractorPayment;
use App\Models\Expense;
use App\Models\Income;
use App\Models\PartnerPayout;
use App\Models\PartnerShare;
use App\Models\Project;
use App\Models\Property;
use App\Models\PropertyMaster;
use App\Models\PropertyMasterPayment;
use App\Models\PropertySale;
use App\Models\RentalPayment;
use App\Models\StockInward;
use Illuminate\Support\Collection;

class PartnerProfitService
{
    /**
     * Compute profit summary, partner entitlement, paid total, and balance payable for a share.
     */
    public function calculateForShare(PartnerShare $share): array
    {
        $metrics = match ($share->scope_type) {
            PartnerShare::SCOPE_FIRM            => $this->computeFirmProfit((int) $share->firm_id),
            PartnerShare::SCOPE_PROJECT         => $this->computeProjectProfit((int) $share->project_id),
            PartnerShare::SCOPE_PROPERTY_MASTER => $this->computePropertyMasterProfit((int) $share->property_master_id),
            PartnerShare::SCOPE_PROPERTY        => $this->computePropertyProfit((int) $share->property_id),
            default                             => $this->emptyMetrics(),
        };

        $pct = (float) $share->share_percentage;
        $realizedProfit = $metrics['realized_profit'];
        $bookedProfit   = $metrics['booked_profit'];

        // Entitled share is 0 when realized/booked profit is negative (losses are not paid out)
        $realizedEntitled = round(max(0.0, $realizedProfit) * ($pct / 100), 2);
        $bookedEntitled   = round(max(0.0, $bookedProfit) * ($pct / 100), 2);

        $paidAmount = (float) PartnerPayout::where('partner_share_id', $share->id)->sum('amount');
        $remainingPayable = round($realizedEntitled - $paidAmount, 2);
        $bookedRemaining  = round($bookedEntitled - $paidAmount, 2);

        return array_merge($metrics, [
            'share_percentage'  => $pct,
            'realized_entitled' => $realizedEntitled,
            'booked_entitled'   => $bookedEntitled,
            'paid_amount'       => round($paidAmount, 2),
            'remaining_payable' => $remainingPayable,
            'balance_due'       => round(max(0.0, $remainingPayable), 2),
            'overdrawn_amount'  => round(max(0.0, -$remainingPayable), 2),
            'booked_remaining'  => $bookedRemaining,
        ]);
    }

    /**
     * Compute profit summary for a collection of shares.
     */
    public function calculateForShares(Collection $shares): array
    {
        $results = [];
        foreach ($shares as $share) {
            $results[$share->id] = $this->calculateForShare($share);
        }
        return $results;
    }

    /**
     * Validate that total active share percentages on the same scope target do not exceed 100%.
     */
    public function allocatedPercentageForTarget(
        string $scopeType,
        int $firmId,
        ?int $projectId = null,
        ?int $propertyMasterId = null,
        ?int $propertyId = null,
        ?int $excludeShareId = null
    ): float {
        $query = PartnerShare::where('firm_id', $firmId)
            ->where('scope_type', $scopeType)
            ->where('status', 'active');

        if ($scopeType === PartnerShare::SCOPE_PROJECT) {
            $query->where('project_id', $projectId);
        } elseif ($scopeType === PartnerShare::SCOPE_PROPERTY_MASTER) {
            $query->where('property_master_id', $propertyMasterId);
        } elseif ($scopeType === PartnerShare::SCOPE_PROPERTY) {
            $query->where('property_id', $propertyId);
        }

        if ($excludeShareId) {
            $query->where('id', '!=', $excludeShareId);
        }

        return round((float) $query->sum('share_percentage'), 2);
    }

    private function computeFirmProfit(int $firmId): array
    {
        if ($firmId <= 0) {
            return $this->emptyMetrics();
        }

        $sales = PropertySale::with(['payments', 'properties.propertyMaster', 'property.propertyMaster'])
            ->where('firm_id', $firmId)
            ->where('sale_status', '!=', 'cancelled')
            ->get();

        $soldPropIds = $this->extractSoldPropertyIds($sales);

        $saleCash    = (float) $sales->sum(fn ($s) => $this->saleCollectedCash($s));
        $saleRevenue = (float) $sales->sum('sale_amount');
        $saleCost    = (float) $sales->sum(fn ($s) => $s->total_purchase_cost);

        $bookingCash = $this->bookingAdvancesForFirm($firmId, $soldPropIds);

        $rentalCash = (float) RentalPayment::where(function ($q) use ($firmId) {
            $q->where('firm_id', $firmId)
              ->orWhereHas('rental', fn ($rq) => $rq->where('firm_id', $firmId));
        })->sum('paid_amount');

        $otherIncome = (float) Income::where('firm_id', $firmId)
            ->where(function ($q) {
                $q->whereNull('description')->orWhere('description', 'not like', '%[AUTO:BK#%');
            })
            ->sum('amount');

        $landPaid          = (float) PropertyMasterPayment::where('firm_id', $firmId)->sum('amount');
        $expensesTotal     = (float) Expense::where('firm_id', $firmId)->sum('amount');
        $contractorPaid    = (float) ContractorPayment::where('firm_id', $firmId)->sum('amount');
        $commissionPaid    = (float) BrokerCommission::where('firm_id', $firmId)->where('payment_status', 'paid')->sum('commission_amount');
        $commissionBooked  = (float) BrokerCommission::where('firm_id', $firmId)->sum('commission_amount');
        $directStockInward = (float) StockInward::where('firm_id', $firmId)->whereNull('purchase_order_id')->sum('total_amount');

        $realizedInflow  = $saleCash + $bookingCash + $rentalCash + $otherIncome;
        $realizedOutflow = $landPaid + $expensesTotal + $contractorPaid + $commissionPaid + $directStockInward;

        $bookedRevenue = $saleRevenue + $rentalCash + $otherIncome;
        $bookedCost    = $saleCost + $expensesTotal + $contractorPaid + $commissionBooked + $directStockInward;

        return $this->formatMetrics($realizedInflow, $realizedOutflow, $bookedRevenue, $bookedCost);
    }

    private function computeProjectProfit(int $projectId): array
    {
        $project = Project::with('propertyMasters')->find($projectId);
        if (!$project) {
            return $this->emptyMetrics();
        }

        $propIds = Property::where('project_id', $projectId)->pluck('id')->toArray();
        $pmIds   = $project->propertyMasters->pluck('id')->toArray();
        if ($project->property_id && !in_array($project->property_id, $pmIds)) {
            $pmIds[] = $project->property_id;
        }

        $sales = !empty($propIds)
            ? PropertySale::with(['payments', 'properties.propertyMaster', 'property.propertyMaster'])
                ->where('sale_status', '!=', 'cancelled')
                ->where(function ($q) use ($propIds) {
                    $q->whereIn('property_id', $propIds)
                      ->orWhereHas('properties', fn ($pq) => $pq->whereIn('properties.id', $propIds));
                })
                ->get()
            : collect();

        $soldPropIds = $this->extractSoldPropertyIds($sales);
        $saleCash    = (float) $sales->sum(fn ($s) => $this->saleCollectedCash($s));
        $saleRevenue = (float) $sales->sum('sale_amount');
        $saleCost    = (float) $sales->sum(fn ($s) => $s->total_purchase_cost);

        $bookingCash = $this->bookingAdvancesForProperties($propIds, $soldPropIds);

        $rentalCash = !empty($propIds)
            ? (float) RentalPayment::where(function ($q) use ($propIds) {
                $q->whereIn('property_id', $propIds)
                  ->orWhereHas('rental', fn ($rq) => $rq->whereIn('property_id', $propIds))
                  ->orWhereHas('rental.properties', fn ($rq) => $rq->whereIn('properties.id', $propIds));
            })->sum('paid_amount')
            : 0.0;

        $otherIncome = !empty($propIds)
            ? (float) Income::whereIn('property_id', $propIds)
                ->where(function ($q) {
                    $q->whereNull('description')->orWhere('description', 'not like', '%[AUTO:BK#%');
                })
                ->sum('amount')
            : 0.0;

        $landPaid = !empty($pmIds)
            ? (float) PropertyMasterPayment::whereIn('property_master_id', $pmIds)->sum('amount')
            : 0.0;

        $expensesTotal = (float) Expense::where(function ($q) use ($projectId, $propIds) {
            $q->where('project_id', $projectId);
            if (!empty($propIds)) {
                $q->orWhereIn('property_id', $propIds)
                  ->orWhereHas('properties', fn ($pq) => $pq->whereIn('properties.id', $propIds));
            }
        })->sum('amount');

        $contractorPaid = (float) ContractorPayment::where(function ($q) use ($projectId, $propIds) {
            $q->where('project_id', $projectId);
            if (!empty($propIds)) {
                $q->orWhereIn('property_id', $propIds);
            }
        })->sum('amount');

        $saleIds = $sales->pluck('id')->toArray();
        $commQuery = BrokerCommission::where(function ($q) use ($propIds, $saleIds) {
            if (!empty($propIds)) {
                $q->whereIn('property_id', $propIds)
                  ->orWhereHas('booking', fn ($bq) => $bq->whereIn('property_id', $propIds));
            }
            if (!empty($saleIds)) {
                $q->orWhereIn('property_sale_id', $saleIds);
            }
        });
        $commissionPaid   = (float) (clone $commQuery)->where('payment_status', 'paid')->sum('commission_amount');
        $commissionBooked = (float) (clone $commQuery)->sum('commission_amount');

        $directStockInward = (float) StockInward::where('project_id', $projectId)
            ->whereNull('purchase_order_id')
            ->sum('total_amount');

        $realizedInflow  = $saleCash + $bookingCash + $rentalCash + $otherIncome;
        $realizedOutflow = $landPaid + $expensesTotal + $contractorPaid + $commissionPaid + $directStockInward;

        $bookedRevenue = $saleRevenue + $rentalCash + $otherIncome;
        $bookedCost    = $saleCost + $expensesTotal + $contractorPaid + $commissionBooked + $directStockInward;

        return $this->formatMetrics($realizedInflow, $realizedOutflow, $bookedRevenue, $bookedCost);
    }

    private function computePropertyMasterProfit(int $propertyMasterId): array
    {
        $pm = PropertyMaster::find($propertyMasterId);
        if (!$pm) {
            return $this->emptyMetrics();
        }

        $propIds = Property::where('property_master_id', $pm->id)->pluck('id')->toArray();

        $sales = !empty($propIds)
            ? PropertySale::with(['payments', 'properties.propertyMaster', 'property.propertyMaster'])
                ->where('sale_status', '!=', 'cancelled')
                ->where(function ($q) use ($propIds) {
                    $q->whereIn('property_id', $propIds)
                      ->orWhereHas('properties', fn ($pq) => $pq->whereIn('properties.id', $propIds));
                })
                ->get()
            : collect();

        $soldPropIds = $this->extractSoldPropertyIds($sales);
        $saleCash    = (float) $sales->sum(fn ($s) => $this->saleCollectedCash($s));
        $saleRevenue = (float) $sales->sum('sale_amount');
        $saleCost    = (float) $sales->sum(fn ($s) => $s->total_purchase_cost);

        $bookingCash = $this->bookingAdvancesForProperties($propIds, $soldPropIds);

        $rentalCash = !empty($propIds)
            ? (float) RentalPayment::where(function ($q) use ($propIds) {
                $q->whereIn('property_id', $propIds)
                  ->orWhereHas('rental', fn ($rq) => $rq->whereIn('property_id', $propIds))
                  ->orWhereHas('rental.properties', fn ($rq) => $rq->whereIn('properties.id', $propIds));
            })->sum('paid_amount')
            : 0.0;

        $otherIncome = !empty($propIds)
            ? (float) Income::whereIn('property_id', $propIds)
                ->where(function ($q) {
                    $q->whereNull('description')->orWhere('description', 'not like', '%[AUTO:BK#%');
                })
                ->sum('amount')
            : 0.0;

        $landPaid = (float) PropertyMasterPayment::where('property_master_id', $pm->id)->sum('amount');
        if ($landPaid <= 0 && (float) ($pm->paid_amount ?? 0) > 0) {
            $landPaid = (float) $pm->paid_amount;
        }

        $expensesTotal = !empty($propIds)
            ? (float) Expense::where(function ($q) use ($propIds) {
                $q->whereIn('property_id', $propIds)
                  ->orWhereHas('properties', fn ($pq) => $pq->whereIn('properties.id', $propIds));
            })->sum('amount')
            : 0.0;

        $contractorPaid = !empty($propIds)
            ? (float) ContractorPayment::whereIn('property_id', $propIds)->sum('amount')
            : 0.0;

        $saleIds = $sales->pluck('id')->toArray();
        $commQuery = BrokerCommission::where(function ($q) use ($propIds, $saleIds) {
            if (!empty($propIds)) {
                $q->whereIn('property_id', $propIds);
            }
            if (!empty($saleIds)) {
                $q->orWhereIn('property_sale_id', $saleIds);
            }
        });
        $commissionPaid   = !empty($propIds) || !empty($saleIds) ? (float) (clone $commQuery)->where('payment_status', 'paid')->sum('commission_amount') : 0.0;
        $commissionBooked = !empty($propIds) || !empty($saleIds) ? (float) (clone $commQuery)->sum('commission_amount') : 0.0;

        $realizedInflow  = $saleCash + $bookingCash + $rentalCash + $otherIncome;
        $realizedOutflow = $landPaid + $expensesTotal + $contractorPaid + $commissionPaid;

        $bookedRevenue = $saleRevenue + $rentalCash + $otherIncome;
        $bookedCost    = $saleCost + $expensesTotal + $contractorPaid + $commissionBooked;

        return $this->formatMetrics($realizedInflow, $realizedOutflow, $bookedRevenue, $bookedCost);
    }

    private function computePropertyProfit(int $propertyId): array
    {
        $property = Property::with('propertyMaster')->find($propertyId);
        if (!$property) {
            return $this->emptyMetrics();
        }

        $sales = PropertySale::with(['payments', 'properties', 'property'])
            ->where('sale_status', '!=', 'cancelled')
            ->where(function ($q) use ($propertyId) {
                $q->where('property_id', $propertyId)
                  ->orWhereHas('properties', fn ($pq) => $pq->where('properties.id', $propertyId));
            })
            ->get();

        // Apportion multi-property sales evenly across the plots in that sale
        $saleCash = 0.0;
        $saleRevenue = 0.0;
        foreach ($sales as $sale) {
            $unitCount = max(1, $sale->all_properties->count());
            $saleCash    += $this->saleCollectedCash($sale) / $unitCount;
            $saleRevenue += ((float) ($sale->sale_amount ?? 0)) / $unitCount;
        }

        $bookingCash = $sales->isEmpty()
            ? $this->bookingAdvancesForProperties([$propertyId], [])
            : 0.0;

        $rentalCash = (float) RentalPayment::where(function ($q) use ($propertyId) {
            $q->where('property_id', $propertyId)
              ->orWhereHas('rental', fn ($rq) => $rq->where('property_id', $propertyId))
              ->orWhereHas('rental.properties', fn ($rq) => $rq->where('properties.id', $propertyId));
        })->sum('paid_amount');

        $otherIncome = (float) Income::where('property_id', $propertyId)->sum('amount');

        $purchaseCost   = (float) $property->effective_purchase_cost;
        $expensesTotal  = (float) $property->total_expenses;
        $contractorPaid = (float) ContractorPayment::where('property_id', $propertyId)->sum('amount');

        $saleIds = $sales->pluck('id')->toArray();
        $commQuery = BrokerCommission::where(function ($q) use ($propertyId, $saleIds) {
            $q->where('property_id', $propertyId);
            if (!empty($saleIds)) {
                $q->orWhereIn('property_sale_id', $saleIds);
            }
        });
        $commissionPaid   = (float) (clone $commQuery)->where('payment_status', 'paid')->sum('commission_amount');
        $commissionBooked = (float) (clone $commQuery)->sum('commission_amount');

        $realizedInflow  = $saleCash + $bookingCash + $rentalCash + $otherIncome;
        $realizedOutflow = $purchaseCost + $expensesTotal + $contractorPaid + $commissionPaid;

        $bookedRevenue = $saleRevenue + $rentalCash + $otherIncome;
        $bookedCost    = $purchaseCost + $expensesTotal + $contractorPaid + $commissionBooked;

        return $this->formatMetrics($realizedInflow, $realizedOutflow, $bookedRevenue, $bookedCost);
    }

    private function saleCollectedCash(PropertySale $sale): float
    {
        $paymentsSum = $sale->relationLoaded('payments')
            ? (float) $sale->payments->sum('payment_amount')
            : (float) $sale->payments()->sum('payment_amount');

        return max($paymentsSum, (float) ($sale->booking_amount ?? 0));
    }

    private function extractSoldPropertyIds(Collection $sales): array
    {
        $ids = [];
        foreach ($sales as $sale) {
            if ($sale->property_id) {
                $ids[] = $sale->property_id;
            }
            if ($sale->relationLoaded('properties')) {
                foreach ($sale->properties as $prop) {
                    $ids[] = $prop->id;
                }
            }
        }
        return array_values(array_unique($ids));
    }

    private function bookingAdvancesForFirm(int $firmId, array $excludeSoldPropIds): float
    {
        $query = Booking::where('firm_id', $firmId)
            ->where('status', '!=', 'cancelled');

        if (!empty($excludeSoldPropIds)) {
            $query->whereNotIn('property_id', $excludeSoldPropIds)
                ->whereDoesntHave('properties', fn ($q) => $q->whereIn('properties.id', $excludeSoldPropIds));
        }

        return (float) $query->sum('booking_amount');
    }

    private function bookingAdvancesForProperties(array $propIds, array $excludeSoldPropIds): float
    {
        $eligibleIds = array_values(array_diff($propIds, $excludeSoldPropIds));
        if (empty($eligibleIds)) {
            return 0.0;
        }

        return (float) Booking::where('status', '!=', 'cancelled')
            ->where(function ($q) use ($eligibleIds) {
                $q->whereIn('property_id', $eligibleIds)
                  ->orWhereHas('properties', fn ($pq) => $pq->whereIn('properties.id', $eligibleIds));
            })
            ->sum('booking_amount');
    }

    private function formatMetrics(float $inflow, float $outflow, float $bookedRev, float $bookedCost): array
    {
        return [
            'realized_inflow'  => round($inflow, 2),
            'realized_outflow' => round($outflow, 2),
            'realized_profit'  => round($inflow - $outflow, 2),
            'booked_revenue'   => round($bookedRev, 2),
            'booked_cost'      => round($bookedCost, 2),
            'booked_profit'    => round($bookedRev - $bookedCost, 2),
        ];
    }

    private function emptyMetrics(): array
    {
        return $this->formatMetrics(0.0, 0.0, 0.0, 0.0);
    }
}
