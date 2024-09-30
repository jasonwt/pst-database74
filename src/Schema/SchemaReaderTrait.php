<?php

declare(strict_types=1);

namespace Pst\Database\Schema;

use Pst\Core\Types\Type;
use Pst\Core\Enumerable\RewindableEnumerable;
use Pst\Core\Enumerable\IRewindableEnumerable;

use Pst\Database\Validator;

use InvalidArgumentException;

trait SchemaReaderTrait {
    private static array $schemaReaderTraitCache = [];

    /**
     * Loads sql Schemas
     * 
     * @return IRewindableEnumerable 
     */
    public function readSchemas(): IRewindableEnumerable{
        $key = "*";
        
        return static::$schemaReaderTraitCache[$key] ??= RewindableEnumerable::create(
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
     * @return IRewindableEnumerable 
     */
    protected abstract function implReadSchemas(?string $schemaName = null): IRewindableEnumerable;
}
