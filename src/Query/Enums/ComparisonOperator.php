<?php

declare(strict_types=1);

namespace Pst\Database\Query\Enums;

use Pst\Core\Enum;

class ComparisonOperator extends Enum {
    public static function cases(): array {
        return [
            'EQUALS' => '=',
            'NOT_EQUALS' => '!=',
            'NOT_EQUALS2' => '<>',
            'GREATER_THAN' => '>',
            'LESS_THAN' => '<',
            'GREATER_THAN_OR_EQUAL' => '>=',
            'LESS_THAN_OR_EQUAL' => '<=',

            // 'LIKE' => 'LIKE',
        ];
    }

    public static function EQUALS(): ComparisonOperator {
        return new ComparisonOperator("EQUALS");
    }

    public static function NOT_EQUALS(): ComparisonOperator {
        return new ComparisonOperator("NOT_EQUALS");
    }

    public static function NOT_EQUALS2(): ComparisonOperator {
        return new ComparisonOperator("NOT_EQUALS2");
    }

    public static function GREATER_THAN(): ComparisonOperator {
        return new ComparisonOperator("GREATER_THAN");
    }

    public static function LESS_THAN(): ComparisonOperator {
        return new ComparisonOperator("LESS_THAN");
    }

    public static function GREATER_THAN_OR_EQUAL(): ComparisonOperator {
        return new ComparisonOperator("GREATER_THAN_OR_EQUAL");
    }

    public static function LESS_THAN_OR_EQUAL(): ComparisonOperator {
        return new ComparisonOperator("LESS_THAN_OR_EQUAL");
    }

    public static function LIKE(): ComparisonOperator {
        return new ComparisonOperator("LIKE");
    }
}