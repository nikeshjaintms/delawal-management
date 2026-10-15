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
