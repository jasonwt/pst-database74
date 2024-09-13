<?php

declare(strict_types=1);

namespace Pst\Database\Query\Enums;

use Pst\Core\Enum;

class JoinType extends Enum {
    public static function cases(): array {
        return [
            'INNER' => 'INNER',
            'LEFT' => 'LEFT',
            'RIGHT' => 'RIGHT',
            'FULL' => 'FULL',
            'CROSS' => 'CROSS',
            'NATURAL' => 'NATURAL',
            'SELF' => 'SELF'
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

    public static function FULL(): self {
        return new self("FULL");
    }

    public static function CROSS(): self {
        return new self("CROSS");
    }

    public static function NATURAL(): self {
        return new self("NATURAL");
    }

    public static function SELF(): self {
        return new self("SELF");
    }
}