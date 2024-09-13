<?php

declare(strict_types=1);

namespace Pst\Database\Structure\Index;

use Pst\Core\Collections\IEnumerable;

use Pst\Database\Structure\Validator;

use InvalidArgumentException;

trait IndexReaderTrait {    
    /**
     * Loads a sql indexes
     * 
     * @param string $schemaName 
     * @param string $tableName 
     * 
     * @return IEnumerable 
     * 
     * @throws InvalidArgumentException 
     */
    public function readIndexes(string $schemaName, string $tableName): IEnumerable {
        if (!Validator::validateSchemaName($schemaName)) {
            throw new InvalidArgumentException("Invalid schema name.: '$schemaName'");
        } else if (!Validator::validateTableName($tableName)) {
            throw new InvalidArgumentException("Invalid table name.: '$tableName'");
        }

        return $this->implReadIndexes($schemaName, $tableName);
    }

    /**
     * Loads a sql index
     * 
     * @param string $schemaName 
     * @param string $tableName 
     * @param string $indexName 
     * 
     * @return Index 
     * 
     * @throws InvalidArgumentException 
     */
    public function readIndex(string $schemaName, string $tableName, string $indexName): Index {
        if (!Validator::validateSchemaName($schemaName)) {
            throw new InvalidArgumentException("Invalid schema name.: '$schemaName'");
        } else if (!Validator::validateTableName($tableName)) {
            throw new InvalidArgumentException("Invalid table name.: '$tableName'");
        } else if (!Validator::validateIndexName($indexName)) {
            throw new InvalidArgumentException("Invalid index name.: '$indexName'");
        }

        return $this->implReadIndexes($schemaName, $tableName, $indexName);
    }

    /**
     * Implementation specific load indexes
     * 
     * @param string $schemaName 
     * @param string $tableName 
     * @param null|string $indexName 
     * 
     * @return IEnumerable|Index 
     */
    protected abstract function implReadIndexes(string $schemaName, string $tableName, ?string $indexName = null);
}
