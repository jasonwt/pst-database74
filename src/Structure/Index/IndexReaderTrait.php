<?php

declare(strict_types=1);

namespace Pst\Database\Structure\Index;

use Pst\Core\Types\Type;
use Pst\Core\Collections\ReadOnlyCollection;
use Pst\Core\Collections\IReadOnlyCollection;

use Pst\Database\Structure\Validator;

use InvalidArgumentException;

trait IndexReaderTrait {
    private static array $indexReaderTraitCache = [];
    /**
     * Loads a sql indexes
     * 
     * @param string $schemaName 
     * @param string $tableName 
     * 
     * @return IReadOnlyCollection 
     * 
     * @throws InvalidArgumentException 
     */
    public function readIndexes(string $schemaName, string $tableName): IReadOnlyCollection {
        if (!Validator::validateSchemaName($schemaName)) {
            throw new InvalidArgumentException("Invalid schema name.: '$schemaName'");
        } else if (!Validator::validateTableName($tableName)) {
            throw new InvalidArgumentException("Invalid table name.: '$tableName'");
        }

        $key = trim($schemaName) . "." . trim($tableName);

        return static::$indexReaderTraitCache[$key] ??= new ReadOnlyCollection (
            $this->implReadIndexes($schemaName, $tableName)->toArray(function($v) { return $v->name(); }),
            Type::class(Index::class)
        );
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

        $key = trim($schemaName) . "." . trim($tableName) . "." . trim($indexName);

        return (static::$indexReaderTraitCache[$key] ??= $this->implReadIndexes($schemaName, $tableName, $indexName));
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
