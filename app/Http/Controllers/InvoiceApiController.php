<?php

namespace App\Http\Controllers;

use App\Http\Resources\InvoiceResource;
use App\Http\Resources\InvoiceResourceCollection;
use App\Models\Invoice;
use App\Models\Supplier;
use BaseCode\Common\Controllers\ResourceApiController;
use BaseCode\Common\Exceptions\GeneralApiException;
use DateTime;
use Illuminate\Http\Request;

class InvoiceApiController extends ResourceApiController
{
    const EXPORT_FILE_NAME = 'invoices.xlsx';

    protected $invoice;

    public function __construct(Invoice $invoice)
    {
        parent::__construct($invoice);
        $this->limit = 100;
    }

    public function getResource($item)
    {
        return new InvoiceResource($item);
    }

    public function getResourceCollection($items)
    {
        return new InvoiceResourceCollection($items);
    }

    public function storeMultipleInvoice(Request $request)
    {
        $invoices = $this->getInvoicesFromForm($request);
        foreach ($invoices as $invoice) {
            $invoice->save();
        };
        return $this->getResourceCollection(collect($invoices));
    }

    public function markAsPaid(Request $request)
    {
        $invoices = $this->getInvoices($request);
        foreach ($invoices as $invoice) {
            $invoice->setPaid(true);
            $invoice->save();
        }
        
        return $this->getResourceCollection(collect($invoices));
    }

    public function destroy($id)
    {
        $invoice = Invoice::find($id);
        $invoice->delete();
        return $this->getResource($invoice);
    }

    public function update($id, Request $request)
    {
        $invoice = Invoice::find($id);
        $supplier = Supplier::find($request->input('supplierId'));
        $invoice->setSupplier($supplier);
        $invoice->setDate(new DateTime($request->input('date')));
        $invoice->setInvoiceNumber($request->input('invoiceNumber'));
        $invoice->setAmount($request->input('amount'));
        $invoice->setDescription($request->input('description'));
        $invoice->save();
        return $this->getResource($invoice);
    }

    private function getInvoices(Request $request)
    {
        return array_map(function ($item) {
            $detail = Invoice::find($item['id']);
            return $detail;
        }, $request->input('selected'));
    }

    private function getInvoicesFromForm(Request $request)
    {
        return array_map(function ($item) {
            if (isset($item['id']) || $item['id'] < 0) {
                $detail = new Invoice();
            } else {
                $detail = Invoice::find($item['id']);
            }
            $supplier = Supplier::find($item['supplierId']);
            $detail->setSupplier($supplier);
            $detail->setDate(new DateTime($item['date']));
            $detail->setInvoiceNumber($item['invoiceNumber']);
            $detail->setAmount($item['amount']);
            $detail->setDescription($item['description']);
            return $detail;
        }, $request->input('invoices.data'));
    }
}
