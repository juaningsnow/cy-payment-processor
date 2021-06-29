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
        return view('companies.create', ['title' => "Company Create", 'id' => null]);
    }

    public function edit($id)
    {
        return view('companies.edit', ['title' => "Company Edit", 'id' => $id]);
    }

    public function show($id)
    {
        $company = Company::find($id);
        return view('companies.show', ['title' => $company ? $company->name : "--", 'id' => $id]);
    }
}
