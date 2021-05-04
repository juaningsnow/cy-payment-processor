<?php

if (!function_exists('roundUpToTheNearest')) {
    function roundUpToTheNearest($number, $multiple)
    {
        $digits = strlen(substr(strrchr($multiple, '.'), 1));
        return round(ceil($number / $multiple) * $multiple, $digits);
    }
}
