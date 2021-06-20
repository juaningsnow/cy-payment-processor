<?php
namespace App\Utils;

use App\Models\Company;

trait HasCompanyFilter
{
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function getCompany()
    {
        return $this->company;
    }

    public function setCompany(Company $value)
    {
        $this->company()->associate($value);
        $this->company_id = $value->getId();
        return $this;
    }

    public function scopeFilterByCompany($query, Company $company)
    {
        return $query->where('company_id', $company->getId());
    }
}
