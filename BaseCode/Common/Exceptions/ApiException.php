<?php

namespace BaseCode\Common\Exceptions;

abstract class ApiException extends \Exception
{
    protected $internalCode;
    protected $statusCode = 400;
    protected $message;
    protected $title;
    protected $errors = [];
    protected $isModal = false;

    public function __construct(\Exception $previous = null)
    {
        parent::__construct($this->message, $this->statusCode, $previous);
    }

    public function isModal()
    {
        return $this->isModal;
    }

    public function getErrorMessages()
    {
        return array_reduce($this->errors, function ($prev, $curr) {
            $prev[$curr->getTitle()] = $curr->getDetails();
            return $prev;
        }, []);
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getInternalCode()
    {
        return $this->internalCode;
    }

    public function getStatusCode()
    {
        return $this->getCode();
    }
}
