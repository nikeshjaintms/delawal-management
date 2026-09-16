<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Property;
use App\Models\Firm;
use App\Models\PropertyMaster;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class NaturalSortPlotTest extends TestCase
{
    use DatabaseTransactions;

    public function test_properties_and_plots_are_sorted_in_natural_line_order()
    {
        $firm = Firm::create([
            'firm_name' => 'Natural Sort Firm',
            'email' => 'naturalsort@example.com',
            'status' => 'active'
        ]);
        $pm = PropertyMaster::create([
            'firm_id' => $firm->id,
            'property_name' => 'Galaxy Enclave',
            'property_code' => 'PROP-GALA-001',
            'status' => 'active',
        ]);

        $rawNames = [
            'House No A1',
            'House No A10',
            'House No A11',
            'House No A12',
            'House No A19',
            'House No A2',
            'House No A20',
            'House No A3',
            'House No A9',
        ];

        $props = collect();
        foreach ($rawNames as $name) {
            $props->push(Property::create([
                'firm_id' => $firm->id,
                'property_master_id' => $pm->id,
                'property_name' => $name,
                'property_code' => 'P-' . $name,
                'status' => 'available',
                'price' => 3000000.00,
            ]));
        }

        $sorted = Property::naturalSort($props);
        $sortedNames = $sorted->pluck('property_name')->toArray();

        $expectedOrder = [
            'House No A1',
            'House No A2',
            'House No A3',
            'House No A9',
            'House No A10',
            'House No A11',
            'House No A12',
            'House No A19',
            'House No A20',
        ];

        $this->assertEquals($expectedOrder, $sortedNames);
    }
}
