<?php

declare(strict_types=1);

namespace Pst\Database\TableRow;

use Pst\Core\Enumerable\IEnumerable;
use Pst\Core\Collections\IReadonlyCollection;
use Pst\Core\Events\IEventSubscriptions;

interface ITableRow extends IReadOnlyTableRow {
    public function columnValuesSetEvent(): IEventSubscriptions;

    public function setColumnValues(iterable $columnValues, bool $noThrow = true): bool;
    public function setColumnValue(string $columnName, $columnValue, bool $noThrow = true): bool;

    //public function update(iterable $columnValuesToUpdate = []): bool;
    //public function delete(): bool;
}