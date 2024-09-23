<?php

declare(strict_types=1);

namespace Pst\Database\Schema;

use Pst\Core\CoreObject;
use Pst\Core\Types\Type;
use Pst\Core\Collections\ReadonlyCollection;
use Pst\Core\Collections\IReadonlyCollection;

use Pst\Database\Validator;
use Pst\Database\Table\Table;

use InvalidArgumentException;

class Schema extends CoreObject {
    private string $name;

    private IReadonlyCollection $tables;
    
    public function __construct(string $name, array $tables = []) {
        if (Validator::validateSchemaName($this->name = $name) !== true) {
            throw new \InvalidArgumentException("Invalid schema name: '$name'.");
        }

        $this->tables = ReadonlyCollection::new(array_map($tables, function($table) {
            if (!$table instanceof Table) {
                throw new InvalidArgumentException('Table must be an instance of Table.');
            }

            return $table;
        }), Type::class(Table::class));
    }

    public function name(): string {
        return $this->name;
    }

    public function tables(): IReadonlyCollection {
        return ReadonlyCollection::new($this->tables, Type::class(Table::class));
    }
}