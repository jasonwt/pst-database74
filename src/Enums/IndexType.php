<?php

declare(strict_types=1);

namespace Pst\Database\Enums;

use Pst\Core\Enum;

class IndexType extends Enum {
    protected static function caseAliases(): array {
        return [
            'PRI' => 'PRIMARY',
            'UNI' => 'UNIQUE',
            'MUL' => 'INDEX'
        ];
    }
    public static function cases(): array {
        return [
            'PRIMARY' => 'PRIMARY',
            'UNIQUE' => 'UNIQUE',
            'INDEX' => 'INDEX',
            'FULLTEXT' => 'FULLTEXT',
            'SPATIAL' => 'SPATIAL'
        ];
    }

    public static function PRIMARY(): IndexType {
        return new IndexType("PRIMARY");
    }

    public static function UNIQUE(): IndexType {
        return new IndexType("UNIQUE");
    }

    public static function INDEX(): IndexType {
        return new IndexType("INDEX");
    }

    public static function FULLTEXT(): IndexType {
        return new IndexType("FULLTEXT");
    }

    public static function SPATIAL(): IndexType {
        return new IndexType("SPATIAL");
    }

    public function isPrimary(): bool {
        return $this->value() === 'PRIMARY';
    }

    public function isUnique(): bool {
        return $this->value() === 'PRIMARY' || $this->value() === 'UNIQUE';
    }
}