<?php

namespace BaseCode\Common\Exceptions;

class GeneralApiException extends ApiException
{
    public function __construct($message = '', $title = '', $statusCode = 400, $isModal = false, array $errors = [], $internalCode = '', \Exception $previous = null)
    {
        $this->message = $message;
        $this->title = $title;
        $this->statusCode = $statusCode;
        $this->internalCode = $internalCode;
        $this->isModal = $isModal;
        $this->errors = $errors;
        parent::__construct($previous);
    }
}
