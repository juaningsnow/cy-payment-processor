<?php

namespace BaseCode\Common\Utils;

class Sorter
{
    private $key;
    private $direction;

    public function __construct($key, $direction = 'ASC')
    {
        $this->key = $key;
        $this->direction = $direction;
    }

    public function getKey()
    {
        return $this->key;
    }

    public function getDirection()
    {
        return $this->direction;
    }
}
