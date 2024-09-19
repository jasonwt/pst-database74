<?php

declare(strict_types=1);

namespace Pst\Database\Traits;

use Pst\Core\Types\Type;
use Pst\Core\Types\TypeHintFactory;
use Pst\Core\Collections\ReadOnlyCollection;
use Pst\Core\Collections\IReadOnlyCollection;

use Pst\Database\Enums\ColumnDefaultValue;
use Pst\Database\Structure\Validator;
use Pst\Database\Structure\Column\Column;
use Pst\Database\Structure\Table\Table;
use Pst\Database\Structure\Schema\Schema;
use Pst\Database\Structure\Schema\ISchemaReader;
use Pst\Database\Connections\IDatabaseConnection;

use Pst\Core\Exceptions\NotImplementedException;

trait DatabaseTableRecordTrait {
    private static array $databaseTableRecordTrait = [
        "lastTryCreateErrors" => null,
        "lastTryReadErrors" => null,
        "lastTryUpdateErrors" => null,
        "lastTryDeleteErrors" => null,
        "lastTryListErrors" => null,

        "uniqueColumns" => null
    ];

    private array $databaseTableRecordTraitPropertyValues = [];

    protected static abstract function db(): IDatabaseConnection;
    protected static abstract function dbs(): ISchemaReader;

    protected function __construct(array $columnValues) {
        $this->databaseTableRecordTraitPropertyValues = static::dbs()->readColumns(static::schemaName(), static::tableName())->
            select(function ($column) use ($columnValues) {
                if (array_key_exists($column->name(), $columnValues)) {
                    return $columnValues[$column->name()];
                }

                if ($column->defaultValue() instanceof ColumnDefaultValue) {
                    if ($column->defaultValue() == ColumnDefaultValue::NONE()) {
                        throw new \Exception("Column '{$column->name()}' value is required.");
                    } else if ($column->defaultValue() == ColumnDefaultValue::NULL()) {
                        return null;
                    } else if ($column->defaultValue() == ColumnDefaultValue::CURRENT_TIMESTAMP()) {
                        return null;
                    } else if ($column->defaultValue() == ColumnDefaultValue::UUID()) {
                        throw new NotImplementedException("UUID not implemented.");
                    }
                } else {
                    return $column->defaultValue();
                }
                //return $columnValues[$column->name()] ?? $column->defaultValue();
        }, TypeHintFactory::mixed())->toArray();
    }

    public function getColumnValues(): array {
        return $this->databaseTableRecordTraitPropertyValues;
    }

    public function setColumnValues(array $columnValues): void {
        foreach ($columnValues as $columnName => $value) {
            if (!isset($this->databaseTableRecordTraitPropertyValues[$columnName])) {
                throw new \Exception("Invalid column name.");
            }

            $this->databaseTableRecordTraitPropertyValues[$columnName] = $value;
        }
    }

    /**
     * @param string $columnName
     * @param array|IReadOnlyCollection $input
     * 
     * @return mixed
     * 
     * @throws \Exception
     */
    protected static function toDatabaseValue(string $columnName, $input) {
        if (!isset(static::columnsArray()[$columnName = trim($columnName)])) {
            throw new \Exception("Invalid column name.");
        }

        return $input;
    }

    protected function toDatabaseValues(array $input): array {
        return array_reduce(array_keys($input), function ($acc, $columnName) use ($input) {
            $acc[$columnName] = $this->toDatabaseValue($columnName, $input[$columnName]);
            return $acc;
        }, []);
    }

    protected function fromDatabaseValue(string $columnName, $input) {
        if (!isset(static::columnsArray()[$columnName = trim($columnName)])) {
            throw new \Exception("Invalid column name.");
        }
        
        return $input;
    }

    protected function fromDatabaseValues(array $input): array {
        return array_reduce(array_keys($input), function ($acc, $columnName) use ($input) {
            $acc[$columnName] = $this->fromDatabaseValue($columnName, $input[$columnName]);
            return $acc;
        }, []);
    }
    

    public static abstract function schemaName(): string;
    public static abstract function tableName(): string;

    public static function lastTryCreateErrors(): ?array {
        return static::$databaseTableRecordTrait["lastTryCreateErrors"];
    }

    public static function lastTryReadErrors(): ?array {
        return static::$databaseTableRecordTrait["lastTryReadErrors"];
    }

    public static function lastTryUpdateErrors(): ?array {
        return static::$databaseTableRecordTrait["lastTryUpdateErrors"];
    }

    public static function lastTryDeleteErrors(): ?array {
        return static::$databaseTableRecordTrait["lastTryDeleteErrors"];
    }

    public static function lastTryListErrors(): ?array {
        return static::$databaseTableRecordTrait["lastTryListErrors"];
    } 

    public static function schema(): Schema {
        return static::dbs()->ReadSchema(static::schemaName());
    }

    public static function table(): Table {
        return static::dbs()->ReadTable(static::schemaName(), static::tableName());
    }

    public static function columns(): IReadOnlyCollection {
        return static::dbs()->readColumns(static::schemaName(), static::tableName());
    }

    public static function indexes(): IReadOnlyCollection {
        return static::dbs()->readIndexes(static::schemaName(), static::tableName());
    }

    public static function uniqueColumns(): IReadOnlyCollection {
        return static::$databaseTableRecordTrait["uniqueColumns"] ??= 
            static::dbs()->
            readColumns(static::schemaName(), static::tableName())->
            where(function ($column) { return $column->indexType() !== null && $column->indexType()->isUnique(); })->
            toReadonlyCollection();
    }

    

    public static function tryCreate(array $columnValues): ?self {
        $staticColumnsArray = static::dbs()->readColumns(static::schemaName(), static::tableName());
        $uniqueColumns = static::uniqueColumns();

        $errors = [];

        foreach ($columnValues as $columnName => $value) {
            if (!isset($staticColumnsArray[$columnName])) {
                $errors[$columnName] = "Invalid column name.";
            }
        }

        foreach ($staticColumnsArray as $columnName => $column) {
            $columnType = $column->type();
            $columnDefaultValue = $column->defaultValue();

            if (!isset($columnValues[$columnName])) {
                $columnValues[$columnName] = $columnDefaultValue;
            }

            $columnValue = $columnValues[$columnName];

            if ($columnValue instanceof ColumnDefaultValue) {
                if ($columnValue == ColumnDefaultValue::NONE()) {
                    if ($columnType->isAutoIncrementing()) {
                        unset($columnValues[$columnName]);
                    } else {
                        $errors[$columnName] = "Column '{$columnName}' value is required.";
                    }
                } else if ($columnValue == ColumnDefaultValue::NULL()) {
                    $columnValues[$columnName] = null;
                } else if ($columnValue == ColumnDefaultValue::CURRENT_TIMESTAMP()) {
                    unset($columnValues[$columnName]);
                } else if ($columnValue == ColumnDefaultValue::UUID()) {
                    throw new NotImplementedException("UUID not implemented.");
                }
            } else {
                if (($columnValueValidation = Validator::validateColumnValue($column, $columnValue)) !== true) {
                    $errors[$columnName] = $columnValueValidation;
                }
            }
        }

        if (count($errors) === 0 && count($uniqueColumns) > 0) {
            foreach ($uniqueColumns as $columnName => $column) {
                if (array_key_exists($columnName, $columnValues) === false) {
                    continue;
                }

                if (($existingRow = static::tryRead([$columnName => $columnValues[$columnName]])) !== null) {
                    $errors[$columnName] = "Record already exists for unique column: '{$columnName}' with value: '{$columnValues[$columnName]}'.";
                }
            }
        }

        if (count($errors) > 0) {
            static::$databaseTableRecordTrait["lastTryCreateErrors"] = $errors;
            return null;
        }

        $insertQuery = "INSERT INTO " . static::tableName();
        $insertQuery .= " (" . implode(", ", array_keys($columnValues)) . ")";
        $insertQuery .= " VALUES (" . implode(", ", array_map(function ($value) { return "?";}, $columnValues)) . ")";

        static::db()->query($insertQuery, array_values($columnValues));

        if (($insertId = static::db()->lastInsertId()) !== null) {
            foreach ($uniqueColumns as $columnName => $column) {
                if ($column->type()->isAutoIncrementing()) {
                    if (($instance = static::tryRead([$columnName => $insertId])) !== null) {
                        return $instance;
                    }

                    static::$databaseTableRecordTrait["lastTryCreateErrors"] = static::lastTryReadErrors();
                    return null;
                }
            }
        }

        if (($instance = static::tryRead($columnValues)) !== null) {
            return $instance;
        }

        static::$databaseTableRecordTrait["lastTryCreateErrors"] = static::lastTryReadErrors();
        return null;
    }

    public static function create(array $columnValues): self {
        $record = static::tryCreate($columnValues);

        if ($record === null) {
            throw new \Exception("Failed to create record.\n" . implode("\n", static::lastTryCreateErrors()));
        }

        return $record;
    }

    public static function tryRead(array $predicate): ?self {
        $staticColumnsArray = static::dbs()->readColumns(static::schemaName(), static::tableName());

        $errors = [];

        foreach ($predicate as $columnName => $value) {
            if (!isset($staticColumnsArray[$columnName])) {
                $errors[$columnName] = "Invalid column name.";
            }

            if (($columnValueValidation = Validator::validateColumnValue($staticColumnsArray[$columnName], $value)) !== true) {
                $errors[$columnName] = $columnValueValidation;
            }
        }

        if (count($errors) > 0) {
            static::$databaseTableRecordTrait["lastTryReadErrors"] = $errors;
            return null;
        }

        $selectQuery = "SELECT * FROM " . static::tableName();
        $selectQuery .= " WHERE " . implode(" AND ", array_map(function ($columnName) { return "$columnName = ?"; }, array_keys($predicate)));

        $records = static::db()->query($selectQuery, array_values($predicate))->fetchAll();

        if (count($records) === 0) {
            return null;
        } else if (count($records) > 1) {
            throw new \Exception("Multiple records found.");
        }

        return new static($records[0]);
    }

    public static function read(array $predicate): self {
        $record = static::tryRead($predicate);

        if ($record === null) {
            throw new \Exception("Failed to read record.");
        }

        return $record;
    }

    public static function tryUpdate(self $record, array $updateColumnValues): ?self {
        throw new \Exception("Not implemented.");
    }

    public static function update(self $record, array $updateColumnValues): self {
        $record = static::tryUpdate($record, $updateColumnValues);

        if ($record === null) {
            throw new \Exception("Failed to update record.");
        }

        return $record;
    }

    public static function tryDelete(self $record): bool {
        throw new \Exception("Not implemented.");
    }

    public static function delete(self $record): void {
        if (static::tryDelete($record) === false) {
            throw new \Exception("Failed to delete record.");
        }
    }

    public static function tryList(array $predicate = [], int $take = 0, int $skip = 0): ?IReadOnlyCollection {
        $staticColumnsArray = static::dbs()->readColumns(static::schemaName(), static::tableName());

        $errors = [];

        if ($take < 0) {
            $errors["take"] = "Take must be greater than or equal to 0.";    
        }

        if ($skip < 0) {
            $errors["skip"] = "Skip must be greater than or equal to 0.";    
        }

        foreach ($predicate as $columnName => $value) {
            if (!isset($staticColumnsArray[$columnName])) {
                $errors[$columnName] = "Invalid column name.";
            }

            if (($columnValueValidation = Validator::validateColumnValue($staticColumnsArray[$columnName], $value)) !== true) {
                $errors[$columnName] = $columnValueValidation;
            }
        }

        if (count($errors) > 0) {
            static::$databaseTableRecordTrait["lastTryListErrors"] = $errors;
            return null;
        }
        
        $selectQuery = "SELECT * FROM " . static::tableName();

        if (count($predicate) > 0) {
            $selectQuery .= " WHERE " . implode(" AND ", array_map(function ($columnName) { return "$columnName = ?"; }, array_keys($predicate)));
        }

        if ($take > 0 && $skip > 0) {
            $selectQuery .= " LIMIT $take, $skip";
        } else if ($take > 0) {
            $selectQuery .= " LIMIT $take";
        } else if ($skip > 0) {
            $selectQuery .= " LIMIT $skip, 18446744073709551615";
        }

        $records = static::db()->query($selectQuery, array_values($predicate))->fetchAll();

        return ReadOnlyCollection::new(array_map(function ($record) { return new static($record); }, $records), Type::class(static::class));
    }

    public static function list(array $predicate = [], int $skip = 0, int $take = 0): IReadOnlyCollection {
        $records = static::tryList($predicate, $skip, $take);

        if ($records === null) {
            throw new \Exception("Failed to list records.");
        }

        return $records;
    }
}