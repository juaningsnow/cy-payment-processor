<?php

namespace App\Models;

use App\Utils\HasCompanyFilter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory;
    use HasCompanyFilter;

    protected $fillable = [
        'xero_account_id',
        'name',
        'code',
        'company_id'
    ];
}
