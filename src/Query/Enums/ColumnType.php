<?php

declare(strict_types=1);

namespace Pst\Database\Enums;

use Pst\Core\Enum;
use Pst\Core\IEnum;
use Pst\Core\Types\TypeHintFactory;

use Pst\Core\Exceptions\NotImplementedException;

use InvalidArgumentException;

class ColumnType extends Enum {
    public static function cases(): array {
        return [
            'TINYINT' => 'TINYINT',
            'SMALLINT' => 'SMALLINT',
            'MEDIUMINT' => 'MEDIUMINT',
            'INT' => 'INT',
            'BIGINT' => 'BIGINT',

            'UNSIGNED_TINYINT' => 'UNSIGNED_TINYINT',
            'UNSIGNED_SMALLINT' => 'UNSIGNED_SMALLINT',
            'UNSIGNED_MEDIUMINT' => 'UNSIGNED_MEDIUMINT',
            'UNSIGNED_INT' => 'UNSIGNED_INT',
            'UNSIGNED_BIGINT' => 'UNSIGNED_BIGINT',

            'AUTO_INCREMENTING_TINYINT' => 'AUTO_INCREMENTING_TINYINT',
            'AUTO_INCREMENTING_SMALLINT' => 'AUTO_INCREMENTING_SMALLINT',
            'AUTO_INCREMENTING_MEDIUMINT' => 'AUTO_INCREMENTING_MEDIUMINT',
            'AUTO_INCREMENTING_INT' => 'AUTO_INCREMENTING_INT',
            'AUTO_INCREMENTING_BIGINT' => 'AUTO_INCREMENTING_BIGINT',

            'AUTO_INCREMENTING_UNSIGNED_TINYINT' => 'AUTO_INCREMENTING_UNSIGNED_TINYINT',
            'AUTO_INCREMENTING_UNSIGNED_SMALLINT' => 'AUTO_INCREMENTING_UNSIGNED_SMALLINT',
            'AUTO_INCREMENTING_UNSIGNED_MEDIUMINT' => 'AUTO_INCREMENTING_UNSIGNED_MEDIUMINT',
            'AUTO_INCREMENTING_UNSIGNED_INT' => 'AUTO_INCREMENTING_UNSIGNED_INT',
            'AUTO_INCREMENTING_UNSIGNED_BIGINT' => 'AUTO_INCREMENTING_UNSIGNED_BIGINT',

            'DECIMAL' => 'DECIMAL',
            'FLOAT' => 'FLOAT',
            'DOUBLE' => 'DOUBLE',
            'REAL' => 'REAL',

            'UNSIGNED_DECIMAL' => 'UNSIGNED_DECIMAL',
            'UNSIGNED_FLOAT' => 'UNSIGNED_FLOAT',
            'UNSIGNED_DOUBLE' => 'UNSIGNED_DOUBLE',
            'UNSIGNED_REAL' => 'UNSIGNED_REAL',

            'CHAR' => 'CHAR',
            'VARCHAR' => 'VARCHAR',

            'TINYTEXT' => 'TINYTEXT',
            'TEXT' => 'TEXT',
            'MEDIUMTEXT' => 'MEDIUMTEXT',
            'LONGTEXT' => 'LONGTEXT',

            'BINARY' => 'BINARY',
            'VARBINARY' => 'VARBINARY',

            'TINYBLOB' => 'TINYBLOB',
            'BLOB' => 'BLOB',
            'MEDIUMBLOB' => 'MEDIUMBLOB',
            'LONGBLOB' => 'LONGBLOB',

            'DATE' => 'DATE',
            'TIME' => 'TIME',
            'DATETIME' => 'DATETIME',
            'TIMESTAMP' => 'TIMESTAMP',

            'SET' => 'SET',
            'ENUM' => 'ENUM'
        ];
    }

    public static function TINYINT(): ColumnType {
        return new ColumnType("TINYINT");
    }

    public static function SMALLINT(): ColumnType {
        return new ColumnType("SMALLINT");
    }

    public static function MEDIUMINT(): ColumnType {
        return new ColumnType("MEDIUMINT");
    }

    public static function INT(): ColumnType {
        return new ColumnType("INT");
    }

    public static function BIGINT(): ColumnType {
        return new ColumnType("BIGINT");
    }

    public static function UNSIGNED_TINYINT(): ColumnType {
        return new ColumnType("UNSIGNED_TINYINT");
    }

    public static function UNSIGNED_SMALLINT(): ColumnType {
        return new ColumnType("UNSIGNED_SMALLINT");
    }

    public static function UNSIGNED_MEDIUMINT(): ColumnType {
        return new ColumnType("UNSIGNED_MEDIUMINT");
    }

    public static function UNSIGNED_INT(): ColumnType {
        return new ColumnType("UNSIGNED_INT");
    }

    public static function UNSIGNED_BIGINT(): ColumnType {
        return new ColumnType("UNSIGNED_BIGINT");
    }

    public static function AUTO_INCREMENTING_TINYINT(): ColumnType {
        return new ColumnType("AUTO_INCREMENTING_TINYINT");
    }

    public static function AUTO_INCREMENTING_SMALLINT(): ColumnType {
        return new ColumnType("AUTO_INCREMENTING_SMALLINT");
    }

    public static function AUTO_INCREMENTING_MEDIUMINT(): ColumnType {
        return new ColumnType("AUTO_INCREMENTING_MEDIUMINT");
    }

    public static function AUTO_INCREMENTING_INT(): ColumnType {
        return new ColumnType("AUTO_INCREMENTING_INT");
    }

    public static function AUTO_INCREMENTING_BIGINT(): ColumnType {
        return new ColumnType("AUTO_INCREMENTING_BIGINT");
    }

    public static function AUTO_INCREMENTING_UNSIGNED_TINYINT(): ColumnType {
        return new ColumnType("AUTO_INCREMENTING_UNSIGNED_TINYINT");
    }

    public static function AUTO_INCREMENTING_UNSIGNED_SMALLINT(): ColumnType {
        return new ColumnType("AUTO_INCREMENTING_UNSIGNED_SMALLINT");
    }

    public static function AUTO_INCREMENTING_UNSIGNED_MEDIUMINT(): ColumnType {
        return new ColumnType("AUTO_INCREMENTING_UNSIGNED_MEDIUMINT");
    }

    public static function AUTO_INCREMENTING_UNSIGNED_INT(): ColumnType {
        return new ColumnType("AUTO_INCREMENTING_UNSIGNED_INT");
    }

    public static function AUTO_INCREMENTING_UNSIGNED_BIGINT(): ColumnType {
        return new ColumnType("AUTO_INCREMENTING_UNSIGNED_BIGINT");
    }

    public static function DECIMAL(): ColumnType {
        return new ColumnType("DECIMAL");
    }

    public static function FLOAT(): ColumnType {
        return new ColumnType("FLOAT");
    }

    public static function DOUBLE(): ColumnType {
        return new ColumnType("DOUBLE");
    }

    public static function REAL(): ColumnType {
        return new ColumnType("REAL");
    }

    public static function UNSIGNED_DECIMAL(): ColumnType {
        return new ColumnType("UNSIGNED_DECIMAL");
    }

    public static function UNSIGNED_FLOAT(): ColumnType {
        return new ColumnType("UNSIGNED_FLOAT");
    }

    public static function UNSIGNED_DOUBLE(): ColumnType {
        return new ColumnType("UNSIGNED_DOUBLE");
    }

    public static function UNSIGNED_REAL(): ColumnType {
        return new ColumnType("UNSIGNED_REAL");
    }

    public static function CHAR(): ColumnType {
        return new ColumnType("CHAR");
    }

    public static function VARCHAR(): ColumnType {
        return new ColumnType("VARCHAR");
    }

    public static function TINYTEXT(): ColumnType {
        return new ColumnType("TINYTEXT");
    }

    public static function TEXT(): ColumnType {
        return new ColumnType("TEXT");
    }

    public static function MEDIUMTEXT(): ColumnType {
        return new ColumnType("MEDIUMTEXT");
    }

    public static function LONGTEXT(): ColumnType {
        return new ColumnType("LONGTEXT");
    }

    public static function BINARY(): ColumnType {
        return new ColumnType("BINARY");
    }

    public static function VARBINARY(): ColumnType {
        return new ColumnType("VARBINARY");
    }

    public static function TINYBLOB(): ColumnType {
        return new ColumnType("TINYBLOB");
    }

    public static function BLOB(): ColumnType {
        return new ColumnType("BLOB");
    }

    public static function MEDIUMBLOB(): ColumnType {
        return new ColumnType("MEDIUMBLOB");
    }

    public static function LONGBLOB(): ColumnType {
        return new ColumnType("LONGBLOB");
    }

    public static function DATE(): ColumnType {
        return new ColumnType("DATE");
    }

    public static function TIME(): ColumnType {
        return new ColumnType("TIME");
    }

    public static function DATETIME(): ColumnType {
        return new ColumnType("DATETIME");
    }

    public static function TIMESTAMP(): ColumnType {
        return new ColumnType("TIMESTAMP");
    }

    public static function SET(): ColumnType {
        return new ColumnType("SET");
    }

    public static function ENUM(): ColumnType {
        return new ColumnType("ENUM");
    }

    public static function fromMysqlColumnType(string $columnType, bool $isAutoIncrementing = false): ColumnType {
        if (($columnType = trim(strtoupper($columnType))) === "") {
            throw new InvalidArgumentException("Invalid column type name: $columnType");
        }

        if (($zeroFilled = (substr($columnType, -8) === "ZEROFILL"))) {
            $columnType = trim(substr($columnType, 0, -8));
        }

        if (($unsigned = (substr($columnType, -8) === "UNSIGNED"))) {
            $columnType = trim(substr($columnType, 0, -8));
        }

        $columnTypeParts = explode("(", $columnType);
        $dataType = trim($columnTypeParts[0]);

        if (in_array($dataType, ["TINYINT", "SMALLINT", "MEDIUMINT", "INT", "BIGINT"])) {
            $columnTypeName = $isAutoIncrementing ? "AUTO_INCREMENTING_" : "";
            $columnTypeName .= $unsigned ? "UNSIGNED_" : "";
            $columnTypeName .= $dataType;
            
            return self::fromName($columnTypeName);
        }
        
        if (in_array($dataType, ["DECIMAL", "FLOAT", "DOUBLE", "REAL"])) {
            $columnTypeName = $unsigned ? "UNSIGNED_" : "";
            $columnTypeName .= $dataType;
        } else if ($unsigned) {
            throw new InvalidArgumentException("Invalid unsigned column type: $columnType");
        }

        if ($isAutoIncrementing) {
            throw new InvalidArgumentException("Invalid auto incrementing column type: $columnType");
        }

        if ($zeroFilled) {
            throw new InvalidArgumentException("Invalid zero filled column type: $columnType");
        }

        return self::fromName($dataType);
    }

    public function isUnsigned(): bool {
        return strpos($this->name(), "UNSIGNED") !== false;
    }

    public function isAutoIncrementing(): bool {
        return strpos($this->name(), "AUTO_INCREMENTING") !== false;
    }

    public function isIntegerType(): bool {
        return substr($this->name(), -3) === "INT";
    }

    public function isDecimalType(): bool {
        return in_array($this->name(), ["DECIMAL", "FLOAT", "DOUBLE", "REAL"]);
    }

    public function isNumericType(): bool {
        return $this->isIntegerType() || $this->isDecimalType();
    }

    public function isChar(): bool {
        return in_array($this->name(), ["CHAR", "VARCHAR"]);
    }

    public function isText(): bool {
        return in_array($this->name(), ["TINYTEXT", "TEXT", "MEDIUMTEXT", "LONGTEXT"]);
    }

    public function isTextType(): bool {
        return $this->isChar() || $this->isText();
    }

    public function isBinary(): bool {
        return in_array($this->name(), ["BINARY", "VARBINARY"]);
    }

    public function isBlob(): bool {
        return in_array($this->name(), ["TINYBLOB", "BLOB", "MEDIUMBLOB", "LONGBLOB"]);
    }

    public function isBinaryType(): bool {
        return $this->isBinary() || $this->isBlob();
    }

    public function isStringType(): bool {
        return $this->isTextType() || $this->isBinaryType();
    }

    public function isTinyInt(): bool {
        return in_array($this->name(), ["TINYINT", "UNSIGNED_TINYINT", "AUTO_INCREMENTING_TINYINT", "AUTO_INCREMENTING_UNSIGNED_TINYINT"]);
    }

    public function isSmallInt(): bool {
        return in_array($this->name(), ["SMALLINT", "UNSIGNED_SMALLINT", "AUTO_INCREMENTING_SMALLINT", "AUTO_INCREMENTING_UNSIGNED_SMALLINT"]);
    }

    public function isMediumInt(): bool {
        return in_array($this->name(), ["MEDIUMINT", "UNSIGNED_MEDIUMINT", "AUTO_INCREMENTING_MEDIUMINT", "AUTO_INCREMENTING_UNSIGNED_MEDIUMINT"]);
    }

    public function isInt(): bool {
        return in_array($this->name(), ["INT", "UNSIGNED_INT", "AUTO_INCREMENTING_INT", "AUTO_INCREMENTING_UNSIGNED_INT"]);
    }

    public function isBigInt(): bool {
        return in_array($this->name(), ["BIGINT", "UNSIGNED_BIGINT", "AUTO_INCREMENTING_BIGINT", "AUTO_INCREMENTING_UNSIGNED_BIGINT"]);
    }

    public function isDecimal(): bool {
        return in_array($this->name(), ["DECIMAL", "UNSIGNED_DECIMAL"]);
    }

    public function isFloat(): bool {
        return in_array($this->name(), ["FLOAT", "UNSIGNED_FLOAT"]);
    }

    public function isDouble(): bool {
        return in_array($this->name(), ["DOUBLE", "UNSIGNED_DOUBLE"]);
    }

    public function isReal(): bool {
        return in_array($this->name(), ["REAL", "UNSIGNED_REAL"]);
    }

    public function requiresLength(): bool {
        return $this->isChar() || $this->isBinary() || $this->isText() || $this->isBlob() || $this->name() === "ENUM" || $this->name() === "SET";
    }

    public function toTypeHint(bool $isNullable = false): TypeHint {
        $typeHintString = $this->isIntegerType() ? 'int' : (
            $this->isDecimalType() ? 'float' : (
                $this->isStringType() ? 'string' : null
            )
        );

        if ($typeHintString === null) {
            throw new NotImplementedException("Unsupported column type: '" . $this->name() . "'");
        }

        if ($isNullable) {
            $typeHintString = '?' . $typeHintString;
        }

        return TypeHintFactory::tryParse($typeHintString);
    }

    public function toPhpType(bool $isNullable = false): string {
        $phpType = $this->isIntegerType() ? ($isNullable ? '?int' : 'int') : (
            $this->isDecimalType() ? ($isNullable ? '?float' : 'float') : (
                $this->isStringType() ? ($isNullable ? '?string' : 'string') : null
            )
        );

        if ($phpType === null) {
            throw new NotImplementedException("Unsupported column type: '" . $this->name() . "'");
        }

        return $phpType;
    }
}