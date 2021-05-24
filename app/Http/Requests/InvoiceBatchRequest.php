<?php

namespace App\Http\Requests;

use App\Models\InvoiceBatchDetail;
use App\Models\Supplier;
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
            'batchName' => 'required|unique:invoice_batches,batch_name,' . $this->route('id') ?? null
        ];
    }

    public function getBatchName()
    {
        return $this->input('batchName');
    }

    public function getDate()
    {
        return new DateTime($this->input('date'));
    }

    public function getInvoiceBatchDetails()
    {
        $this->checkIfInvoiceNumberIsDuplicate($this->input('invoiceBatchDetails.data'));
        return array_map(function ($item) {
            if (isset($item['id']) || $item['id'] < 0) {
                $detail = new InvoiceBatchDetail();
                $this->checkIfInvoiceExists($item['invoiceNumber']);
            } else {
                $detail = InvoiceBatchDetail::find($item['id']);
            }
            $supplier = Supplier::find($item['supplierId']);
            $detail->setSupplier($supplier);
            $detail->setDate(new DateTime($item['date']));
            $detail->setInvoiceNumber($item['invoiceNumber']);
            $detail->setAmount($item['amount']);
            return $detail;
        }, $this->input('invoiceBatchDetails.data'));
    }

    private function checkIfInvoiceExists($invoiceNumber)
    {
        $count = InvoiceBatchDetail::where('invoice_number', $invoiceNumber)->count();
        if ($count > 0) {
            throw new GeneralApiException("Invoice Number already exists in our records");
        }
    }

    private function checkIfInvoiceNumberIsDuplicate(array $details)
    {
        $invoiceNumberArray = array_map(function ($data) {
            return $data['invoiceNumber'];
        }, $details);
        $entryHasDuplicates = count($invoiceNumberArray) > count(array_unique($invoiceNumberArray));
        if ($entryHasDuplicates) {
            throw new GeneralApiException("Duplicate Invoice Number Entries");
        }
    }
}
