<?php

namespace App\Http\Controllers;

use App\Http\Resources\BankResource;
use App\Http\Resources\BankResourceCollection;
use App\Models\Bank;
use BaseCode\Common\Controllers\ResourceApiController;
use Illuminate\Http\Request;

class BankApiController extends ResourceApiController
{
    const EXPORT_FILE_NAME = 'banks.xlsx';

    protected $bank;

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

    public function userIndex()
    {
        $banks = Bank::where("name", 'like', '%Oversea-Chinese Banking Corporation Ltd %')
            ->orWhere('name', 'like', '%United Overseas Bank Ltd %')->get();
        return $this->getResourceCollection($banks);
    }
}
