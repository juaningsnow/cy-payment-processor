<?php

namespace App\Models;

use BaseCode\Common\Exceptions\GeneralApiException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function getId()
    {
        return $this->id;
    }

    public function getDefaultAccountNumber()
    {
        return $this->companyBanks()->where('default', true)->first()->account_number;
    }

    public function getDefaultAccountCode()
    {
        return $this->companyBanks()->where('default', true)->first()->xero_account_code;
    }

    public function hasBanks()
    {
        return $this->banks()->exists();
    }

    public function banks()
    {
        return $this->belongsToMany(Bank::class, 'company_banks')
            ->withPivot(['bank_id', 'company_id', 'account_number', 'default'])
            ->withTimestamps();
    }

    public function companyBanks()
    {
        return $this->hasMany(CompanyBank::class);
    }
}
