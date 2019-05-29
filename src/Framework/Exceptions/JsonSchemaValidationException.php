<?php

namespace App\Framework\Exceptions;

use Exception;
use Opis\JsonSchema\ValidationResult;
use Throwable;

class JsonSchemaValidationException extends Exception
{
    private $validation;

    public function __construct(ValidationResult $validation, $code = 0, Throwable $previous = null)
    {
        $this->validation = $validation;
        parent::__construct('', $code, $previous);
    }

    public function getValidation()
    {
        return $this->validation;
    }
}
