<?php

declare(strict_types=1);

namespace Pst\Database\Query\Builder\Clauses\Having;

use Pst\Core\Enum;

class HavingExpressionType extends Enum {
    public static function cases(): array {
        return [
            'HAVING' => '',
            'AND' => 'AND',
            'OR' => 'OR',
        ];
    }

    public static function HAVING(): HavingExpressionType {
        return new HavingExpressionType("");
    }

    public static function AND(): HavingExpressionType {
        return new HavingExpressionType("AND");
    }

    public static function OR(): HavingExpressionType {
        return new HavingExpressionType("OR");
    }
}