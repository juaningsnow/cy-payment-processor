<?php
namespace App\Utils;

use Illuminate\Http\Request;

trait CompanyIndexFilter
{
    public function index(Request $request)
    {
        if (!$request->user()->isAdmin()) {
            $this->query = $this->query->filterByCompany($request->user()->company);
        }
        $this->filter($request)->sort($request)->include($request);
        return $this->getResourceCollection($this->query->paginate($this->getLimit($request)));
    }
}
