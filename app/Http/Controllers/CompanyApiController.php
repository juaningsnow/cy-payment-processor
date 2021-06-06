<?php

namespace App\Http\Controllers;

use App\Http\Resources\CompanyResource;
use App\Http\Resources\CompanyResourceCollection;
use App\Models\Company;
use BaseCode\Common\Controllers\ResourceApiController;
use Illuminate\Http\Request;

class CompanyApiController extends ResourceApiController
{
    const EXPORT_FILE_NAME = 'companies.xlsx';

    protected $companies;

    public function __construct(Company $company)
    {
        $this->middleware('auth:api');
        parent::__construct($company);
    }

    public function getResource($item)
    {
        return new CompanyResource($item);
    }

    public function getResourceCollection($items)
    {
        return new CompanyResourceCollection($items);
    }

    public function store(Request $request)
    {
        $company = new Company;
        $company->name = $request->input('name');
        $company->save();
        return $this->getResource($company);
    }

    public function update($id, Request $request)
    {
        $company = Company::find($id);
        $company->name = $request->input('name');
        $company->save();
        return $this->getResource($company);
    }

    public function destroy($id)
    {
        $company = Company::find($id);
        $company->delete();
        return response('success', 200);
    }
}
