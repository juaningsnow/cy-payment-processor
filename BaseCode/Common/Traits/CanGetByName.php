<?php

namespace BaseCode\Common\Traits;

trait CanGetByName
{
    public function getByName($name, $field = 'name')
    {
        return $this->model->where($field, $name)->first();
    }
}
