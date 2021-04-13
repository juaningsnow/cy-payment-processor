<?php

namespace App\Utils;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class UriParser
{
    /**
     * @var Request the given request
     */
    protected $request;

    /**
     * @var string the available compare operators
     */
    protected $pattern = '/!=|=|<=|<|>=|>/';

    /**
     * @var array the keywords which are handled individually
     */
    protected $predefinedParams = [
        'sort',
        'limit',
        'page',
        'include',
        'fields',
        //'columns',
        //'rels',
    ];

    /**
     * @var string the request uri
     */
    protected $uri;

    /**
     * @var string the path of the uri
     */
    protected $path;

    /**
     * @var string the query string (already encoded)
     */
    protected $query;

    /**
     * @var array the extracted query parameters
     */
    protected $queryParameters = [];

    public function setup(Request $request)
    {
        $this->request = $request;

        $this->uri = $request->getRequestUri();

        $this->path = $request->getPathInfo();

        $this->query = rawurldecode($request->getQueryString());

        if ($this->hasQueryUri()) {
            $this->setQueryParameters($this->query);
        }
    }

    /**
     * Gets the respective parameter
     *
     * @param $key
     * @return mixed
     */
    public function queryParameter($key)
    {
        $keys = Arr::pluck($this->queryParameters, 'key');
        $queryParameters = array_combine($keys, $this->queryParameters);

        return $queryParameters[$key];
    }

    /**
     * Gets the predefined parameters
     *
     * @return array
     */
    public function predefinedParameters()
    {
        return $this->predefinedParams;
    }

    /**
     * Gets the WHERE parameters
     *
     * @return array
     */
    public function whereParameters()
    {
        return array_filter(
            $this->queryParameters,
            function ($queryParameter) {
                $key = $queryParameter['key'];
                return (!in_array($key, $this->predefinedParams));
            }
        );
    }

    /**
     * Sets the query parameters
     *
     * @param $query
     */
    private function setQueryParameters($query)
    {
        $queryParameters = array_filter(explode('&', $query));

        array_map([$this, 'appendQueryParameter'], $queryParameters);
    }

    /**
     * Appends one parameter to the builder
     *
     * @param $parameter
     */
    private function appendQueryParameter($parameter)
    {
        preg_match($this->pattern, $parameter, $matches);

        if (empty($matches)) {
            return;
        }

        $operator = $matches[0];

        list($key, $value) = explode($operator, $parameter);

        if (strlen($value) == 0) {
            return;
        }

        if ((!$this->isPredefinedParameter($key)) && $this->isLikeQuery($value)) {
            if ($operator == '=') {
                $operator = 'like';
            }
            if ($operator == '!=') {
                $operator = 'not like';
            }

            $value = str_replace('*', '%', $value);
        }

        $this->queryParameters[] = [
            'key' => $key,
            'operator' => $operator,
            'value' => $value
        ];
    }

    /**
     * Checks if the URI has a query string appended
     *
     * @return string
     */
    protected function hasQueryUri()
    {
        return ($this->query);
    }

    /**
     * Checks if the URI has query parameters
     * @return bool
     */
    public function hasQueryParameters()
    {
        return (count($this->queryParameters) > 0);
    }

    /**
     * Checks, if the given query parameter exists
     *
     * @param $key
     * @return bool
     */
    public function hasQueryParameter($key)
    {
        $keys = Arr::pluck($this->queryParameters, 'key');
        return (in_array($key, $keys));
    }

    /**
     * Checks, if the query parameter contains an asteriks (*) symbol and must be treated as like parameter
     *
     * @param $query
     * @return int
     */
    private function isLikeQuery($query)
    {
        $pattern = "/^\*|\*$/";

        return (preg_match($pattern, $query, $matches));

        // return strpos($query, '%') !== false || strpos($query, '*') !== false;
    }

    /**
     * Checks if the key is a predefined parameter
     *
     * @param $key
     * @return bool
     */
    private function isPredefinedParameter($key)
    {
        return (in_array($key, $this->predefinedParams));
    }
}
