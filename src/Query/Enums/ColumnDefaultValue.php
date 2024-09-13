<?php

declare(strict_types=1);

namespace Pst\Database\Enums;

use Pst\Core\Enum;

class ColumnDefaultValue extends Enum {
    public static function cases(): array {
        return [
            'NONE' => 'NONE',
            'NULL' => 'NULL',
            'UUID' => 'UUID',
            'CURRENT_TIMESTAMP' => 'CURRENT_TIMESTAMP'
        ];
    }

    public static function NONE(): ColumnDefaultValue {
        return new ColumnDefaultValue("NONE");
    }

    public static function NULL(): ColumnDefaultValue {
        return new ColumnDefaultValue("NULL");
    }

    public static function UUID(): ColumnDefaultValue {
        return new ColumnDefaultValue("UUID");
    }

    public static function CURRENT_TIMESTAMP(): ColumnDefaultValue {
        return new ColumnDefaultValue("CURRENT_TIMESTAMP");
    }
}