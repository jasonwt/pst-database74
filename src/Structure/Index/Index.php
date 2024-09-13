<?php

declare(strict_types=1);

namespace Pst\Database\Structure\Index;

use Pst\Core\CoreObject;
use Pst\Core\Types\TypeHintFactory;
use Pst\Core\Collections\Enumerator;
use Pst\Core\Collections\IEnumerable;

use Pst\Database\Enums\IndexType;
use Pst\Database\Structure\Validator;

use InvalidArgumentException;

class Index extends CoreObject {
    private string $schemaName;
    private string $tableName;
    private string $name;
    private IndexType $type;

    private array $columns;
    
    public function __construct(string $schemaName, string $tableName, string $name, IndexType $type, array $columns) {
        if (Validator::validateSchemaName($this->schemaName = $schemaName) !== true) {
            throw new \InvalidArgumentException("Invalid schema name: '$schemaName'.");
        }

        if (Validator::validateTableName($this->tableName = $tableName) !== true) {
            throw new \InvalidArgumentException("Invalid table name: '$tableName'.");
        }

        if (Validator::validateIndexName($this->name = $name) !== true) {
            throw new \InvalidArgumentException("Invalid index name: '$name'.");
        }

        $this->type = $type;

        $this->columns = array_map(function($column) {
            if (!is_string($column)) {
                throw new InvalidArgumentException('Column name must be a string.');
            } else if (empty($column)) {
                throw new InvalidArgumentException('Column name cannot be empty.');
            }

            return $column;
        }, $columns);

        if (count($this->columns) === 0) {
            throw new InvalidArgumentException('No columns specified');
        }
    }

    public function schemaName(): string {
        return $this->schemaName;
    }

    public function tableName(): string {
        return $this->tableName;
    }

    public function name(): string {
        return $this->name;
    }

    public function type(): IndexType {
        return $this->type;
    }

    public function columns(): IEnumerable {
        return Enumerator::new($this->columns, TypeHintFactory::string());
    }
}