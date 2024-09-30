<?php

declare(strict_types=1);

namespace Pst\Database\Schema;

use Pst\Core\Interfaces\ICoreObject;
use Pst\Core\Enumerable\IRewindableEnumerable;

interface ISchema extends ICoreObject  {
    private string $name;

    private IRewindableEnumerable $tables;
    
    public function name(): string;
    public function tables(): IRewindableEnumerable;
}