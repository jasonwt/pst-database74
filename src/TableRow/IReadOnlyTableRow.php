<?php

declare(strict_types=1);

namespace Pst\Database\TableRow;

use Pst\Core\Interfaces\ICoreObject;
use Pst\Core\Collections\IReadonlyCollection;

interface IReadOnlyTableRow extends ITableRowInfo, ICoreObject {
    public function isExistingDatabaseRecord(): bool;
    public function isInSyncWithDatabase(): bool;

    public function getColumnValues(): IReadonlyCollection;
    public function tryGetColumnValue(string $columnName, &$columnValue): bool;
    public function getColumnValue(string $columnName);

    //    public static function create(iterable $columnValues, &$tableRowOrException, $noThrow = false): bool;
    public static function read(iterable $predicate, &$tableRowOrException, $noThrow = false): bool;
    // update
    // delete
    public static function list(iterable $predicates = [], &$tableRowsOrException, $noThrow = false): bool;
    
}