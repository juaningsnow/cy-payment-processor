<?php

namespace App\Http\Controllers;

use App\Http\Resources\BankResource;
use App\Http\Resources\BankResourceCollection;
use App\Models\Bank;
use BaseCode\Common\Controllers\ResourceApiController;

class BankApiController extends ResourceApiController
{
    const EXPORT_FILE_NAME = 'banks.xlsx';

    protected $banks;

    public function __construct(Bank $bank)
    {
        parent::__construct($bank);
    }

    public function getResource($item)
    {
        return new BankResource($item);
    }

    public function getResourceCollection($items)
    {
        return new BankResourceCollection($items);
    }
}
