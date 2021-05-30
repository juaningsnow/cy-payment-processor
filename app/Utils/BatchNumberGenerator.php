<?php

namespace App\Utils;

use App\Models\Config;

class BatchNumberGenerator
{
    public static function generate()
    {
        $config = Config::first();
        $number = $config->batch_counter;
        $paddedNumber = str_pad($number, 7, '0', STR_PAD_LEFT);
        return sprintf('%s%s', 'BN', $paddedNumber);
    }
}
