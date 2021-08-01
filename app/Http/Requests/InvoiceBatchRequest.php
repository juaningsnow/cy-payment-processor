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
            $detail->setInvoice($invoice);
            $detail->amount = $item['amount'];
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
            $detail->setInvoice($invoice);
            return $detail;
        }, $this->input('selected'));
    }
}
