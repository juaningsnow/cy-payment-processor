<?php

namespace App\Utils;

use App\Models\Config;

class BatchNumberGenerator
{
    public static function generate()
    {
        $date = now()->format('ymd');
        $config = Config::first();
        $number = $config->batch_counter;
        $paddedNumber = str_pad($number, 3, '0', STR_PAD_LEFT);
        return sprintf('%s%s-%s', 'OCBC', $date, $paddedNumber);
    }
}
