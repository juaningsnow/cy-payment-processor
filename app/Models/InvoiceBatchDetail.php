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

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function getInvoice()
    {
        return $this->invoice;
    }
    
    public function setInvoice(Invoice $value)
    {
        $this->invoice()->associate($value);
        $this->invoice_id = $value->id;
        return $this;
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

    public function scopeSupplierId($query, $supplierId)
    {
        return $query->whereHas('invoice', function($invoice) use ($supplierId){
            return $invoice->whereHas('supplier', function($supplier) use ($supplierId){
                return $supplier->whereId($supplierId);
            });
        });
    }
}
