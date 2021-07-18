<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    private $availableFilters = [
        ['id' => 'name', 'text' => 'Name'],
        ['id' => 'purpose_code', 'text' => "Purpose"],
        ['id' => 'payment_type', 'text' => 'Payment Type'],
        ['id' => 'account_number', 'text' => "Account Number"],
        ['id' => 'bank_name', 'text' => "Bank"]
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
            'baseUrl' => '/api/suppliers?include=purpose,bank',
            'exportBaseUrl' => '/suppliers'
        ];
        return view('suppliers.index', ['title' => 'Suppliers', 'indexVariables' => $indexVariables]);
    }

    public function create()
    {
        if (!auth()->user()->getActiveCompany()->isXeroConnected()) {
            return redirect()->route('xero_status');
        }
        return view('suppliers.create', ['title' => "Supplier Create", 'id' => null]);
    }

    public function edit($id)
    {
        if (!auth()->user()->getActiveCompany()->isXeroConnected()) {
            return redirect()->route('xero_status');
        }
        return view('suppliers.edit', ['title' => "Supplier Edit", 'id' => $id]);
    }

    public function show($id)
    {
        if (!auth()->user()->getActiveCompany()->isXeroConnected()) {
            return redirect()->route('xero_status');
        }
        $supplier = Supplier::find($id);
        return view('suppliers.show', ['title' => $supplier ? $supplier->getName() : "--", 'id' => $id]);
    }
}
