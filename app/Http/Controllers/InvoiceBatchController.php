<?php

namespace App\Http\Controllers;

use App\Models\InvoiceBatch;

class InvoiceBatchController extends Controller
{
    private $availableFilters = [
        ['id' => 'batch_name', 'text' => 'name']
    ];

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $indexVariables = [
            'filterable' => $this->availableFilters,
            'sorter' => 'batch_name',
            'sortAscending' => true,
            'baseUrl' => '/api/invoice-batches',
            'exportBaseUrl' => '/invoice-batches'
        ];
        return view('invoices.index', ['title' => 'Invoice Batch', 'indexVariables' => $indexVariables]);
    }

    public function create()
    {
        return view('invoices.create', ['title' => "Invoice Batch Create", 'id' => null]);
    }

    public function edit($id)
    {
        $batch = InvoiceBatch::find($id);
        if ($batch->isGenerated()) {
            return redirect()->route('invoice-batches_show', $id);
        }
        return view('invoices.edit', ['title' => "Invoice Batch Edit", 'id' => $id]);
    }

    public function show($id)
    {
        $invoice = InvoiceBatch::find($id);
        return view('invoices.show', ['title' => $invoice ? $invoice->getBatchName() : "--", 'id' => $id]);
    }
}
