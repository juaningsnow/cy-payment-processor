<?php

namespace App\Http\Interpreters\Traits;

use DateTime;

trait DateParser
{
    public function parseDate($date)
    {
        date_default_timezone_set('UTC');
        $insideParenthesis = $this->getStringBetween($this->removeSlash($date), '(', ')');
        $integer = (int)Date($insideParenthesis);
        $convertedInt = $integer/1000;
        return new DateTime("@$convertedInt");
    }

    private function removeSlash($date)
    {
        return str_replace('/', '', $date);
    }

    private function getStringBetween($string, $start, $end)
    {
        $string = ' ' . $string;
        $ini = strpos($string, $start);
        if ($ini == 0) {
            return '';
        }
        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;
        return substr($string, $ini, $len);
    }
}
