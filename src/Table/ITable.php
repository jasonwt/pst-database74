<?php

declare(strict_types=1);

namespace Pst\Database\Table;

use Pst\Core\Interfaces\ICoreObject;
use Pst\Core\Enumerable\IRewindableEnumerable;

use Pst\Database\Index\IIndex;
use Pst\Database\Column\IColumn;

interface ITable extends ICoreObject {
    
    public function schemaName(): string;

    public function name(): string;

    public function columns(): IRewindableEnumerable;

    public function indexes(): IRewindableEnumerable;

    public function primaryIndex(): ?IIndex;

    public function autoIncrementingColumn(): ?IColumn;

    public function primaryKeyColumns(): IRewindableEnumerable;

    public function uniqueIndexes(): IRewindableEnumerable;
}