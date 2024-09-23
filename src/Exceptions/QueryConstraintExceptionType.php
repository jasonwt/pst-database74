<?php

declare(strict_types=1);

namespace Pst\Database\Exceptions;

use Pst\Core\Enum;

class QueryConstraintExceptionType extends Enum {
    public static function cases(): array {
        return [
            'UNIQUE_KEY' => 'UNIQUE_KEY',
            'PRIMARY_KEY' => 'PRIMARY_KEY',
            'FOREIGN_KEY' => 'FOREIGN_KEY',
            'SYNTAX_ERROR' => 'SYNTAX_ERROR',
        ];
    }

    public static function UNIQUE_KEY(): QueryConstraintExceptionType {
        return new QueryConstraintExceptionType("UNIQUE_KEY");
    }

    public static function PRIMARY_KEY(): QueryConstraintExceptionType {
        return new QueryConstraintExceptionType("PRIMARY_KEY_CONSTRAINT");
    }

    public static function FOREIGN_KEY(): QueryConstraintExceptionType {
        return new QueryConstraintExceptionType("FOREIGN_KEY_CONSTRAINT");
    }

    public static function SYNTAX_ERROR(): QueryConstraintExceptionType {
        return new QueryConstraintExceptionType("SYNTAX_ERROR");
    }
}