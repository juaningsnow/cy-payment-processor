<?php

namespace App\Http\Controllers;

use App\Http\Resources\AccountResource;
use App\Http\Resources\AccountResourceCollection;
use App\Models\Account;
use App\Utils\CompanyIndexFilter;
use BaseCode\Common\Controllers\ResourceApiController;
use Illuminate\Http\Request;

class AccountApiController extends ResourceApiController
{
    //
    use CompanyIndexFilter;
    
    const EXPORT_FILE_NAME = 'accounts.xlsx';

    protected $accounts;

    public function __construct(Account $account)
    {
        parent::__construct($account);
    }

    public function getResource($item)
    {
        return new AccountResource($item);
    }

    public function getResourceCollection($items)
    {
        return new AccountResourceCollection($items);
    }
}
