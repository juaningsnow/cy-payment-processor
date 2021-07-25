<?php
namespace App\Utils;

use App\Models\Invoice;
use App\Models\InvoiceBatch;
use Illuminate\Http\Request;

trait CompanyIndexFilter
{
    public function index(Request $request)
    {
        $this->query = $this->query->filterByCompany($request->user()->getActiveCompany());
        $this->filter($request)->sort($request)->include($request);
        return $this->getResourceCollection($this->query->paginate($this->getLimit($request)));
    }
}
