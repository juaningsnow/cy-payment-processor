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

    public function invoiceBatchDetails()
    {
        return $this->hasMany(InvoiceBatchDetail::class, 'invoice_id');
    }

    public function hasOneNonCancelledInvoiceBatchDetail()
    {
        foreach ($this->invoiceBatchDetails as $detail) {
            if (!$detail->invoiceBatch->getCancelled()) {
                return true;
            }
        }
        return false;
    }

    public function hasInvoiceBatchDetails()
    {
        return $this->invoiceBatchDetails()->exists();
    }

    public function hasCancelledInvoiceBatchDetail()
    {
        if ($this->hasInvoiceBatchDetails()) {
            $count = $this->invoiceBatchDetails()->whereHas('invoiceBatch', function ($invoiceBatch) {
                return $invoiceBatch->where('cancelled', true);
            })->count();
            if ($count > 0) {
                return true;
            }
        }
        return false;
    }

    public function scopeNoInvoiceBatchDetail($query, $value = null)
    {
        return $query->whereDoesntHave('invoiceBatchDetails');
    }

    public function scopeNoInvoiceBatchDetailOrCancelled($query, $value = null)
    {
        return $query->noInvoiceBatchDetail()->orWhereDoesntHave('invoiceBatchDetails', function ($invoiceBatchDetails) {
            return $invoiceBatchDetails->whereHas('invoiceBatch', function ($invoiceBatch) {
                return $invoiceBatch->where('cancelled', false);
            });
        });
    }

    public function scopeHasInvoiceBatchDetailOrPaid($query)
    {
        return $query->whereHas('invoiceBatchDetails')->orWhere('paid', true);
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

    public function getInvoiceBatchDetail()
    {
        return $this->invoiceBatchDetails()->orderBy('id', 'desc')->first();
    }

    private function computeStatus()
    {
        if ($this->hasInvoiceBatchDetails()) {
            if ($this->getInvoiceBatchDetail()->getInvoiceBatch()->getCancelled()) {
                return "Batch Cancelled";
            }
            if ($this->getInvoiceBatchDetail()->getInvoiceBatch()->isGenerated() &&
                    !$this->getInvoiceBatchDetail()->getInvoiceBatch()->getCancelled()) {
                return "Generated and Paid";
            }
            return "Batched";
        }
        if ($this->getPaid()) {
            return "Paid";
        }
    }

    public function getStatus()
    {
        $this->status = $this->computeStatus();
        $this->save();
        return $this->status;
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

    public function scopeSupplierName($query, $value)
    {
        return $query->whereHas('supplier', function ($supplier) use ($value) {
            return $supplier->where('name', 'like', $value);
        });
    }

    public function scopeOrderBySupplierName($query, $direction = "DESC")
    {
        return $query->join('suppliers', 'suppliers.id', 'invoices.supplier_id')
            ->select('suppliers.name as supplier_name', 'invoices.*')
            ->orderBy('supplier_name', $direction);
    }
}
