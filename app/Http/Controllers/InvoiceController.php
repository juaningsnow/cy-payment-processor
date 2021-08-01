<?php

namespace App\Http\Controllers;

use App\Http\Interpreters\XeroInterpreter;
use App\Models\Invoice;
use App\Models\InvoiceXeroAttachment;
use App\Utils\StatusList;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    //
    private $availableFilters = [
        ['id' => 'invoice_number', 'text' => 'Number'],
        ['id' => 'supplier_name', 'text' => "Supplier"],
        ['id' => 'date', 'text' => "Date", 'type' => "Date"]
    ];

    private $availableFilters2 = [
        ['id' => 'invoice_number', 'text' => 'Number'],
        ['id' => 'supplier_name', 'text' => "Supplier"],
        ['id' => 'date', 'text' => "Date", 'type' => "Date"],
        ['id' => 'status', 'text' => "Status", 'type' => "Select", 'options' => StatusList::INVOICE_STATUS_LIST]
    ];

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        if (!auth()->user()->getActiveCompany()->isXeroConnected()) {
            return redirect()->route('xero_status');
        }
        $indexVariables = [
            'filterable' => $this->availableFilters,
            'sorter' => 'id',
            'sortAscending' => true,
            'baseUrl' => '/api/invoices?no_invoice_batch_detail_or_cancelled=1&paid=0&include=supplier,currency',
            'exportBaseUrl' => '/invoices',
            'companyId' => auth()->user()->getActiveCompany()->id,
        ];
        return view('invoices.index', ['title' => 'Invoice', 'indexVariables' => $indexVariables]);
    }

    public function index2()
    {
        if (!auth()->user()->getActiveCompany()->isXeroConnected()) {
            return redirect()->route('xero_status');
        }
        $indexVariables = [
            'filterable' => $this->availableFilters2,
            'sorter' => 'id',
            'sortAscending' => true,
            'baseUrl' => '/api/invoices?has_invoice_batch_detail_or_paid=1&include=supplier,currency',
            'exportBaseUrl' => '/invoices'
        ];
        return view('invoices.index2', ['title' => 'Invoice', 'indexVariables' => $indexVariables]);
    }

    public function create()
    {
        if (!auth()->user()->getActiveCompany()->isXeroConnected()) {
            return redirect()->route('xero_status');
        }
        return view('invoices.create', ['title' => "Invoice Create", 'id' => null]);
    }

    public function edit($id)
    {
        if (!auth()->user()->getActiveCompany()->isXeroConnected()) {
            return redirect()->route('xero_status');
        }
        $invoice = Invoice::find($id);
        if ($invoice->hasInvoiceBatchDetails()) {
            if ($invoice->getInvoiceBatchDetail()->invoiceBatch->isGenerated()) {
                return redirect()->route('invoice_show', $id);
            }
        }
        if ($invoice->getPaid()) {
            return redirect()->route('invoice_show', $id);
        }
        return view('invoices.edit', ['title' => "Invoice Edit", 'id' => $id]);
    }

    public function show($id)
    {
        if (!auth()->user()->getActiveCompany()->isXeroConnected()) {
            return redirect()->route('xero_status');
        }
        $invoice = Invoice::find($id);
        return view('invoices.show', ['title' => $invoice ? $invoice->getInvoiceNumber() : "--", 'id' => $id]);
    }

    public function downloadXeroAttachment($id)
    {
        if (!auth()->user()->getActiveCompany()->isXeroConnected()) {
            return redirect()->route('xero_status');
        }
        $attachment = InvoiceXeroAttachment::find($id);
        $xeroInterpreter = resolve(XeroInterpreter::class);
        $fileResponse = $xeroInterpreter->downloadAttachment($attachment);
        return Response::make($fileResponse->body(), 200, $fileResponse->getHeaders());
    }
}
