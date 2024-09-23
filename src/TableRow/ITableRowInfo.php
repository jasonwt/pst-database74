<?php

declare(strict_types=1);

namespace Pst\Database\TableRow;

use Pst\Core\Collections\IReadonlyCollection;
use Pst\Database\Table\Table;
use Pst\Database\Schema\Schema;

interface ITableRowInfo {
    public static function schemaName(): string;
    public static function tableName(): string;

    public static function columnExists(string $columnName): bool;

    public static function schema(): Schema;
    public static function table(): Table;
    public static function columns(): IReadonlyCollection;
    public static function indexes(): IReadonlyCollection;

    public static function defaultValues(): array;
    public static function validateColumnValue(string $columnName, $columnValue): bool;
}