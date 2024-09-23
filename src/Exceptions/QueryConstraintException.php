<?php

declare(strict_types=1);

namespace Pst\Database\Exceptions;

use Exception;
use Throwable;

class QueryConstraintException extends QueryException {
    private QueryConstraintExceptionType $queryConstraintExceptionType;

    public function __construct(QueryConstraintExceptionType $queryConstraintExceptionType, string $query = "", string $message = "", int $code = 0, Throwable $previous = null) {
        if (empty($message = trim($message))) {
            $message = (string) $queryConstraintExceptionType;
        }
        $this->queryConstraintExceptionType = $queryConstraintExceptionType;

        parent::__construct($message, $query, $code, $previous);
    }

    public function constraintExceptionType(): QueryConstraintExceptionType {
        return $this->queryConstraintExceptionType;
    }
}