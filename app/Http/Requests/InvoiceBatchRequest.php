<?php

namespace App\Http\Requests;

use App\Models\Invoice;
use App\Models\InvoiceBatchDetail;
use BaseCode\Common\Exceptions\GeneralApiException;
use DateTime;
use Illuminate\Foundation\Http\FormRequest;

class InvoiceBatchRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
        ];
    }

    public function getDate()
    {
        return new DateTime($this->input('date'));
    }

    public function getInvoiceBatchDetails()
    {
        $details = array_map(function ($item) {
            if (isset($item['id']) || $item['id'] < 0) {
                $detail = new InvoiceBatchDetail();
            } else {
                $detail = InvoiceBatchDetail::find($item['id']);
            }
            $invoice = Invoice::find($item['invoiceId']);
            $amount = 0;
            if ($item['amount'] > 0) {
                $amount = $item['amount'];
            } else {
                if ($invoice->amount_due > 0) {
                    $amount = $invoice->amount_due;
                } else {
                    $amount = $invoice->total;
                }
            }
            $detail->setInvoice($invoice);
            $detail->amount = $amount;
            return $detail;
        }, $this->input('invoiceBatchDetails.data'));
        $this->checkIfAllCurrenciesAreSgd($details);
        return $details;
    }

    private function checkIfAllCurrenciesAreSgd(array $details)
    {
        foreach ($details as $detail) {
            if ($detail->invoice->currency->code != 'SGD') {
                throw new GeneralApiException("Cannot add an Invoice that is not in SGD currency");
            }
        }
    }

    public function addInvoiceBatchDetails()
    {
        return array_map(function ($item) {
            if (isset($item['id']) || $item['id'] < 0) {
                $detail = new InvoiceBatchDetail();
            } else {
                $detail = InvoiceBatchDetail::find($item['id']);
            }
            $invoice = Invoice::find($item['id']);
            if ($invoice->amount_due > 0) {
                $amount = $invoice->amount_due;
            } else {
                $amount = $invoice->total;
            }
            $detail->setInvoice($invoice);
            $detail->amount = $amount;
            return $detail;
        }, $this->input('selected'));
    }
}
