<?php

namespace App\Models;

use App\Http\Interpreters\XeroInterpreter;
use App\Utils\HasCompanyFilter;
use BaseCode\Common\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CreditNote extends BaseModel
{
    use HasFactory;
    use HasCompanyFilter;

    public $triggerXero = false;

    protected $dates = [
        'date',
    ];

    protected static function booted()
    {
        $xeroInterpreter = resolve(XeroInterpreter::class);
        static::saving(function ($model) use ($xeroInterpreter) {
            if ($model->triggerXero) {
                // $xeroInterpreter->updateCreditNote($model);
            }
            $model->applied_amount = $model->computeAppliedAmount();
        });
    }

    public function scopePaidAndAuthorised($query)
    {
        return $query->where('status', 'AUTHORISED')->where('status', 'PAID');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function creditNoteAllocations()
    {
        return $this->hasMany(CreditNoteAllocation::class);
    }

    public function getShowUrl()
    {
        return route('credit-note_show', $this->id);
    }

    public function getXeroUrl()
    {
        return "https://go.xero.com/organisationlogin/default.aspx?shortcode="
        .$this->company->xero_short_code."&redirecturl=/AccountsPayable/ViewCreditNote.aspx?creditNoteID=".$this->xero_credit_note_id;
    }


    private function computeAppliedAmount()
    {
        return $this->creditNoteAllocations->sum('amount');
    }

    public function scopeSupplierName($query, $value)
    {
        return $query->whereHas('supplier', function ($supplier) use ($value) {
            return $supplier->where('name', 'like', $value);
        });
    }

    public function scopeOrderBySupplierName($query, $direction)
    {
        return $query->join('suppliers', 'suppliers.id', 'credit_notes.supplier_id')
            ->select('suppliers.name as supplier_name', 'credit_notes.*')
            ->orderBy('supplier_name', $direction);
    }

    public function scopeOrderByCurrencyCode($query, $direction)
    {
        return $query->join('currencies', 'currencies.id', 'credit_notes.currency_id')
            ->select('currencies.code as currency_code', 'credit_notes.*')
            ->orderBy('currency_code', $direction);
    }
}
