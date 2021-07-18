<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    private $availableFilters = [
        ['id' => 'name', 'text' => 'Name'],
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
            'baseUrl' => '/api/companies',
            'exportBaseUrl' => '/companies'
        ];
        return view('companies.index', ['title' => 'Company', 'indexVariables' => $indexVariables]);
    }

    public function create()
    {
        if (!auth()->user()->getActiveCompany()->isXeroConnected()) {
            return redirect()->route('xero_status');
        }
        return view('companies.create', ['title' => "Company Create", 'id' => null]);
    }

    public function edit($id)
    {
        if (!auth()->user()->getActiveCompany()->isXeroConnected()) {
            return redirect()->route('xero_status');
        }
        return view('companies.edit', ['title' => "Company Edit", 'id' => $id]);
    }

    public function show($id)
    {
        if (!auth()->user()->getActiveCompany()->isXeroConnected()) {
            return redirect()->route('xero_status');
        }
        $company = Company::find($id);
        return view('companies.show', ['title' => $company ? $company->name : "--", 'id' => $id]);
    }
}
