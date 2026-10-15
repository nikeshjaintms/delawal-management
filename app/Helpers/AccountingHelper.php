<?php

namespace App\Helpers;

class AccountingHelper
{
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
    public static function formatIndianCurrency($number, int $decimals = 2, bool $withSymbol = true): string
    {
        if ($number === null || $number === '') {
            return ($withSymbol ? '₹' : '') . '0.00';
        }

        $isNegative = (float) $number < 0;
        $absNumber = abs((float) $number);

        // Format to desired decimal places
        $formattedDecimals = number_format($absNumber, $decimals, '.', '');
        $parts = explode('.', $formattedDecimals);
        $integerPart = $parts[0];
        $decimalPart = isset($parts[1]) ? '.' . $parts[1] : '';

        if (strlen($integerPart) > 3) {
            $lastThree = substr($integerPart, -3);
            $remaining = substr($integerPart, 0, -3);
            $remaining = preg_replace("/\B(?=(\d{2})+(?!\d))/", ",", $remaining);
            $formatted = $remaining . ',' . $lastThree;
        } else {
            $formatted = $integerPart;
        }

        $prefix = $isNegative ? '− ' : '';
        $symbol = $withSymbol ? '₹' : '';

        return $prefix . $symbol . $formatted . $decimalPart;
    }

    /**
     * Convert number to words in Indian numbering system (Rupees and Paise)
     *
     * @param float|int|string|null $number
     * @return string
     */
    public static function numberToWordsIndian($number): string
    {
        if ($number === null || $number === '') {
            return 'Rupees Zero Only';
        }

        $number = round((float) $number, 2);
        $isNegative = $number < 0;
        $absNumber = abs($number);

        $integer = (int) floor($absNumber);
        $fraction = (int) round(($absNumber - $integer) * 100);

        $words = self::convertNumberToWords($integer);

        $output = ($isNegative ? 'Negative ' : '') . 'Rupees ' . ($words ?: 'Zero');
        if ($fraction > 0) {
            $output .= ' and ' . self::convertNumberToWords($fraction) . ' Paise';
        }
        $output .= ' Only';

        return $output;
    }

    private static function convertNumberToWords(int $num): string
    {
        if ($num === 0) {
            return '';
        }

        $ones = [
            0 => '', 1 => 'One', 2 => 'Two', 3 => 'Three', 4 => 'Four',
            5 => 'Five', 6 => 'Six', 7 => 'Seven', 8 => 'Eight', 9 => 'Nine',
            10 => 'Ten', 11 => 'Eleven', 12 => 'Twelve', 13 => 'Thirteen', 14 => 'Fourteen',
            15 => 'Fifteen', 16 => 'Sixteen', 17 => 'Seventeen', 18 => 'Eighteen', 19 => 'Nineteen'
        ];

        $tens = [
            2 => 'Twenty', 3 => 'Thirty', 4 => 'Forty', 5 => 'Fifty',
            6 => 'Sixty', 7 => 'Seventy', 8 => 'Eighty', 9 => 'Ninety'
        ];

        if ($num < 20) {
            return $ones[$num];
        }

        if ($num < 100) {
            $t = (int) floor($num / 10);
            $r = $num % 10;
            return trim($tens[$t] . ($r ? ' ' . $ones[$r] : ''));
        }

        if ($num < 1000) {
            $h = (int) floor($num / 100);
            $r = $num % 100;
            return trim($ones[$h] . ' Hundred ' . self::convertNumberToWords($r));
        }

        if ($num < 100000) { // Thousands (up to 99,999)
            $th = (int) floor($num / 1000);
            $r = $num % 1000;
            return trim(self::convertNumberToWords($th) . ' Thousand ' . self::convertNumberToWords($r));
        }

        if ($num < 10000000) { // Lakhs (up to 99,99,999)
            $lk = (int) floor($num / 100000);
            $r = $num % 100000;
            return trim(self::convertNumberToWords($lk) . ' Lakh ' . self::convertNumberToWords($r));
        }

        // Crores (1 Crore and above)
        $cr = (int) floor($num / 10000000);
        $r = $num % 10000000;
        return trim(self::convertNumberToWords($cr) . ' Crore ' . self::convertNumberToWords($r));
    }
}
