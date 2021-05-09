<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $table = 'suppliers';

    public function invoiceBatchDetails()
    {
        return $this->hasMany(InvoiceBatchDetail::class);
    }

    public function hasInvoiceBatchDetails()
    {
        return $this->invoiceBatchDetails()->exists();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($value)
    {
        $this->name = $value;
        return $this;
    }

    public function getPurpose()
    {
        return $this->purpose;
    }

    public function setPurpose($value)
    {
        $this->purpose = $value;
        return $this;
    }

    public function getPaymentType()
    {
        return $this->payment_type;
    }

    public function setPaymentType($value)
    {
        $this->payment_type = $value;
        return $this;
    }

    public function getAccountNumber()
    {
        return $this->account_number;
    }

    public function setAccountNumber($value)
    {
        $this->account_number = $value;
        return $this;
    }

    public function getSwiftCode()
    {
        return $this->swift_code;
    }

    public function setSwiftCode($value)
    {
        $this->swift_code = $value;
        return $this;
    }
}
