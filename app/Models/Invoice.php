<?php

namespace App\Models;

use Carbon\Carbon;
use DateTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $table = 'invoices';

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function invoiceBatchDetail()
    {
        return $this->hasOne(InvoiceBatchDetail::class, 'invoice_id');
    }

    public function hasInvoiceBatchDetail()
    {
        return $this->invoiceBatchDetail()->exists();
    }

    public function scopeNoInvoiceBatchDetail($query, $value)
    {
        return $query->whereDoesntHave('invoiceBatchDetail');
    }

    public function scopeHasInvoiceBatchDetailOrPaid($query)
    {
        return $query->whereHas('invoicebatchDetail')->orWhere('paid', true);
    }

    public function scopePaid($query, $value)
    {
        return $query->where('paid', $value);
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getPaid()
    {
        return (boolean)$this->paid;
    }

    public function setPaid($value)
    {
        $this->paid = $value;
        return $this;
    }

    public function setDescription($value)
    {
        $this->description = $value;
        return $this;
    }
    
    public function getSupplier()
    {
        return $this->supplier;
    }

    public function getSupplierId()
    {
        return $this->supplier_id;
    }

    public function setSupplier(Supplier $value)
    {
        $this->supplier()->associate($value);
        $this->supplier_id = $value->getId();
        return $this;
    }

    public function getDate()
    {
        return $this->date;
    }

    public function setDate(DateTime $value)
    {
        $this->date = $value;
        return $this;
    }

    public function getInvoiceNumber()
    {
        return $this->invoice_number;
    }

    public function setInvoiceNumber($value)
    {
        $this->invoice_number = $value;
        return $this;
    }

    public function getAmount()
    {
        return (float) $this->amount;
    }

    public function setAmount($value)
    {
        $this->amount = $value;
        return $this;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getStatus()
    {
        if ($this->hasInvoiceBatchDetail()) {
            if ($this->invoiceBatchDetail->getInvoiceBatch()->isGenerated()) {
                return "Generated and Paid";
            } else {
                return "Batched";
            }
        }
        if ($this->getPaid()) {
            return "Paid";
        }
    }

    public function scopeDateFrom($query, $date)
    {
        $date = new Carbon($date);
        return $query->where('date', '>=', $date->startOfDay()->copy()->toDateString());
    }

    public function scopeDateTo($query, $date)
    {
        $date = new Carbon($date);
        return $query->where('date', '<=', $date->endOfDay()->copy()->toDateString());
    }
}
