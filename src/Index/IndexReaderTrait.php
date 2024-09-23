<?php

declare(strict_types=1);

namespace Pst\Database\Index;

use Pst\Core\Types\Type;
use Pst\Core\Collections\ReadonlyCollection;
use Pst\Core\Collections\IReadonlyCollection;

use Pst\Database\Validator;

use InvalidArgumentException;

trait IndexReaderTrait {
    private static array $indexReaderTraitCache = [];
    /**
     * Loads a sql indexes
     * 
     * @param string $schemaName 
     * @param string $tableName 
     * 
     * @return IReadonlyCollection 
     * 
     * @throws InvalidArgumentException 
     */
    public function readIndexes(string $schemaName, string $tableName): IReadonlyCollection {
        if (!Validator::validateSchemaName($schemaName)) {
            throw new InvalidArgumentException("Invalid schema name.: '$schemaName'");
        } else if (!Validator::validateTableName($tableName)) {
            throw new InvalidArgumentException("Invalid table name.: '$tableName'");
        }

        $key = trim($schemaName) . "." . trim($tableName);

        return static::$indexReaderTraitCache[$key] ??= new ReadonlyCollection (
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
