<?php

namespace App\Exceptions;

use RuntimeException;
use Throwable;

class BusinessRuleViolation extends RuntimeException
{
    /**
     * @var array<string, list<string>>
     */
    protected array $errors;

    /**
     * @param array<string, list<string>> $errors
     */
    public function __construct(string $message, array $errors = [], int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->errors = $errors;
    }

    /**
     * @return array<string, list<string>>
     */
    public function errors(): array
    {
        return $this->errors;
    }
}
