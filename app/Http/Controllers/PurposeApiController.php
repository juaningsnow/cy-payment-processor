<?php

namespace App\Http\Controllers;

use App\Http\Resources\PurposeResource;
use App\Http\Resources\PurposeResourceCollection;
use App\Models\Purpose;
use BaseCode\Common\Controllers\ResourceApiController;
use Illuminate\Http\Request;

class PurposeApiController extends ResourceApiController
{
    //
    const EXPORT_FILE_NAME = 'purposes.xlsx';

    protected $purposes;

    public function __construct(Purpose $purpose)
    {
        parent::__construct($purpose);
    }

    public function getResource($item)
    {
        return new PurposeResource($item);
    }

    public function getResourceCollection($items)
    {
        return new PurposeResourceCollection($items);
    }
}
