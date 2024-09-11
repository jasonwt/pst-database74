<?php

declare(strict_types=1);

namespace Pst\Database\Structure\Readers\Traits;

use Pst\Core\Collections\IEnumerable;

use Pst\Database\Structure\Table;
use Pst\Database\Validator;

use InvalidArgumentException;

trait TableReaderTrait {
    /**
     * Loads sql Tables
     * 
     * @param string $schemaName 
     * @param string $tableName 
     * 
     * @return IEnumerable 
     * 
     * @throws InvalidArgumentException 
     */
    public function readTables(string $schemaName): IEnumerable {
        if (!Validator::validateSchemaName($schemaName)) {
            throw new InvalidArgumentException("Invalid schema name.: '$schemaName'");
        }

        return $this->implReadTables($schemaName);
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

        return $this->implReadTables($schemaName, $tableName);
    }

    /**
     * Implementation specific read tables
     * 
     * @param string $schemaName 
     * @param null|string $tableName 
     * 
     * @return IEnumerable|Table 
     */
    protected abstract function implReadTables(string $schemaName, ?string $tableName = null);
}
