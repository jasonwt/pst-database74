<?php

declare(strict_types=1);

namespace Pst\Database\Query\Builder\Clauses;

use Pst\Core\ICoreObject;
use Pst\Core\Types\Type;
use Pst\Database\Query\IQueryable;

interface IClause extends ICoreObject, IQueryable {
    public function getExpressions(): array;

    public static function getClauseName(): string;
    public static function getExpressionInterfaceType(): Type;

    public static function tryConstructExpression(string $expression): ?IClauseExpression;    
}