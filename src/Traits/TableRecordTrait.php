<?php

declare(strict_types=1);

namespace Pst\Database\Traits;

use Pst\Core\Types\TypeHintFactory;
use Pst\Core\Collections\Enumerator;
use Pst\Core\Collections\IEnumerable;

use Pst\Database\Connections\IDatabaseConnection;
use Pst\Database\Structure\Index;
use Pst\Database\Structure\Column;
use Pst\Database\Structure\ColumnDefaultValue;
use Pst\Database\Structure\Readers\ITableReader;

use Pst\Core\Exceptions\LogicException;
use Pst\Core\Exceptions\NotSupportedException;

use InvalidArgumentException;

trait TableRecordTrait {
    private static ?array $tableRecordTraitColumnsArray = null;
    private static ?array $tableRecordTraitIndexesArray = null;

    private array $tableColumnValues = [];

    protected abstract static function schemaName(): string;
    protected abstract static function tableName(): string;
    protected abstract static function getDatabaseConnection(): ?IDatabaseConnection;

    

    /**
     * Creates a new table record.
     * 
     * @param array $values The values of the table record.
     * 
     * @return static The new table record.
     */
    public function __construct(array $values = []) {
        $this->tableColumnValues = array_reduce(array_keys($values), function($carry, $columnName) use ($values){
            $columnValue = $values[$columnName];

            if (!array_key_exists($columnName, $carry)) {
                throw new LogicException("Column: '$columnName' does not exist in table with columns: '" . implode(",", array_keys(self::getDefaultValues())) . "'.");
            }

            if (!self::validateColumnValue($columnName, $columnValue)) {
                throw new InvalidArgumentException("Invalid value: '$columnValue' for column: '$columnName'.");
            }

            $carry[$columnName] = $columnValue;
            return $carry;
        }, self::getDefaultValues());
    }

    /**
     * Gets the value of a column.
     * 
     * @param string $columnName The name of the column.
     * 
     * @return mixed The value of the column.
     */
    protected function getColumnValue(string $columnName) {
        if (!array_key_exists($columnName = trim($columnName), $this->tableColumnValues)) {
            throw new InvalidArgumentException("Column: '$columnName' does not exist in table.");
        }

        return $this->tableColumnValues[$columnName];
    }

    /**
     * Sets the value of a column.
     * 
     * @param string $columnName The name of the column.
     * @param mixed $value The value to set.
     * 
     * @throws InvalidArgumentException
     */
    protected function setColumnValue(string $columnName, $value) {
        if (!array_key_exists($columnName = trim($columnName), $this->tableColumnValues)) {
            throw new InvalidArgumentException("Column: '$columnName' does not exist in table.");
        }

        if (($error = self::validateColumnValue($columnName, $value)) !== true) {
            throw new InvalidArgumentException($error);
        }

        $this->tableColumnValues[$columnName] = $value;
    }







    /**
     * Gets the columns cache of the table or loads it.
     * 
     * @return array The columns of the table.
     */
    private static function tableRecordTraitColumns(): array {
        return self::$tableRecordTraitColumnsArray ??= array_reduce(static::implColumns(), function($columns, Column $column) {
            $columns[$column->name()] = $column;
            return $columns;
        }, []);
    }

    /**
     * Gets the indexes cache of the table or loads it.
     * 
     * @return array The indexes of the table.
     */
    private static function tableRecordTraitIndexes(): array {
        return self::$tableRecordTraitIndexesArray ??= array_reduce(static::implIndexes(), function($indexes, Index $index) {
            $indexes[$index->name()] = $index;
            return $indexes;
        }, []);
    }

    /**
     * Reads a record from the table.
     * 
     * @param string $columnIndexValue The value of the column to index by.
     * @param string|null $columnIndexName The name of the column to index by.  If null it will try to match on any primary or unique key.
     * 
     * @return array|null The record read from the table.
     */
    private static function getReadQueryResult($columnIndexValue, ?string $columnIndexName = null): ?array {
        $searchColumnNames = is_string($columnIndexValue) ? [$columnIndexValue] : array_unique(array_reduce(static::$tableRecordTraitIndexesArray, function($carry, Index $index) {
            if (($indexType = $index->type())->isPrimary()) {
                foreach ($index->columns() as $column) {
                    array_unshift($carry, $column);
                }
                return $carry;
            } else if ($indexType->isUnique()) {
                foreach ($index->columns() as $column) {
                    $carry[] = $column;
                }
            }

            return $carry;
        }, []));

        if (count($searchColumnNames) == 0) {
            throw new LogicException("No unique or primary key columns found in table.");
        }

        $whereClause = implode(" AND ", array_map(function($columnName) {
            if (!array_key_exists($columnName, self::tableRecordTraitColumns())) {
                throw new InvalidArgumentException("Column: '$columnName' does not exist in table.");
            }
            return "$columnName = :columnIndexValue";
        }, $searchColumnNames));

        $sql = "SELECT * FROM " . self::schemaName() . "." . self::tableName() . " WHERE $whereClause";

        $results = static::getDatabaseConnection()->query($sql, ["columnIndexValue" => $columnIndexValue]);

        if (count($resultsArray = $results->fetchAll()) === 0) {
            return null;
        } else if (count($resultsArray) > 1) {
            throw new LogicException("Multiple records found for value: '$columnIndexValue'");
        }

        return $resultsArray[0];
    }

    /**
     * Reads a list of records from the table.
     * 
     * @param string $columnIndexValue The value of the column to index by.
     * @param string|null $columnIndexName The name of the column to index by.
     * 
     * @return static|null The record read from the table.
     */
    private static function getListQueryResults(array $whereConditions = []): array {
        $wheres = [];

        foreach ($whereConditions as $columnName => $columnValue) {
            if (!array_key_exists($columnName, self::tableRecordTraitColumns())) {
                throw new InvalidArgumentException("Column: '$columnName' does not exist in table.");
            }

            $wheres[] = "$columnName = :$columnName";
        }

        $query = "SELECT * FROM " . self::schemaName() . "." . self::tableName();

        if (count($wheres) > 0) {
            $query .= " WHERE " . implode(" AND ", $wheres);
        }

        return static::getDatabaseConnection()->query($query, $whereConditions)->fetchAll();
    }

    /**
     * Gets the table reader.
     * 
     * @return ITableReader|null The table reader.
     */
    protected static function getTableReader(): ?ITableReader {
        return null;
    }

    /**
     * Gets the columns of the table.
     * 
     * @return array The columns of the table.
     */
    protected static function implColumns(): array {
        if (($tableReader = static::getTableReader()) === null) {
            throw new NotSupportedException("Table reader is required to load columns.");
        }

        return $tableReader->readColumns(static::schemaName(), static::tableName())->toArray();
    }

    /**
     * Gets the indexes of the table.
     * 
     * @return array The indexes of the table.
     */
    protected static function implIndexes(): array {
        if (($tableReader = static::getTableReader()) === null) {
            throw new NotSupportedException("Table reader is required to load columns.");
        }

        return $tableReader->readIndexes(static::schemaName(), static::tableName())->toArray();
    }


    /**
     * Gets the columns of the table.
     * 
     * @return IEnumerable The columns of the table.
     */
    public static function columns(): IEnumerable {
        return Enumerator::new(self::tableRecordTraitColumns(), TypeHintFactory::tryParse(Column::class));
    }
    
    /**
     * Gets the indexes of the table.
     * 
     * @return IEnumerable The indexes of the table.
     */
    public static function indexes(): IEnumerable {
        return Enumerator::new(self::tableRecordTraitIndexes(), TypeHintFactory::tryParse(Index::class));
    }

    /**
     * Gets the default values for the columns of the table.
     * 
     * @return array The default values for the columns of the table.
     * 
     * @throws LogicException
     * @throws NotSupportedException
     * @throws InvalidArgumentException
     */
    public static function getDefaultValues(): array {
        return array_reduce(self::tableRecordTraitColumns(), function (array $carry, Column $column) {
            $columnType = $column->type();

            if (($columnDefaultValue = $column->defaultValue()) instanceof ColumnDefaultValue) {
                if ($columnDefaultValue->value() == "NULL") {
                    $carry[$column->name()] = null;
                } else if ($columnDefaultValue->value() == "NONE") {
                    if ($columnType->isIntegerType()) {
                        $carry[$column->name()] = 0;
                    } else if ($columnType->isStringType()) {
                        $carry[$column->name()] = "";
                    } else if ($columnType->isDecimalType()) {
                        $carry[$column->name()] = 0.0;
                    } else {
                        throw new LogicException("Unsupported column type: '{$columnType}'");
                    }
                } else if ($columnDefaultValue->value() == "CURRENT_TIMESTAMP") {
                    return $carry[$column->name()] = date("Y-m-d H:i:s");
                } else {
                    throw new LogicException("Unsupported default value: '{$columnDefaultValue->value()}'");
                }
            } else if ($columnDefaultValue === null) {
                throw new LogicException("Column default value should not be null.  Use ColumnDefaultValue::NULL().");
            } else if (!is_int($columnDefaultValue) && !is_float($columnDefaultValue) && !is_string($columnDefaultValue)) {
                throw new LogicException("Unsupported default value type: '" . gettype($columnDefaultValue) . "'");
            } else {
                if ($columnType->isIntegerType()) {
                    $columnDefaultValue = (int) $columnDefaultValue;
                } else if ($columnType->isDecimalType()) {
                    $columnDefaultValue = (float) $columnDefaultValue;
                } else if ($columnType->isStringType()) {
                    $columnDefaultValue = (string) $columnDefaultValue;
                } else {
                    throw new LogicException("Unsupported column type: '{$columnType}'");
                }
                
                $carry[$column->name()] = $columnDefaultValue;
            }

            return $carry;
        }, []);
    }

    /**
     * Validates a column value.
     * 
     * @param Column|string $column The name of the column.
     * @param mixed $value The value to validate.
     * 
     * @return bool|string True if the value is valid; otherwise, an error message.
     */
    public static function validateColumnValue($column, $value) {
        $columns = self::tableRecordTraitColumns();

        if (is_string($column)) {
            if (empty($column = trim($column))) {
                throw new InvalidArgumentException("Column cannot be empty.");
            } else if (($column = $columns[$column] ?? null) === null) {
                throw new InvalidArgumentException("Column: '$column' does not exist in table.");
            }
        } else if (!$column instanceof Column) {
            throw new InvalidArgumentException("Column must be a string or an instance of Column.");
        }

        if ($value === null) {
            if (!$column->isNullable()) {
                return "Value cannot be null";
            }
            
            return true;
        }

        $columnType = $column->type();

        if ($columnType->isStringType()) {
            if ($column->length() === null) {
                throw new LogicException("Column length is required for ENUM, SET, CHAR, BINARY, TEXT AND BLOB types");
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
                throw new LogicException("Column length is only allowed for ENUM, SET, CHAR, BINARY, TEXT AND BLOB types");
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