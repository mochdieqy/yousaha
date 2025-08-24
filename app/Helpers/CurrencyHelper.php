<?php

namespace App\Helpers;

class CurrencyHelper
{
    /**
     * Format amount as Indonesian Rupiah
     *
     * @param float $amount
     * @param int $decimals
     * @return string
     */
    public static function formatRupiah($amount, $decimals = 0)
    {
        return 'Rp ' . number_format($amount, $decimals, ',', '.');
    }

    /**
     * Format amount as Indonesian Rupiah with currency symbol only
     *
     * @param float $amount
     * @param int $decimals
     * @return string
     */
    public static function formatRupiahOnly($amount, $decimals = 0)
    {
        return number_format($amount, $decimals, ',', '.');
    }

    /**
     * Get JavaScript formatCurrency function for Indonesian Rupiah
     *
     * @return string
     */
    public static function getJavaScriptFormatFunction()
    {
        return "
        function formatCurrency(amount) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            }).format(amount);
        }";
    }

    /**
     * Parse currency string to float
     *
     * @param string $currencyString
     * @return float
     */
    public static function parseCurrency($currencyString)
    {
        // Remove Rp, spaces, and dots, replace comma with dot
        $cleanString = str_replace(['Rp', ' ', '.'], '', $currencyString);
        $cleanString = str_replace(',', '.', $cleanString);
        
        return (float) $cleanString;
    }
}
