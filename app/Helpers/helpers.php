<?php

use App\Helpers\AccountingHelper;

if (!function_exists('formatIndianCurrency')) {
    /**
     * Format a numeric amount in the standard Indian Numbering System (Lakhs & Crores).
     * Example: 125000 -> ₹1,25,000.00
     * Example: 5740190102.78 -> ₹5,74,01,90,102.78
     *
     * @param float|int|string|null $number
     * @param int $decimals
     * @param bool $withSymbol
     * @return string
     */
    function formatIndianCurrency($number, int $decimals = 2, bool $withSymbol = true): string
    {
        return AccountingHelper::formatIndianCurrency($number, $decimals, $withSymbol);
    }
}

if (!function_exists('numberToWordsIndian')) {
    /**
     * Convert number to words in Indian numbering system (Rupees and Paise)
     *
     * @param float|int|string|null $number
     * @return string
     */
    function numberToWordsIndian($number): string
    {
        return AccountingHelper::numberToWordsIndian($number);
    }
}

if (!function_exists('getActiveFirm')) {
    /**
     * Resolve the active firm model dynamically from context, session, or user.
     *
     * @param mixed $explicitFirm
     * @return \App\Models\Firm|null
     */
    function getActiveFirm($explicitFirm = null): ?\App\Models\Firm
    {
        if ($explicitFirm instanceof \App\Models\Firm) {
            return $explicitFirm;
        }
        if (!empty($explicitFirm) && is_numeric($explicitFirm)) {
            $f = \App\Models\Firm::find($explicitFirm);
            if ($f) return $f;
        }
        if (session()->has('firm_id') && session('firm_id')) {
            $f = \App\Models\Firm::find(session('firm_id'));
            if ($f) return $f;
        }
        if (auth()->check() && auth()->user()?->firm_id) {
            $f = \App\Models\Firm::find(auth()->user()->firm_id);
            if ($f) return $f;
        }
        return \App\Models\Firm::first();
    }
}

if (!function_exists('getActiveFirmName')) {
    /**
     * Get the active firm name.
     *
     * @param mixed $explicitFirm
     * @param string $fallback
     * @return string
     */
    function getActiveFirmName($explicitFirm = null, string $fallback = 'ERP Management'): string
    {
        $f = getActiveFirm($explicitFirm);
        return $f?->firm_name ?: (session('firm_name') ?: $fallback);
    }
}

