<?php

namespace App\Utils;

use Illuminate\Http\Request;

trait UriParserHelper
{
    protected function getFiltersFromRequest(Request $request)
    {
        $parser = new UriParser();
        $parser->setup($request);
        return array_map(function ($filterArray) {
            return new Filter($filterArray['key'], $filterArray['value'], $filterArray['operator']);
        }, $parser->whereParameters());
    }

    protected function getLimitFromRequest(Request $request)
    {
        $parser = new UriParser();
        $parser->setup($request);
        if ($parser->hasQueryParameter('limit')) {
            return $parser->queryParameter('limit');
        }
        return null;
    }
}
