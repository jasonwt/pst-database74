<?php

declare(strict_types=1);

namespace Pst\Database\Structure;

use Pst\Core\CoreObject;
use Pst\Core\Types\Type;
use Pst\Core\Collections\Enumerator;
use Pst\Core\Collections\IEnumerable;

use Pst\Database\Validator;

use InvalidArgumentException;

class Table extends CoreObject {
    private string $schemaName;
    private string $name;

    private array $columns;
    private array $indexes;

    public function __construct(string $schemaName, string $name, array $columns = [], array $indexes = []) {
        if (Validator::validateSchemaName($this->schemaName = $schemaName) !== true) {
            throw new \InvalidArgumentException("Invalid schema name: '$schemaName'.");
        }

        if (Validator::validateTableName($this->name = $name) !== true) {
            throw new \InvalidArgumentException("Invalid table name: '$name'.");
        }

        $this->columns = array_map($columns, function($column) {
            if (!$column instanceof Column) {
                throw new InvalidArgumentException('Column must be an instance of Column.');
            }

            return $column;
        });

        $this->indexes = array_map($indexes, function($index) {
            if (!$index instanceof Index) {
                throw new InvalidArgumentException('Index must be an instance of Index.');
            }

            return $index;
        });
    }

    public function schemaName(): string {
        return $this->schemaName;
    }

    public function name(): string {
        return $this->name;
    }

    public function columns(): IEnumerable {
        return Enumerator::new($this->columns, Type::class(Column::class));
    }

    public function indexes(): IEnumerable {
        return Enumerator::new($this->indexes, Type::class(Index::class));
    }

    // public function constructTableRecord(array $values = []): TableRecord {
    //     return new TableRecord($this, $values);
    // }
}