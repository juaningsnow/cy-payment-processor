<?php

namespace App\Http\Controllers;

use App\Models\Config;
use App\Models\InvoiceBatch;
use App\Utils\InvoiceBatchTextFileGenerator;
use App\Utils\StatusList;
use BaseCode\Common\Exceptions\GeneralApiException;
use BaseCode\Common\Utils\UriParserHelper;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class InvoiceBatchController extends Controller
{
    use UriParserHelper;

    private $availableFilters = [
        ['id' => 'batch_name', 'text' => 'Batch #'],
        ['id' => 'date', 'text' => "Date", 'type' => "Date"],
        ['id' => 'status', 'text' => "Status", "type" => "Select", "options" => StatusList::INVOICE_BATCH_STATUS_LIST]
    ];

    private $invoiceFilters = [
        ['id' => 'invoice_number', 'text' => 'Number']
    ];

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function create()
    {
        if (!auth()->user()->getActiveCompany()->isXeroConnected()) {
            return redirect()->route('xero_status');
        }
        return view('invoice-batches.create', ['title' => "Batch Create", 'id' => null]);
    }

    public function index(Request $request)
    {
        if (!auth()->user()->getActiveCompany()->isXeroConnected()) {
            return redirect()->route('xero_status');
        }
        $filters = $this->getFilterArraysFromRequest($request, 'field');
        $indexVariables = [
            'filterable' => $this->availableFilters,
            'sorter' => 'batch_name',
            'sortAscending' => true,
            'filters' => $filters,
            'baseUrl' => '/api/invoice-batches',
            'exportBaseUrl' => '/invoice-batches'
        ];
        return view('invoice-batches.index', ['title' => 'Invoice Batch', 'indexVariables' => $indexVariables]);
    }

    public function edit($id)
    {
        if (!auth()->user()->getActiveCompany()->isXeroConnected()) {
            return redirect()->route('xero_status');
        }
        $batch = InvoiceBatch::find($id);
        if ($batch->isGenerated()) {
            return redirect()->route('invoice-batches_show', $id);
        }
        return view('invoice-batches.edit', ['title' => "Invoice Batch Edit", 'id' => $id]);
    }

    public function show($id)
    {
        if (!auth()->user()->getActiveCompany()->isXeroConnected()) {
            return redirect()->route('xero_status');
        }
        $indexVariables = [
            'filterable' => $this->invoiceFilters,
            'sorter' => 'id',
            'sortAscending' => true,
            'baseUrl' => '/api/invoices?no_invoice_batch_detail_or_cancelled=1&paid=0&include=supplier',
            'exportBaseUrl' => '/invoices'
        ];
        $invoice = InvoiceBatch::find($id);
        return view('invoice-batches.show', ['title' => $invoice ? $invoice->getBatchName() : "--", 'id' => $id, 'indexVariables' => $indexVariables]);
    }

    public function downloadTextFile($id)
    {
        if (!auth()->user()->getActiveCompany()->isXeroConnected()) {
            return redirect()->route('xero_status');
        }
       
        $batch = InvoiceBatch::find($id);
        $fileText = InvoiceBatchTextFileGenerator::generate($batch, auth()->user());
        $currentDateTime = Carbon::now();
        $myName = "{$batch->batch_name}{$currentDateTime->format('dmYHis')}.txt";
        $headers = [
            'Content-type'=>'text/plain',
            'Content-Disposition'=>sprintf('attachment; filename="%s"', $myName),
            'Content-Transfer-Encoding' => 'binary',
            'Content-Description' => 'File Transfer',
            'Content-Transfer-Encoding' => 'binary',
            'Cache-Control' => 'must-revalidate',
        ];
        
        return Response::make($fileText, 200, $headers);
    }

    public function downloadTextFileGiro($id, $date)
    {
        if (!auth()->user()->getActiveCompany()->isXeroConnected()) {
            return redirect()->route('xero_status');
        }
       
        $batch = InvoiceBatch::find($id);
        $fileText = InvoiceBatchTextFileGenerator::generate($batch, auth()->user(), true, $date);
        $currentDateTime = Carbon::now();
        $myName = "{$batch->batch_name}{$currentDateTime->format('dmYHis')}.txt";
        $headers = [
            'Content-type'=>'text/plain',
            'Content-Disposition'=>sprintf('attachment; filename="%s"', $myName),
            'Content-Transfer-Encoding' => 'binary',
            'Content-Description' => 'File Transfer',
            'Content-Transfer-Encoding' => 'binary',
            'Cache-Control' => 'must-revalidate',
        ];
        
        return Response::make($fileText, 200, $headers);
    }
}
