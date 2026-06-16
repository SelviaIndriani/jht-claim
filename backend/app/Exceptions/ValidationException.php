<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Response;

class ValidationException extends Exception
{
    public function __construct(
        protected array $errors = [],
        string $message = 'Validation failed'
    ) {
        parent::__construct($message);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function render()
    {
        return response()->json([
            'success' => false,
            'message' => $this->getMessage(),
            'errors'  => $this->errors,
        ], Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
