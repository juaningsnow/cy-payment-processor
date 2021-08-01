<?php

namespace App\Models;

use App\Http\Interpreters\XeroInterpreter;
use App\Utils\HasCompanyFilter;
use App\Utils\StatusList;
use BaseCode\Common\Models\BaseModel;
use Carbon\Carbon;
use DateTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Invoice extends BaseModel implements HasMedia
{
    use HasFactory;
    use HasCompanyFilter;
    use InteractsWithMedia;

    protected $table = 'invoices';

    public $fromXero = false;

    protected static function booted()
    {
        $xeroInterpreter = resolve(XeroInterpreter::class);

        static::created(function ($model) use ($xeroInterpreter) {
            if (!$model->fromXero) {
                $xeroInterpreter->createInvoice($model);
            }
        });

        static::updated(function ($model) use ($xeroInterpreter) {
            if (!$model->fromXero) {
                $xeroInterpreter->updateInvoice($model);
                if (!$model->xero_payment_id && $model->paid) {
                    $xeroInterpreter->makePayment($model);
                }
            }
        });

        static::deleting(function ($model) use ($xeroInterpreter) {
            if (!$model->fromXero) {
                $xeroInterpreter->voidInvoice($model);
            }
        });
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function companyOwner()
    {
        return $this->belongsTo(CompanyOwner::class, 'company_owner_id');
    }

    public function invoiceBatchDetails()
    {
        return $this->hasMany(InvoiceBatchDetail::class, 'invoice_id');
    }

    public function invoiceXeroAttachments()
    {
        return $this->hasMany(InvoiceXeroAttachment::class, 'invoice_id');
    }

    public function invoicePayments()
    {
        return $this->hasMany(InvoicePayment::class, 'invoice_id');
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function getCurrency()
    {
        return $this->currency;
    }

    public function getCompanyOwner()
    {
        return $this->companyOwner;
    }

    public function setCompanyOwner(CompanyOwner $value)
    {
        $this->companyOwner()->associate($value);
        $this->company_owner_id = $value->id;
        return $this;
    }

    public function setCurrency(Currency $value)
    {
        $this->currency()->associate($value);
        $this->currency_id = $value->id;
        return $this;
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

    public function computePaidAmount()
    {
        if ($this->paid) {
            return $this->total;
        }
        return $this->invoicePayments()->sum('amount');
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

    public function setPaidBy($value)
    {
        $this->paid_by = $value;
        return $this;
    }

    public function getPaidBy()
    {
        return $this->paid_by;
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

    public function getTotal()
    {
        return $this->total;
    }

    public function setTotal($value)
    {
        $this->total = $value;
        return $this;
    }

    public function getAmountDue()
    {
        return $this->amount_due;
    }

    public function setAmountDue($value)
    {
        $this->amount_due = $value;
        return $this;
    }

    public function getAmountPaid()
    {
        return $this->amount_paid;
    }

    public function setAmountPaid($value)
    {
        $this->amount_paid = $value;
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

    public function computeStatus()
    {
        if ($this->amount_paid > 0) {
            if ($this->amount_paid < $this->total) {
                return StatusList::PARTIALLY_PAID;
            }
            if ($this->amount_paid == $this->total) {
                return StatusList::PAID;
            }
        }
        if ($this->hasInvoiceBatchDetails()) {
            if ($this->getInvoiceBatchDetail()->getInvoiceBatch()->getCancelled()) {
                return StatusList::UNPAID;
            }
            if ($this->getInvoiceBatchDetail()->getInvoiceBatch()->isGenerated() &&
                    !$this->getInvoiceBatchDetail()->getInvoiceBatch()->getCancelled()) {
                if ($this->amount_paid != $this->total) {
                    return StatusList::PARTIALLY_PAID;
                }
                return StatusList::GENERATED_AND_PAID;
            }
            return StatusList::BATCHED;
        }

        if ($this->getPaid()) {
            if($this->getPaidBy() == 'Others'){
                return "Paid by {$this->companyOwner->name}";
            }
            return "Paid by {$this->getPaidBy()}";
        }
    }

    public function getStatus()
    {
        $this->status = $this->computeStatus();
        $this->save();
        return $this->status;
    }

    public function getXeroUrl()
    {
        if (!$this->xero_invoice_id) {
            return '#';
        }
        return "https://go.xero.com/organisationlogin/default.aspx?shortcode=".
            $this->company->xero_short_code
        ."&redirecturl=/AccountsPayable/View.aspx?InvoiceID=".$this->xero_invoice_id;
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

    public function scopeOrderBySupplierName($query, $direction)
    {
        return $query->join('suppliers', 'suppliers.id', 'invoices.supplier_id')
            ->select('suppliers.name as supplier_name', 'invoices.*')
            ->orderBy('supplier_name', $direction);
    }

    public function scopeOrderByCurrencyCode($query, $direction)
    {
        return $query->join('currencies', 'currencies.id', 'invoices.currency_id')
            ->select('currencies.code as currency_code', 'invoices.*')
            ->orderBy('currency_code', $direction);
    }
}
