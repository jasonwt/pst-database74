<?php

declare(strict_types=1);

namespace Pst\Database\Exceptions;

use Pst\Database\Index\Index;

use Throwable;

class DuplicateUniqueIndexException extends QueryException {
    private Index $index;

    public function __construct(Index $index, string $message = 'Duplicate unique key violation.', string $query = "", int $code = 0, Throwable $previous = null) {
        $this->index = $index;

        parent::__construct($message, $query, $code, $previous);
    }

    public function index(): Index {
        return $this->index;
    }
    
}