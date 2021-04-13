<?php

namespace App\Utils;

use Illuminate\Http\Request;

trait FilterHelper
{
    use UriParserHelper;

    protected function filterQuery($key, $operator, $value)
    {
        $scopeMethod = \Str::camel('scope' . ucfirst($key));
        
        if (method_exists($this->query->getModel(), $scopeMethod)) {
            $scope = \Str::camel($key);
            $this->query->$scope($value);
            return $this;
        }

        $key = \Str::snake($key);

        if (strtoupper($value) == 'NULL') {
            if ($operator == '=') {
                $this->query->whereNull($key);
            } elseif ($operator == '!=' || $operator == '<>') {
                $this->query->whereNotNull($key);
            }
            return $this;
        }
        
        if (strtoupper($value) == 'TRUE') {
            $value = true;
        } elseif (strtoupper($value) == 'FALSE') {
            $value = false;
        }
        
        $this->query->where($key, $operator, $value);
        return $this;
    }

    protected function filter(Request $request)
    {
        $filters = $this->getFiltersFromRequest($request);
        foreach ($filters as $filter) {
            $this->filterQuery($filter->getKey(), $filter->getOperator(), $filter->getValue());
        }
        return $this;
    }

    protected function getLimit(Request $request)
    {
        return $this->getLimitFromRequest($request)['value'] ?? $this->limit;
    }
}
