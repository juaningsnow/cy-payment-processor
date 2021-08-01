<?php

namespace App\Models;

use BaseCode\Common\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Company extends BaseModel
{
    use HasFactory;

    protected $fillable = ['name'];

    protected $companyOwnersToSet = null;

    public function getId()
    {
        return $this->id;
    }

    public function getDefaultAccountNumber()
    {
        return $this->companyBanks()->where('default', true)->first()->account_number;
    }

    public function cashAccount()
    {
        return $this->belongsTo(Account::class, 'cash_account_id');
    }

    public function bankAccount()
    {
        return $this->belongsTo(Account::class, 'bank_account_id');
    }

    public function companyOwners()
    {
        return $this->hasMany(CompanyOwner::class, 'company_id');
    }

    public function currencies()
    {
        return $this->hasMany(Currency::class, 'company_id');
    }

    public function getCompanyOwners()
    {
        if ($this->companyOwnersToSet !== null) {
            return collect($this->companyOwnersToSet);
        }
        return $this->companyOwners;
    }

    public function setCompanyOwners(array $value)
    {
        $this->companyOwnersToSet = $value;
        return $this;
    }

    public function getCashAccount()
    {
        return $this->cashAccount;
    }

    public function setCashAccount(Account $value)
    {
        $this->cashAccount()->associate($value);
        $this->cash_account_id = $value->id;
        return $this;
    }

    public function getBankAccount()
    {
        return $this->bankAccount;
    }

    public function setBankAccount(Account $value)
    {
        $this->bankAccount()->associate($value);
        $this->bank_account_id = $value->id;
        return $this;
    }

    public function getDefaultAccountCode()
    {
        return $this->getDefaultBank()->account->code;
    }

    public function getDefaultBank()
    {
        return $this->companyBanks()->where('default', true)->first();
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_companies', 'company_id', 'user_id');
    }

    public function hasUsers()
    {
        return $this->users()->exists();
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

    public function isXeroConnected()
    {
        return $this->xero_tenant_id ? true : false;
    }
}
