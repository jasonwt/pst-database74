<?php

declare(strict_types=1);

namespace Pst\Database\Index;

use Pst\Core\CoreObject;
use Pst\Core\Types\TypeHintFactory;
use Pst\Core\Enumerable\IRewindableEnumerable;
use Pst\Core\Enumerable\RewindableEnumerable;

use Pst\Database\Validator;
use Pst\Database\Index\IndexType;

use InvalidArgumentException;

class Index extends CoreObject  implements IIndex {
    private string $schemaName;
    private string $tableName;
    private string $name;
    
    private IndexType $type;

    private IRewindableEnumerable $columns;
    
    public function __construct(string $schemaName, string $tableName, string $name, IndexType $type, string ...$columns) {
        if (Validator::validateSchemaName($this->schemaName = $schemaName) !== true) {
            throw new InvalidArgumentException("Invalid schema name: '$schemaName'.");
        }

        if (Validator::validateTableName($this->tableName = $tableName) !== true) {
            throw new InvalidArgumentException("Invalid table name: '$tableName'.");
        }

        if (Validator::validateIndexName($this->name = $name) !== true) {
            throw new InvalidArgumentException("Invalid index name: '$name'.");
        }

        if (count($columns) === 0) {
            throw new InvalidArgumentException('No columns specified');
        }

        $this->type = $type;

        $this->columns = RewindableEnumerable::create(array_map(function($column) {
            if (!is_string($column)) {
                throw new InvalidArgumentException('Column name must be a string.');
            } else if (empty($column)) {
                throw new InvalidArgumentException('Column name cannot be empty.');
            }

            return $column;
        }, $columns), TypeHintFactory::string());
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

    public function columns(): IRewindableEnumerable {
        return $this->columns;
    }
}