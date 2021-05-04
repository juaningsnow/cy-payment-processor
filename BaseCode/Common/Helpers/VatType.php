<?php

namespace BaseCode\Common\Helpers;

class VatType
{
    const VAT_INCLUSIVE = "VAT Inclusive";
    const VAT_EXEMPT = "VAT Exempt";
    const ZERO_RATED  = "Zero Rated";

    const OPTIONS = [
        self::VAT_INCLUSIVE,
        self::VAT_EXEMPT,
        self::ZERO_RATED
    ];
}
