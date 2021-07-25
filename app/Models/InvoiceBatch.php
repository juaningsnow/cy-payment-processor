<?php

namespace App\Models;

use App\Http\Interpreters\XeroInterpreter;
use App\Utils\HasCompanyFilter;
use BaseCode\Common\Models\BaseModel;
use DateTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InvoiceBatch extends BaseModel
{
    use HasFactory;
    use HasCompanyFilter;

    protected $table = 'invoice_batches';

    protected $dates = ['date'];

    protected static function booted()
    {
        $xeroInterpreter = resolve(XeroInterpreter::class);

        static::created(function ($model) {
            $config = Config::first();
            $config->batch_counter++;
            $config->save();
        });

        static::updated(function ($model) use ($xeroInterpreter) {
            if ($model->generated && !$model->xero_batch_payment_id) {
                $xeroInterpreter->makeBatchPayment($model);
            }
            if($model->cancelled){
                $xeroInterpreter->cancelBatchPayment($model);
            }
        });

        static::deleting(function ($model) use ($xeroInterpreter) {
                $xeroInterpreter->cancelBatchPayment($model);
        });
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function hasSupplier()
    {
        return $this->supplier()->exists();
    }

    public function getSupplierId()
    {
        return $this->supplier_id;
    }

    public function getSupplier()
    {
        return $this->supplier;
    }

    public function setSupplier(Supplier $value)
    {
        $this->supplier()->associate($value);
        $this->supplier_id = $value->getId();
        return $this;
    }

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
            return $detail->getInvoice()->getAmount();
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

    public function getCancelled()
    {
        return $this->cancelled;
    }

    public function setCancelled($value)
    {
        $this->cancelled = $value;
        return $this;
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

    private function computeStatus()
    {
        if ($this->getCancelled()) {
            return "Cancelled";
        }
        if ($this->isGenerated()) {
            return "Generated";
        }
        return "Not Yet Generated";
    }

    public function getStatus()
    {
        $this->status = $this->computeStatus();
        $this->save();
        return $this->status;
    }
}
