<?php

namespace App\Http\Controllers;

use App\Models\CreditNote;
use Illuminate\Http\Request;

class CreditNoteController extends Controller
{
    private $availableFilters = [
        ['id' => 'supplier_name', 'text' => "Supplier"],
        ['id' => 'date', 'text' => "Date", 'type' => "Date"]
    ];

    public function index()
    {
        if (!auth()->user()->getActiveCompany()->isXeroConnected()) {
            return redirect()->route('xero_status');
        }
        $indexVariables = [
            'filterable' => $this->availableFilters,
            'sorter' => 'name',
            'sortAscending' => true,
            'baseUrl' => '/api/credit-notes?include=supplier,currency',
            'exportBaseUrl' => '/credit-notes'
        ];
        return view('credit-notes.index', ['title' => 'Credit Note', 'indexVariables' => $indexVariables]);
    }

    public function show($id)
    {
        if (!auth()->user()->getActiveCompany()->isXeroConnected()) {
            return redirect()->route('xero_status');
        }
        $creditNote = CreditNote::find($id);
        return view('credit-notes.show', ['title' => $creditNote ? $creditNote->id : "--", 'id' => $id]);
    }
}
