<?php

namespace BaseCode\Common\Errors;

class BaseError
{
    protected $title;
    protected $details = [];

    public function __construct($title, array $details = [])
    {
        $this->title = $title;
        $this->details[] = $details;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getDetails()
    {
        return $this->details;
    }
}
