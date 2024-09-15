<?php

declare(strict_types=1);

namespace Pst\Database\Enums;

use Pst\Core\Enum;

class JoinType extends Enum {
    public static function cases(): array {
        return [
            'INNER' => 'INNER',
            'LEFT' => 'LEFT',
            'RIGHT' => 'RIGHT'
        ];
    }

    public static function INNER(): self {
        return new self("INNER");
    }

    public static function LEFT(): self {
        return new self("LEFT");
    }

    public static function RIGHT(): self {
        return new self("RIGHT");
    }
}