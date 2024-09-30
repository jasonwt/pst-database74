<?php

declare(strict_types=1);

namespace Pst\Database\Schema;

use Pst\Core\CoreObject;
use Pst\Core\Types\Type;
use Pst\Core\Enumerable\IRewindableEnumerable;
use Pst\Core\Enumerable\RewindableEnumerable;

use Pst\Database\Validator;
use Pst\Database\Table\ITable;

use InvalidArgumentException;


class Schema extends CoreObject implements ISchema {
    private string $name;

    private IRewindableEnumerable $tables;
    
    public function __construct(string $name, iterable $tables = []) {
        if (Validator::validateSchemaName($this->name = $name) !== true) {
            throw new InvalidArgumentException("Invalid schema name: '$name'.");
        }

        $this->tables = RewindableEnumerable::create($tables, Type::class(ITable::class), Type::string());
    }

    public function name(): string {
        return $this->name;
    }

    public function tables(): IRewindableEnumerable {
        return RewindableEnumerable::create($this->tables, Type::class(ITable::class));
    }
}