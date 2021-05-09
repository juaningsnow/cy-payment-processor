<?php

namespace BaseCode\Common\Controllers;

use BaseCode\Common\Utils\RepoExporter;
use BaseCode\Common\Utils\UriParserHelper;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Database\Eloquent\Model;

abstract class ResourceApiController extends Controller
{
    protected $query;

    use UriParserHelper;

    protected $limit = 15;
    protected $exportFileName = 'export.xlsx';

    public function __construct(Model $model)
    {
        $this->query = $model->newQuery();
    }

    abstract protected function getResource($query);

    abstract protected function getResourceCollection($pagination);

    public function show(Request $request, $id)
    {
        $this->filter($request)->sort($request)->include($request);
        return $this->getResource($this->query->find($id));
    }

    // TODO test if working with query
    public function export(Request $request)
    {
        $this->filter($request)->sort($request)->include($request);
        return new RepoExporter($this->query->getModel(), $this->getFieldsFromRequest($request), $this->exportFileName);
    }

    public function index(Request $request)
    {
        $this->filter($request)->sort($request)->include($request);
        return $this->getResourceCollection($this->query->paginate($this->getLimit($request)));
    }
    
    public function destroyMultiple(Request $request)
    {
        $ids = $request->all();
        \DB::transaction(function () use ($ids) {
            foreach ($ids as $id) {
                $this->destroy($id);
            }
        });
    }

    protected function include(Request $request)
    {
        $includes = $this->getIncludesFromRequest($request);
        foreach ($includes as $include) {
            $this->query->with($include);
        }
        return $this;
    }

    public function sortQuery($key, $direction = 'ASC')
    {
        $scopeMethod = \Str::camel('scopeOrderBy' . ucfirst($key));

        if (method_exists($this->query->getModel(), $scopeMethod)) {
            $scope = \Str::camel('orderBy' . ucfirst($key));
            $this->query->$scope($direction);
            return $this;
        }
        
        $key = \Str::snake($key);
        
        $this->query->orderBy($key, $direction);
        return $this;
    }

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

    protected function only(Request $request)
    {
        // TODO
        // return $this->getFieldsFromRequest($request);
    }

    protected function sort(Request $request)
    {
        $sorters = $this->getSortersFromRequest($request);

        foreach ($sorters as $sorter) {
            $this->sortQuery($sorter->getKey(), $sorter->getDirection());
        }

        return $this;
    }
}
