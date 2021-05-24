<?php

namespace App\Models;

use Carbon\Carbon;
use DateTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceBatchDetail extends Model
{
    use HasFactory;

    protected $table = 'invoice_batch_details';

    protected $dates = ['date'];

    public function invoiceBatch()
    {
        return $this->belongsTo(InvoiceBatch::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function getInvoiceBatch()
    {
        return $this->invoiceBatch;
    }

    public function getInvoiceBatchId()
    {
        return $this->invoice_batch_id;
    }

    public function setInvoiceBatch(InvoiceBatch $value)
    {
        $this->invoiceBatch()->associate($value);
        $this->invoice_batch_id = $value->getId();
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

    public function scopeDateFrom($query, $date)
    {
        $date = new Carbon($date);
        return $query->where('date', '>=', $date->startOfDay()->copy()->toDateString());
    }

    public function scopeDateTo($query,$date)
    {
        $date = new Carbon($date);
        return $query->where('date', '<=', $date->endOfDay()->copy()->toDateString());
    }
}
