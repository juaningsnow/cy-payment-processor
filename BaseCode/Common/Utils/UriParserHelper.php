<?php

namespace BaseCode\Common\Utils;

use Illuminate\Http\Request;

trait UriParserHelper
{
    protected function getFieldsFromRequest(Request $request)
    {
        $parser = new UriParser();
        $parser->setup($request);
        if ($parser->hasQueryParameter('fields')) {
            $parameter = $parser->queryParameter('fields');
            $fields = explode(',', $parameter['value']);
            return $fields;
        }
        return [];
    }

    protected function getIncludesFromRequest(Request $request)
    {
        $parser = new UriParser();
        $parser->setup($request);

        if ($parser->hasQueryParameter('include')) {
            $parameter = $parser->queryParameter('include');
            $fields = explode(',', $parameter['value']);
            return $fields;
        }

        return [];
    }

    protected function getFiltersFromRequest(Request $request)
    {
        $parser = new UriParser();
        $parser->setup($request);
        return array_map(function ($filterArray) {
            return new Filter($filterArray['key'], $filterArray['value'], $filterArray['operator']);
        }, $parser->whereParameters());
    }

    protected function getFilterArraysFromRequest(Request $request, ?string $keyName = null, ?string $valueName = null, ?string $operatorName = null)
    {
        $params = array_filter([$keyName, $valueName, $operatorName], function ($param) {
            return $param != null;
        });

        return array_map(function ($filterObject) use ($params) {
            return $filterObject->toArray(...$params);
        }, $this->getFiltersFromRequest($request));
    }

    protected function getSortersFromRequest(Request $request)
    {
        $parser = new UriParser();
        $parser->setup($request);

        if ($parser->hasQueryParameter('sort')) {
            $parameter = $parser->queryParameter('sort');
            $fields = explode(',', $parameter['value']);

            return array_map(function ($field) {
                $direction = 'ASC';
                if ($field[0] == '-') {
                    $field = substr($field, 1);
                    $direction = 'DESC';
                }
                return new Sorter($field, $direction);
            }, $fields);
            // TODO what is the sorter for the last?
            // $sorters[$field] = $direction;
            // $sorters['id'] = $direction;
        }

        return [];
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
