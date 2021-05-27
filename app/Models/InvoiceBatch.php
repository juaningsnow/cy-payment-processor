<?php

namespace App\Models;

use BaseCode\Common\Models\BaseModel;
use DateTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InvoiceBatch extends BaseModel
{
    use HasFactory;

    protected $table = 'invoice_batches';

    protected $dates = ['date'];

    protected $invoiceBatchDetailsToSet = null;

    public function invoiceBatchDetails()
    {
        return $this->hasMany(InvoiceBatchDetail::class);
    }

    public function getInvoiceBatchDetails()
    {
        if ($this->invoiceBatchDetailsToSet !== null) {
            return collect($this->invoiceBatchDetailsToSet);
        }
        return $this->invoiceBatchDetails;
    }

    public function setInvoiceBatchDetails(array $value)
    {
        $this->invoiceBatchDetailsToSet = $value;
        $this->setTotal($this->computeTotal());
        return $this;
    }

    private function computeTotal()
    {
        return $this->getInvoiceBatchDetails()->sum(function ($detail) {
            return $detail->getAmount();
        });
    }

    public function getId()
    {
        return $this->id;
    }

    public function getBatchName()
    {
        return $this->batch_name;
    }

    public function setBatchName($value)
    {
        $this->batch_name = $value;
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

    public function getTotal()
    {
        return (float) $this->total;
    }

    public function setTotal($value)
    {
        $this->total = $value;
        return $this;
    }

    public function isGenerated()
    {
        return $this->generated;
    }

    public function setGenerated($value)
    {
        $this->generated = $value;
        return $this;
    }
}
