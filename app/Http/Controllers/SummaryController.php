<?php

namespace App\Http\Controllers;

use App\Exports\SummaryExport;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SummaryController extends Controller
{
    public function create()
    {
        return view('summary.create', ['title' => "Summary Reports", 'id' => null]);
    }

    public function exportExcel($dateFrom, $dateTo)
    {
        if (!auth()->user()->getActiveCompany()->isXeroConnected()) {
            return redirect()->route('xero_status');
        }
        $dateFromCarbon = new Carbon($dateFrom);
        $dateToCarbon = new Carbon($dateTo);
        $fileName = "Summary {$dateFromCarbon->toFormattedDateString()} - {$dateToCarbon->toFormattedDateString()}.xlsx";
        return (new SummaryExport($dateFrom, $dateTo))->download($fileName, \Maatwebsite\Excel\Excel::XLSX);
    }

    public function exportCsv($dateFrom, $dateTo)
    {
        if (!auth()->user()->getActiveCompany()->isXeroConnected()) {
            return redirect()->route('xero_status');
        }
        $dateFromCarbon = new Carbon($dateFrom);
        $dateToCarbon = new Carbon($dateTo);
        $fileName = "Summary {$dateFromCarbon->toFormattedDateString()} - {$dateToCarbon->toFormattedDateString()}.csv";
        return (new SummaryExport($dateFrom, $dateTo))->download($fileName, \Maatwebsite\Excel\Excel::CSV);
    }
}
