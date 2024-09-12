<?php

declare(strict_types=1);

namespace Pst\Database\Structure\Traits;

use Pst\Core\Types\Type;

use Pst\Database\Structure\Column;
use Pst\Database\Structure\Index;
use Pst\Database\Structure\Table;
use Pst\Database\Structure\ColumnDefaultValue;

trait TableRecordTrait {
    private string $TableRecordTrait__schemaName;
    private string $TableRecordTrait__tableName;

    private array $TableRecordTrait__columns = [];
    private array $TableRecordTrait__indexes = [];

    public function __construct(Table $table) {
        $this->TableRecordTrait__schemaName = $table->schemaName();
        $this->TableRecordTrait__tableName = $table->name();

        $this->TableRecordTrait__columns = $table->columns()->select(function(Column $column, $index) {
            $columnIsNullable = $column->isNullable();
            $columnType = $column->type()->toTypeHint($columnIsNullable);

            $columnDefaultValue = $column->defaultValue();

            if ($columnDefaultValue instanceof ColumnDefaultValue) {
                if ($columnDefaultValue->value() == "NULL") {
                    $columnDefaultValue = "null";
                } else if ($columnDefaultValue->value() == "NONE") {
                    $columnDefaultValue = $columnIsNullable ? null : Type::typeOf($columnType->fullName())->defaultValue();
                } else {
                    $columnDefaultValue = "ColumnDefaultValue::" . $columnDefaultValue->value() . "()";
                }
            }

            return [
                "column" => $column,
                "phpType" => $columnType->fullName(),
                "value" => $columnDefaultValue,
                "defaultValue" => $columnDefaultValue,
            ];
        }, function (Column $column, $key) { return $column->name();})->toArray();

        $this->TableRecordTrait__indexes = $table->indexes()->select(function(Index $index, $key) { 
            return $index; 
        }, function(Index $index, $key) { return $index->name(); })->toArray();
    }

    public function schemaName(): string {
        return $this->TableRecordTrait__schemaName;
    }

    public function tableName(): string {
        return $this->TableRecordTrait__tableName;
    }

    public function getColumnNames(): array {
        return array_keys($this->TableRecordTrait__columns);
    }

    public function getColumnValue(string $columnName) {
        if (!isset($this->TableRecordTrait__columns[$columnName])) {
            throw new \InvalidArgumentException("Column: '$columnName' does not exist in table: '{$this->TableRecordTrait__tableName}'");
        }

        return $this->TableRecordTrait__columns[$columnName]["value"];
    }

    public function getColumnValues(): array {
        return array_map(function($column) {
            return $column["value"];
        }, $this->TableRecordTrait__columns);
    }

    public function setColumnValue(string $columnName, $value) {
        if (!isset($this->TableRecordTrait__columns[$columnName])) {
            throw new \InvalidArgumentException("Column: '$columnName' does not exist in table: '{$this->TableRecordTrait__tableName}'");
        }

        if (($columnValueValidation = static::validateColumnValue($this->TableRecordTrait__columns[$columnName]["column"], $value)) !== true) {
            throw new \InvalidArgumentException("Column: '$columnName' value validation failed: $columnValueValidation");
        }

        $this->TableRecordTrait__columns[$columnName]["value"] = $value;
    }

    public function setColumnValues(array $values) {
        foreach ($values as $columnName => $value) {
            $this->setColumnValue($columnName, $value);
        }
    }

    protected static function validateColumnValue(Column $column, $value) {
        $columnType = $column->type();

        if ($value === null) {
            if (!$column->isNullable()) {
                return "Value cannot be null";
            }
            
            return true;
        }

        if ($columnType->isStringType()) {
            if ($column->length() === null) {
                return "Column length is required for ENUM, SET, CHAR, BINARY, TEXT AND BLOB types";
            }

            if ($columnType->isStringType()) {
                if (!is_string($value)) {
                    return "Can not assign value type: '" . gettype($value) . "' to string column type";
                }

                if (strlen($value) > $column->length()) {
                    return "Value length: " . strlen($value) . " exceeds maximum character length: {$column->length()}";
                }
            }

        } else if ($columnType->isNumericType()) {
            if ($column->length() !== null) {
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

        return true;
    }
}