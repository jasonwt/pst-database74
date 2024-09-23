<?php

declare(strict_types=1);

namespace Pst\Database\Exceptions;


class QueryException extends DatabaseException {
    private string $query;

    public function __construct(string $message = 'Query exception.', string $query = "", int $code = 0, \Throwable $previous = null) {
        $this->query = $query;

        parent::__construct($message, $code, $previous);
    }

    public function query(): string {
        return $this->query;
    }
}