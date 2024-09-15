<?php

declare(strict_types=1);

namespace Pst\Database\Query\Builder\Clauses\Where;

use Pst\Core\Enum;

class WhereExpressionType extends Enum {
    public static function cases(): array {
        return [
            'WHERE' => '',
            'AND' => 'AND',
            'OR' => 'OR',
        ];
    }

    public static function WHERE(): WhereExpressionType {
        return new WhereExpressionType("");
    }

    public static function AND(): WhereExpressionType {
        return new WhereExpressionType("AND");
    }

    public static function OR(): WhereExpressionType {
        return new WhereExpressionType("OR");
    }
}