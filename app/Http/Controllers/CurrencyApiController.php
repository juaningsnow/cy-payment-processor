<?php

namespace App\Http\Controllers;

use App\Http\Resources\CurrencyResource;
use App\Http\Resources\CurrencyResourceCollection;
use App\Models\Currency;
use App\Utils\CompanyIndexFilter;
use BaseCode\Common\Controllers\ResourceApiController;
use BaseCode\Common\Exceptions\GeneralApiException;
use Illuminate\Http\Request;

class CurrencyApiController extends ResourceApiController
{
    const EXPORT_FILE_NAME = 'currencies.xlsx';
    use CompanyIndexFilter;

    public function __construct(Currency $currency)
    {
        $this->middleware('auth:api');
        parent::__construct($currency);
    }

    public function getResource($item)
    {
        return new CurrencyResource($item);
    }

    public function getResourceCollection($items)
    {
        return new CurrencyResourceCollection($items);
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|unique:currencies,code',
        ]);
        $currency = new Currency;
        $currency->code = $request->input('code');
        $currency->description = $request->input('description');
        $currency->save();
        return $this->getResource($currency);
    }

    public function update($id, Request $request)
    {
        $request->validate([
            'code' => 'required|unique:currencies,code,' . $request->route('id') ?? null,
        ]);

        $currency = Currency::find($id);
        $currency->code = $request->input('code');
        $currency->description = $request->input('description');
        $currency->save();
        return $this->getResource($currency);
    }

    public function destroy($id)
    {
        $currency = Currency::find($id);
        if ($currency->invoices()->exists()) {
            throw new GeneralApiException("Cannot delete Currency that has Invoice entries");
        }
        $currency->delete();
        return response('success', 200);
    }
}
