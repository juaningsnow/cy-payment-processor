<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    //
    private $availableFilters = [
        ['id' => 'invoice_number', 'text' => 'Number']
    ];

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $indexVariables = [
            'filterable' => $this->availableFilters,
            'sorter' => 'id',
            'sortAscending' => true,
            'baseUrl' => '/api/invoices?no_invoice_batch_detail=1&include=supplier',
            'exportBaseUrl' => '/invoices'
        ];
        return view('invoices.index', ['title' => 'Invoice', 'indexVariables' => $indexVariables]);
    }

    public function create()
    {
        return view('invoices.create', ['title' => "Invoice Create", 'id' => null]);
    }

    public function edit($id)
    {
        $invoice = Invoice::find($id);
        if ($invoice->hasInvoiceBatchDetail()) {
            if ($invoice->invoiceBatchDetail->invoiceBatch->isGenerated()) {
                return redirect()->route('invoice-batches_show', $id);
            }
        }
        return view('invoices.edit', ['title' => "Invoice Batch Edit", 'id' => $id]);
    }

    public function show($id)
    {
        $invoice = Invoice::find($id);
        return view('invoices.show', ['title' => $invoice ? $invoice->getInvoiceNumber() : "--", 'id' => $id]);
    }
}
