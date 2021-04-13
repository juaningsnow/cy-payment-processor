<?php

namespace App\Utils;

class Filter
{
    private $key;
    private $value;
    private $operator;

    public function __construct($key, $value, $operator = '=')
    {
        $this->key = $key;
        $this->value = $value;
        $this->operator = $operator;
    }

    public function toArray(string $keyName = 'key', string $valueName = 'value', string $operatorName = 'operator')
    {
        return [
            $keyName => $this->key,
            $valueName => $this->value,
            $operatorName => $this->operator,
        ];
    }

    public function getKey()
    {
        return $this->key;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function getOperator()
    {
        return $this->operator;
    }
}
