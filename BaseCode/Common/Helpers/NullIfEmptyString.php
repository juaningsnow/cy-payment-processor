<?php

if (!function_exists('null_if_empty_string')) {
    function null_if_empty_string($value)
    {
        return is_string($value) && $value === '' ? null : $value;
    }
}
