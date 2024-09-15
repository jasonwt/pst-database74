<?php

declare(strict_types=1);

namespace Pst\Database\Query\Builder\Clauses\Insert;

use Pst\Core\Enum;

class InsertType extends Enum {
    public static function cases(): array {
        return [
            'INTO' => 'INTO',
            'IGNORE' => 'IGNORE'
        ];
    }

    public static function INTO(): self {
        return new InsertType('INTO');
    }

    public static function IGNORE(): self {
        return new InsertType('IGNORE');
    }
}