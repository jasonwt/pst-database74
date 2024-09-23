<?php

declare(strict_types=1);

namespace Pst\Database\Column;

use Pst\Core\Interfaces\ICoreObject;

use Pst\Database\Index\IndexType;
use Pst\Database\Column\ColumnType;

interface IColumn extends ICoreObject{
    public function schemaName(): string;
    public function tableName(): string;
    public function name(): string;
    public function type(): ColumnType;
    public function length(): ?int;
    public function defaultValue();
    public function isNullable(): bool;
    public function indexType(): ?IndexType;

    public function tryGetDefaultEvaluatedValue(&$evaluatedValue): bool;
    public function tryGetEvaluatedValue($value, &$evaluatedValue): bool;

    public function defaultPhpValue();
}