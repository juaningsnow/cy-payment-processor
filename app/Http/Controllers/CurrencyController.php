<?php

namespace App\Http\Controllers;

use App\Models\Currency;
use Illuminate\Http\Request;

class CurrencyController extends Controller
{
    private $availableFilters = [
        ['id' => 'code', 'text' => 'Code'],
        ['id' => 'description', 'text' => 'Description'],
    ];
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    public function index()
    {
        if (!auth()->user()->getActiveCompany()->isXeroConnected()) {
            return redirect()->route('xero_status');
        }
        $indexVariables = [
            'filterable' => $this->availableFilters,
            'sorter' => 'name',
            'sortAscending' => true,
            'baseUrl' => '/api/currencies',
            'exportBaseUrl' => '/currencies'
        ];
        return view('currencies.index', ['title' => 'Currencies', 'indexVariables' => $indexVariables]);
    }

    public function create()
    {
        if (!auth()->user()->getActiveCompany()->isXeroConnected()) {
            return redirect()->route('xero_status');
        }
        return view('currencies.create', ['title' => "Currency Create", 'id' => null]);
    }

    public function edit($id)
    {
        if (!auth()->user()->getActiveCompany()->isXeroConnected()) {
            return redirect()->route('xero_status');
        }
        return view('currencies.edit', ['title' => "Currency Edit", 'id' => $id]);
    }

    public function show($id)
    {
        if (!auth()->user()->getActiveCompany()->isXeroConnected()) {
            return redirect()->route('xero_status');
        }
        $currency = Currency::find($id);
        return view('currencies.show', ['title' => $currency ? $currency->code : "--", 'id' => $id]);
    }
}
