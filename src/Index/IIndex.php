<?php

declare(strict_types=1);

namespace Pst\Database\Index;

use Pst\Core\Interfaces\ICoreObject;
use Pst\Core\Enumerable\IRewindableEnumerable;

use Pst\Database\Index\IndexType;

interface IIndex extends ICoreObject {
    public function schemaName(): string;
    public function tableName(): string;
    public function name(): string;
    public function type(): IndexType;
    public function columns(): IRewindableEnumerable;
}