<?php

declare(strict_types=1);

namespace Pst\Database\Structure\Schema;

use Pst\Core\Collections\IEnumerable;

use Pst\Database\Structure\Validator;

use InvalidArgumentException;

trait SchemaReaderTrait {
    /**
     * Loads sql Schemas
     * 
     * @return IEnumerable 
     */
    public function readSchemas(): IEnumerable{
        return $this->implReadSchemas();
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

        return $this->implReadSchemas($schemaName);
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
