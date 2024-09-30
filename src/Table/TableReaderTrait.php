<?php

declare(strict_types=1);

namespace Pst\Database\Table;

use Pst\Core\Types\Type;
use Pst\Core\Enumerable\RewindableEnumerable;
use Pst\Core\Enumerable\IRewindableEnumerable;

use Pst\Database\Validator;

use InvalidArgumentException;


trait TableReaderTrait {
    private static array $tableReaderTraitCache = [];

    /**
     * Loads sql Tables
     * 
     * @param string $schemaName 
     * @param string $tableName 
     * 
     * @return IRewindableEnumerable 
     * 
     * @throws InvalidArgumentException 
     */
    public function readTables(string $schemaName): IRewindableEnumerable {
        if (!Validator::validateSchemaName($schemaName)) {
            throw new InvalidArgumentException("Invalid schema name.: '$schemaName'");
        }

        $key = trim($schemaName);

        return static::$tableReaderTraitCache[$key] ??= RewindableEnumerable::create(
            $this->implReadTables($schemaName)->toArray(function($v) { return $v->name(); }),
            Type::class(Table::class)
        );
    }

    /**
     * Loads a sql Table
     * 
     * @param string $schemaName 
     * @param string $tableName 
     * 
     * @return Table 
     * 
     * @throws InvalidArgumentException 
     */
    public function readTable(string $schemaName, string $tableName): Table {
        if (!Validator::validateSchemaName($schemaName)) {
            throw new InvalidArgumentException("Invalid schema name.: '$schemaName'");
        } else if (!Validator::validateTableName($tableName)) {
            throw new InvalidArgumentException("Invalid table name.: '$tableName'");
        }

        $key = trim($schemaName) . "." . trim($tableName);

        return static::$tableReaderTraitCache[$key] ??= $this->implReadTables($schemaName, $tableName);
    }

    /**
     * Implementation specific read tables
     * 
     * @param string $schemaName 
     * @param null|string $tableName 
     * 
     * @return IRewindableEnumerable 
     */
    protected abstract function implReadTables(string $schemaName, ?string $tableName = null): IRewindableEnumerable;
}
