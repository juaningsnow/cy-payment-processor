<?php

if (!function_exists('to_words')) {
    function to_words($number)
    {
        $exp = explode('.', $number);
        $whole = $exp[0];
        $decimal = $exp[1] ?? null;
        $f = new NumberFormatter('en_US.utf8', NumberFormatter::SPELLOUT);
        $word = $f->format($whole);
        if ($decimal) {
            return sprintf('%s and %d/1%s', $word, $decimal, str_repeat('0', strlen($decimal)));
        }
        return $word;
    }
}
