<?php

namespace App\Http\Controllers;

use App\Models\CreditNote;
use App\Utils\StatusList;
use BaseCode\Common\Utils\UriParserHelper;
use Illuminate\Http\Request;

class CreditNoteController extends Controller
{
    use UriParserHelper;
    private $availableFilters = [
        ['id' => 'supplier_name', 'text' => "Supplier"],
        ['id' => 'date', 'text' => "Date", 'type' => "Date"],
        ['id' => 'status', 'text' => "Status", "type" => "Select", "options" => StatusList::CREDIT_NOTE_STATUS_LIST]
    ];

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        if (!auth()->user()->getActiveCompany()->isXeroConnected()) {
            return redirect()->route('xero_status');
        }
        $filters = $this->getFilterArraysFromRequest($request, 'field');
        $indexVariables = [
            'filterable' => $this->availableFilters,
            'sorter' => 'name',
            'sortAscending' => true,
            'filters' => $filters,
            'baseUrl' => '/api/credit-notes?include=supplier,currency&paid_and_authorised=1',
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
