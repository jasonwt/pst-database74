<?php

declare(strict_types=1);

namespace Pst\Database;

use Closure;

use Pst\Core\Func;
use Pst\Core\IToString;
use Pst\Core\Types\TypeHintFactory;

use Pst\Database\Structure\Column;
use Pst\Database\Structure\Table;


use Pst\Core\Exceptions\NotImplementedException;
use Pst\Database\Structure\ColumnType;

final class Validator {
    /**
     * Validate schema name
     * 
     * @param string $name 
     * 
     * @return bool|string 
     */
    public static function validateSchemaName(string $name) {
        return ($name !== "" && preg_match("/^[a-zA-Z0-9_][a-zA-Z0-9_\$]*$/", $name)) ? true : "Invalid schema name: '$name'.";
    }

    /**
     * Validate table name
     * 
     * @param string $name 
     * 
     * @return bool|string 
     */
    public static function validateTableName(string $name) {
        return ($name !== "" && preg_match("/^[a-zA-Z0-9_][a-zA-Z0-9_\$]*$/", $name)) ? true : "Invalid table name: '$name'.";
    }

    /**
     * Validate table columns value
     * 
     * @param Table $table 
     * @param array $values 
     * 
     * @return bool|array 
     */
    public static function validateTableColumnsValue(Table $table, array $values) {

        throw new NotImplementedException("Not implemented");

        //return $columnValues;
    }

    /**
     * Validate index name
     * 
     * @param string $name 
     * 
     * @return bool|string 
     */
    public static function validateIndexName(string $name) {
        return ($name !== "" && preg_match("/^[a-zA-Z0-9_][a-zA-Z0-9_\$]*$/", $name)) ? true : "Invalid index name: '$name'.";
    }

    /**
     * Validate column name
     * 
     * @param string $name 
     * 
     * @return bool|string 
     */
    public static function validateColumnName(string $name) {
        return ($name !== "" && preg_match("/^[a-zA-Z0-9_][a-zA-Z0-9_\$]*$/", $name)) ? true : "Invalid column name: '$name'.";
    }

    public static function avalidateColumnValue($value, string $columnType, bool $isNullable, ?int $length = null) {
        if ($value === null) {
            if (!$isNullable) {
                return "Value cannot be null";
            }
            
            return true;
        }

        $columnType = ColumnType::fromString($columnType);

        if ($columnType->isStringType()) {
            if ($length === null) {
                return "Column length is required for ENUM, SET, CHAR, BINARY, TEXT AND BLOB types";
            }

            if ($columnType->isStringType()) {
                if (!is_string($value)) {
                    return "Can not assign value type: '" . gettype($value) . "' to string column type";
                }

                if (strlen($value) > $length) {
                    return "Value length: " . strlen($value) . " exceeds maximum character length: {$length}";
                }
            }

        } else if ($columnType->isNumericType()) {
            if ($length !== null) {
                return "Column length is only allowed for ENUM, SET, CHAR, BINARY, TEXT AND BLOB types";
            }

            $isIntegerType = $columnType->isIntegerType();

            $columnTypeIsUnsigned = $columnType->isUnsigned();

            if (is_string($value)) {
                if (!is_numeric($value)) {
                    return "Can not assign non-numeric value: {$value} to column type: '{$columnType}'";
                }

                $value = (strpos($value, ".") !== false) ? (float) $value : (int) $value;
            }
            
            if (is_float($value)) {
                if (strpos((string) $value, ".") !== false && $isIntegerType) {
                    return "Can not assign floating point value: {$value} to column type: '{$columnType}'";
                }

                $value = $isIntegerType ? (int) $value : (float) $value;
            } else if (is_int($value)) {
                $value = $isIntegerType ? (int) $value : (float) $value;
            } else {
                return "Can not assign value type: '" . gettype($value) . "' to column type: '{$columnType}'";
            }

            if ($value < 0 && $columnTypeIsUnsigned) {
                return "Can not assign value: $value to unsigned column type.";
            }

            if ($isIntegerType) {
                $maxValue = $columnType->isTinyInt() ? 127 : (
                    $columnType->isSmallInt() ? 32767 : (
                        $columnType->isMediumInt() ? 8388607 : (
                            $columnType->isInt() ? 2147483647 : 9223372036854775807
                        )
                    )
                );

                if ($columnTypeIsUnsigned) {
                    $maxValue = ($maxValue * 2) + 1;
                    $minValue = 0;
                }

                if ($value < $minValue || $value > $maxValue) {
                    return "Value: $value out of range for column type: {$columnType}";
                }
            }
        }

        throw new NotImplementedException("Column type: {$columnType} is not implemented");
    }

    /**
     * Validate column value
     * 
     * @param Column $column 
     * @param mixed $value 
     * @param Closure $escapeStringClosure 
     * 
     * @return bool|string 
     */
    public static function validateColumnValue(Column $column, mixed $value) {
        $columnType = $column->type();
        $isNullable = $column->isNullable();

        if ($value === null) {
            return $isNullable ? true : "Value cannot be null";
        }

        if ($columnType->isStringType()) {
            $maxCharacterLength = $column->length();

            if (is_object($value)) {
                if (!$value instanceof IToString) {
                    return "Can not assign object of type: '" . get_class($value) . "' to string column type";
                }

                $value = $value->toString();
            } else if (!is_string($value)) {
                return "Can not assign value type: '" . gettype($value) . "' to string column type";
            }

            if ($maxCharacterLength !== null && strlen($value) > $maxCharacterLength) {
                $valueLength = strlen($value);
                $value = (strlen($value) > 50) ? substr($value, 0, 50) . "..." : $value;

                return "Value: '{$value}' length: {$valueLength} exceeds maximum character length: {$maxCharacterLength}";
            }
        } else if ($columnType->isNumericType()) {
            $isIntegerType = $columnType->isIntegerType();

            $columnTypeIsUnsigned = $columnType->isUnsigned();

            if (is_string($value)) {
                if (!is_numeric($value)) {
                    return "Can not assign non-numeric value: {$value} to column type: '{$columnType}'";
                }

                $value = (strpos($value, ".") !== false) ? (float) $value : (int) $value;
            }
            
            if (is_float($value)) {
                if (strpos((string) $value, ".") !== false && $isIntegerType) {
                    return "Can not assign floating point value: {$value} to column type: '{$columnType}'";
                }

                $value = $isIntegerType ? (int) $value : (float) $value;
            } else if (is_int($value)) {
                $value = $isIntegerType ? (int) $value : (float) $value;
            } else {
                return "Can not assign value type: '" . gettype($value) . "' to column type: '{$columnType}'";
            }

            if ($value < 0 && $columnTypeIsUnsigned) {
                return "Can not assign value: $value to unsigned column type.";
            }

            if ($isIntegerType) {
                $maxValue = $columnType->isTinyInt() ? 127 : (
                    $columnType->isSmallInt() ? 32767 : (
                        $columnType->isMediumInt() ? 8388607 : (
                            $columnType->isInt() ? 2147483647 : 9223372036854775807
                        )
                    )
                );

                if ($columnTypeIsUnsigned) {
                    $maxValue = ($maxValue * 2) + 1;
                    $minValue = 0;
                }

                if ($value < $minValue || $value > $maxValue) {
                    return "Value: $value out of range for column type: {$columnType}";
                }
            }
        } else {
            throw new NotImplementedException("Column type: {$columnType} is not implemented");
        }

        return true;
    }
}