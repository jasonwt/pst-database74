<?php

declare(strict_types=1);

namespace Pst\Database\Structure\Schema;

use Pst\Core\CoreObject;
use Pst\Core\Types\Type;
use Pst\Core\Collections\ReadOnlyCollection;
use Pst\Core\Collections\IReadOnlyCollection;

use Pst\Database\Structure\Validator;
use Pst\Database\Structure\Table\Table;

use InvalidArgumentException;

class Schema extends CoreObject {
    private string $name;

    private IReadOnlyCollection $tables;
    
    public function __construct(string $name, array $tables = []) {
        if (Validator::validateSchemaName($this->name = $name) !== true) {
            throw new \InvalidArgumentException("Invalid schema name: '$name'.");
        }

        $this->tables = ReadOnlyCollection::new(array_map($tables, function($table) {
            if (!$table instanceof Table) {
                throw new InvalidArgumentException('Table must be an instance of Table.');
            }

            return $table;
        }), Type::class(Table::class));
    }

    public function name(): string {
        return $this->name;
    }

    public function tables(): IReadOnlyCollection {
        return ReadOnlyCollection::new($this->tables, Type::class(Table::class));
    }
}