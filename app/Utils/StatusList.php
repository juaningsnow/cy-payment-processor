<?php

namespace App\Utils;

class StatusList
{
    const CANCELLED = 'Cancelled';
    const GENERATED = 'Generated';
    const NOT_YET_GENERATED = 'Not Yet Generated';

    const INVOICE_BATCH_STATUS_LIST = [
        self::CANCELLED, self::GENERATED, self::NOT_YET_GENERATED
    ];


    const UNPAID = 'Unpaid';
    const GENERATED_AND_PAID = 'Generated and Paid';
    const BATCHED = 'Batched';
    const PAID = 'Paid';

    const INVOICE_STATUS_LIST = [
        self::UNPAID, self::GENERATED, self::BATCHED, self::PAID
    ];
}
