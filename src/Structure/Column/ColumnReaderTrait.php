<?php

declare(strict_types=1);

namespace Pst\Database\Structure\Column;

use Pst\Core\Types\Type;
use Pst\Core\Collections\IEnumerable;
use Pst\Core\Collections\ReadOnlyCollection;
use Pst\Core\Collections\IReadOnlyCollection;

use Pst\Database\Structure\Validator;

use InvalidArgumentException;

trait ColumnReaderTrait {
    private static array $columnReaderTraitCache = [];

    /**
     * Loads sql columns
     * 
     * @param string $schemaName 
     * @param string $tableName 
     * 
     * @return IEnumerable 
     * 
     * @throws InvalidArgumentException 
     */
    public function readColumns(string $schemaName, string $tableName): IReadOnlyCollection {
        if (!Validator::validateSchemaName($schemaName)) {
            throw new InvalidArgumentException("Invalid schema name.: '$schemaName'");
        } else if (!Validator::validateTableName($tableName)) {
            throw new InvalidArgumentException("Invalid table name.: '$tableName'");
        }

        $key = trim($schemaName) . "." . trim($tableName);

        return static::$columnReaderTraitCache[$key] ??= new ReadOnlyCollection(
            $this->implReadColumns($schemaName, $tableName)->toArray(function($v) { return $v->name(); }), 
            Type::class(Column::class)
        );
    }

    /**
     * Loads a sql column
     * 
     * @param string $schemaName 
     * @param string $tableName 
     * @param string $columnName 
     * 
     * @return Column 
     * 
     * @throws InvalidArgumentException 
     */
    public function readColumn(string $schemaName, string $tableName, string $columnName): Column {
        if (!Validator::validateSchemaName($schemaName)) {
            throw new InvalidArgumentException("Invalid schema name.: '$schemaName'");
        } else if (!Validator::validateTableName($tableName)) {
            throw new InvalidArgumentException("Invalid table name.: '$tableName'");
        } else if (!Validator::validateColumnName($columnName)) {
            throw new InvalidArgumentException("Invalid column name.: '$columnName'");
        }

        $key = trim($schemaName) . "." . trim($tableName) . "." . trim($columnName);

        return (static::$columnReaderTraitCache[$key] ??= $this->implReadColumns($schemaName, $tableName, $columnName));
    }

    /**
     * Implementation specific load columns
     * 
     * @param string $schemaName 
     * @param string $tableName 
     * @param null|string $columnName 
     * 
     * @return IEnumerable|Column 
     */
    protected abstract function implReadColumns(string $schemaName, string $tableName, ?string $columnName = null);
}
