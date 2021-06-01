<?php

namespace App\Http\Controllers;

use App\Models\Config;
use App\Models\InvoiceBatch;
use App\Utils\InvoiceBatchTextFileGenerator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Response;

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
        return view('invoice-batches.index', ['title' => 'Invoice Batch', 'indexVariables' => $indexVariables]);
    }

    public function edit($id)
    {
        $batch = InvoiceBatch::find($id);
        if ($batch->isGenerated()) {
            return redirect()->route('invoice-batches_show', $id);
        }
        return view('invoice-batches.edit', ['title' => "Invoice Batch Edit", 'id' => $id]);
    }

    public function show($id)
    {
        $invoice = InvoiceBatch::find($id);
        return view('invoice-batches.show', ['title' => $invoice ? $invoice->getBatchName() : "--", 'id' => $id]);
    }

    public function downloadTextFile($id)
    {
        $batch = InvoiceBatch::find($id);
        $batch->setGenerated(true);
        $batch->save();
        $fileText = InvoiceBatchTextFileGenerator::generate($batch, auth()->user());
        $currentDateTime = Carbon::now();
        $myName = "{$batch->batch_name}{$currentDateTime->format('dmYHis')}.txt";
        $headers = ['Content-type'=>'text/plain', 'test'=>'YoYo', 'Content-Disposition'=>sprintf('attachment; filename="%s"', $myName),'Content-Length'=>strlen($fileText)];
        
        return Response::make($fileText, 200, $headers);
    }
}
