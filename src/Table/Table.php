<?php

declare(strict_types=1);

namespace Pst\Database\Table;

use Pst\Core\CoreObject;
use Pst\Core\Types\Type;
use Pst\Core\Collections\ReadonlyCollection;
use Pst\Core\Collections\IReadonlyCollection;

use Pst\Database\Index\IndexType;
use Pst\Database\Validator;
use Pst\Database\Index\Index;
use Pst\Database\Column\Column;

use InvalidArgumentException;
use Pst\Core\Enumerable\Enumerator;

class Table extends CoreObject {
    private static array $cache = [];

    private string $schemaName;
    private string $name;

    private IReadonlyCollection $columns;
    private IReadonlyCollection $indexes;
    private IReadonlyCollection $uniqueIndexes;
    private ?Index $primaryIndex = null;
    private ?Column $autoIncrementingColumn = null;

    public function __construct(string $schemaName, string $name, array $columns = [], array $indexes = []) {
        if (Validator::validateSchemaName($this->schemaName = $schemaName) !== true) {
            throw new \InvalidArgumentException("Invalid schema name: '$schemaName'.");
        }

        if (Validator::validateTableName($this->name = $name) !== true) {
            throw new \InvalidArgumentException("Invalid table name: '$name'.");
        }

        $this->columns = ReadonlyCollection::new(array_map(function($column) {
            if (!$column instanceof Column) {
                throw new InvalidArgumentException('Column must be an instance of Column.');
            }

            if ($column->type()->isAutoIncrementing()) {
                $this->autoIncrementingColumn = $column;
            }

            return $column;
        }, $columns), Type::class(Column::class));

        $uniqueIndexes = [];

        $this->indexes = ReadonlyCollection::new(array_map(function($index) use (&$uniqueIndexes) {
            if (!$index instanceof Index) {
                throw new InvalidArgumentException('Index must be an instance of Index.');
            }

            $indexType = $index->type();

            if ($indexType->isPrimary()) {
                $this->primaryIndex = $index;
            }

            if ($indexType->isUnique()) {
                $uniqueIndexes[$index->name()] = $index;
            }

            return $index;
        }, $indexes), Type::class(Index::class));

        $this->uniqueIndexes = ReadonlyCollection::new($uniqueIndexes, Type::class(Index::class));
    }

    public function schemaName(): string {
        return $this->schemaName;
    }

    public function name(): string {
        return $this->name;
    }

    public function columns(): IReadonlyCollection {
        return $this->columns;
    }

    public function indexes(): IReadonlyCollection {
        return $this->indexes;
    }

    public function primaryIndex(): ?Index {
        return $this->primaryIndex;
    }

    public function autoIncrementingColumn(): ?Column {
        return $this->autoIncrementingColumn;
    }

    public function primaryKeyColumns(): IReadonlyCollection {
        return !is_null($this->primaryIndex) ? $this->primaryIndex->columns() : Enumerator::empty();
    }

    public function uniqueIndexes(): IReadonlyCollection {
        return $this->uniqueIndexes;
    }
}