<?php

use Illuminate\Support\Str;

if (!function_exists('generate_unique_slug')) {
    /**
     * Generate slug value
     *
     * @param string $value
     * @param integer $entropy the number of random character
     * @return string
     */
    function generate_unique_slug(string $value, int $entropy = 8): string
    {
        return Str::slug(Str::lower($value)) . '-' . Str::random($entropy);
    }
}
