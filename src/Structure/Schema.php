<?php

declare(strict_types=1);

namespace Pst\Database\Structure;

use Pst\Core\CoreObject;
use Pst\Core\Types\TypeHintFactory;
use Pst\Core\Collections\Enumerator;
use Pst\Core\Collections\IEnumerable;

use Pst\Database\Validator;

use InvalidArgumentException;


class Schema extends CoreObject {
    private string $name;

    private array $tables;
    
    public function __construct(string $name, array $tables = []) {
        if (Validator::validateSchemaName($this->name = $name) !== true) {
            throw new \InvalidArgumentException("Invalid schema name: '$name'.");
        }

        $this->tables = array_map($tables, function($table) {
            if (!$table instanceof Table) {
                throw new InvalidArgumentException('Table must be an instance of Table.');
            }

            return $table;
        });
    }

    public function name(): string {
        return $this->name;
    }

    public function tables(): IEnumerable {
        return Enumerator::new($this->tables, TypeHint::class(Table::class));
    }
}