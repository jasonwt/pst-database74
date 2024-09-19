<?php

declare(strict_types=1);

namespace Pst\Database\Structure\Table;

use Pst\Core\CoreObject;
use Pst\Core\Types\Type;
use Pst\Core\Collections\ReadOnlyCollection;
use Pst\Core\Collections\IReadOnlyCollection;

use Pst\Database\Enums\IndexType;
use Pst\Database\Structure\Validator;
use Pst\Database\Structure\Index\Index;
use Pst\Database\Structure\Column\Column;

use InvalidArgumentException;

class Table extends CoreObject {
    private static array $cache = [];

    private string $schemaName;
    private string $name;

    private IReadOnlyCollection $columns;
    private IReadOnlyCollection $indexes;

    public function __construct(string $schemaName, string $name, array $columns = [], array $indexes = []) {
        if (Validator::validateSchemaName($this->schemaName = $schemaName) !== true) {
            throw new \InvalidArgumentException("Invalid schema name: '$schemaName'.");
        }

        if (Validator::validateTableName($this->name = $name) !== true) {
            throw new \InvalidArgumentException("Invalid table name: '$name'.");
        }

        $this->columns = ReadOnlyCollection::new(array_map(function($column) {
            if (!$column instanceof Column) {
                throw new InvalidArgumentException('Column must be an instance of Column.');
            }

            return $column;
        }, $columns), Type::class(Column::class));

        $this->indexes = ReadOnlyCollection::new(array_map(function($index) {
            if (!$index instanceof Index) {
                throw new InvalidArgumentException('Index must be an instance of Index.');
            }

            return $index;
        }, $indexes), Type::class(Index::class));
    }

    public function schemaName(): string {
        return $this->schemaName;
    }

    public function name(): string {
        return $this->name;
    }

    public function columns(): IReadOnlyCollection {
        return $this->columns;
    }

    public function indexes(): IReadOnlyCollection {
        return $this->indexes;
    }

    public function primaryKeyColumns(): IReadOnlyCollection {
        $cacheKey = "primaryKeyColumnsCollection." . $this->schemaName . '.' . $this->name;

        if (isset(static::$cache[$cacheKey])) {
            return ReadOnlyCollection::new(static::$cache[$cacheKey], Type::class(Column::class));
        }

        $primaryKeyColumnsCollection = $this->indexes()->
            where(function(Index $index) { return $index->type() == IndexType::PRIMARY();})->
            select(function(Index $index) { return $index->columns();})->
            toReadonlyCollection();

        if (empty($primaryKeyColumns)) {
            $primaryKeyColumnsCollection = $this->columns()->
                where(function(Column $column) { return $column->indexType() == IndexType::PRIMARY();})->
                toReadonlyCollection();
        }

        return static::$cache[$cacheKey] = $primaryKeyColumnsCollection;
    }
}