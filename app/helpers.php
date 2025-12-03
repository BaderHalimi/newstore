<?php

use App\Models\Setting;

if (!function_exists('setting')) {
    /**
     * Get or set a setting value
     *
     * @param string|null $key
     * @param mixed $default
     * @return mixed
     */
    function setting($key = null, $default = null)
    {
        if (is_null($key)) {
            return new Setting;
        }

        if (is_array($key)) {
            foreach ($key as $k => $v) {
                Setting::set($k, $v);
            }
            return true;
        }

        return Setting::get($key, $default);
    }
}

if (!function_exists('currency_symbol')) {
    /**
     * Get the current currency symbol
     *
     * @return string
     */
    function currency_symbol()
    {
        return setting('currency_symbol', '₪');
    }
}

if (!function_exists('format_price')) {
    /**
     * Format a price with the currency symbol
     *
     * @param float $amount
     * @return string
     */
    function format_price($amount)
    {
        return number_format($amount, 2) . ' ' . currency_symbol();
    }
}
