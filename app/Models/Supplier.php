<?php

namespace App\Models;

use App\Http\Interpreters\XeroInterpreter;
use App\Utils\HasCompanyFilter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;
    use HasCompanyFilter;
    
    protected static function booted()
    {
        $xeroInterpreter = resolve(XeroInterpreter::class);
        static::created(function ($model) use ($xeroInterpreter) {
            $xeroInterpreter->createContact($model);
        });
    }

    protected $table = 'suppliers';

    protected $fillable = [
        'name',
        'payment_type',
        'email',
        'xero_contact_id',
        'bank_id',
        'purpose_id',
        'account_number'
    ];

    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }

    public function purpose()
    {
        return $this->belongsTo(Purpose::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }


    public function getBank()
    {
        return $this->bank;
    }

    public function setBank(Bank $value)
    {
        $this->bank()->associate($value);
        $this->bank_id = $value->id;
        return $this;
    }

    public function hasInvoices()
    {
        return $this->invoices()->exists();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($value)
    {
        $this->email = $value;
        return $this;
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

    public function setPurpose(Purpose $value)
    {
        $this->purpose()->associate($value);
        $this->purpose_id = $value->id;
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

    public function scopePurposeCode($query, $value)
    {
        return $query->whereHas('purpose', function ($purpose) use ($value) {
            return $purpose->where('name', 'like', $value);
        });
    }

    public function scopeOrderByPurposeCode($query, $direction = 'DESC')
    {
        return $query->join('purposes', 'purposes.id', 'suppliers.purpose_id')
            ->select('purposes.name as purpose_name', 'suppliers.*')
            ->orderBy('purpose_name', $direction);
    }

    public function scopeBankName($query, $value)
    {
        return $query->whereHas('bank', function ($bank) use ($value) {
            return $bank->where('name', 'like', $value);
        });
    }

    public function scopeOrderByBankName($query, $direction = 'DESC')
    {
        return $query->join('banks', 'banks.id', 'suppliers.bank_id')
            ->select('banks.name as bank_name', 'suppliers.*')
            ->orderBy('bank_name', $direction);
    }
}
