<?php

declare(strict_types=1);

namespace Pst\Database\Table;

use Pst\Core\CoreObject;
use Pst\Core\Types\Type;
use Pst\Core\Enumerable\Enumerable;
use Pst\Core\Enumerable\IRewindableEnumerable;
use Pst\Core\Enumerable\RewindableEnumerable;

use Pst\Database\Validator;
use Pst\Database\Index\IIndex;
use Pst\Database\Column\IColumn;

use InvalidArgumentException;

class Table extends CoreObject {
    private static array $cache = [];

    private string $schemaName;
    private string $name;

    private IRewindableEnumerable $columns;
    private IRewindableEnumerable $indexes;
    private IRewindableEnumerable $uniqueIndexes;
    private ?IIndex $primaryIndex = null;
    private ?IColumn $autoIncrementingColumn = null;

    public function __construct(string $schemaName, string $name, iterable $columns = [], iterable $indexes = []) {
        if (Validator::validateSchemaName($this->schemaName = $schemaName) !== true) {
            throw new InvalidArgumentException("Invalid schema name: '$schemaName'.");
        }

        if (Validator::validateTableName($this->name = $name) !== true) {
            throw new InvalidArgumentException("Invalid table name: '$name'.");
        }


        $this->columns = $columns instanceof RewindableEnumerable ? $columns : RewindableEnumerable::create(array_map(function($column) {
            if (!$column instanceof IColumn) {
                throw new InvalidArgumentException('IColumn must be an instance of IColumn.');
            }

            if ($column->type()->isAutoIncrementing()) {
                $this->autoIncrementingColumn = $column;
            }

            return $column;
        }, $columns), Type::class(IColumn::class), Type::int());

        $uniqueIndexes = [];

        $this->indexes = $indexes instanceof RewindableEnumerable ? $indexes : RewindableEnumerable::create(array_map(function($index) use (&$uniqueIndexes) {
            if (!$index instanceof IIndex) {
                throw new InvalidArgumentException('IIndex must be an instance of IIndex.');
            }

            $indexType = $index->type();

            if ($indexType->isPrimary()) {
                $this->primaryIndex = $index;
            }

            if ($indexType->isUnique()) {
                $uniqueIndexes[$index->name()] = $index;
            }

            return $index;
        }, $indexes), Type::class(IIndex::class));

        $this->uniqueIndexes = RewindableEnumerable::create($uniqueIndexes, Type::class(IIndex::class));
    }

    public function schemaName(): string {
        return $this->schemaName;
    }

    public function name(): string {
        return $this->name;
    }

    public function columns(): IRewindableEnumerable {
        return $this->columns;
    }

    public function indexes(): IRewindableEnumerable {
        return $this->indexes;
    }

    public function primaryIndex(): ?IIndex {
        return $this->primaryIndex;
    }

    public function autoIncrementingColumn(): ?IColumn {
        return $this->autoIncrementingColumn;
    }

    public function primaryKeyColumns(): IRewindableEnumerable {
        return !is_null($this->primaryIndex) ? $this->primaryIndex->columns() : Enumerable::empty();
    }

    public function uniqueIndexes(): IRewindableEnumerable {
        return $this->uniqueIndexes;
    }
}