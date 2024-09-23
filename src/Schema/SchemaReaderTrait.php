<?php

declare(strict_types=1);

namespace Pst\Database\Schema;

use Pst\Core\Types\Type;
use Pst\Core\Collections\ReadonlyCollection;
use Pst\Core\Collections\IReadonlyCollection;

use Pst\Database\Validator;

use InvalidArgumentException;

trait SchemaReaderTrait {
    private static array $schemaReaderTraitCache = [];

    /**
     * Loads sql Schemas
     * 
     * @return IReadonlyCollection 
     */
    public function readSchemas(): IReadonlyCollection{
        $key = "*";
        
        return static::$schemaReaderTraitCache[$key] ??= new ReadonlyCollection (
            $this->implReadSchemas()->toArray(function($v) { return $v->name(); }),
            Type::class(Schema::class)
        );
    }

    /**
     * Loads sql Schema
     * 
     * @param string $schemaName 
     * 
     * @return IEnumerable 
     * 
     * @throws InvalidArgumentException 
     */
    public function readSchema(string $schemaName): Schema {
        if (!Validator::validateSchemaName($schemaName)) {
            throw new InvalidArgumentException("Invalid schema name.: '$schemaName'");
        }

        $key = trim($schemaName);

        return static::$schemaReaderTraitCache[$key] ??= $this->implReadSchemas($schemaName);
    }

    /**
     * Implementation specific load schemas
     * 
     * @param null|string $schemaName 
     * 
     * @return IEnumerable|Schema 
     */
    protected abstract function implReadSchemas(?string $schemaName = null);
}
