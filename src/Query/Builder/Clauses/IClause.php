<?php

declare(strict_types=1);

namespace Pst\Database\Query\Builder\Clauses;

use Pst\Core\ICoreObject;
use Pst\Core\Types\Type;
use Pst\Database\Query\Builder\IGetQueryParts;

interface IClause extends ICoreObject, IGetQueryParts {
    public static function getClauseName(): string;
    public static function getExpressionInterfaceType(): Type;

    public static function tryConstructExpression(string $expression): ?IClauseExpression;    
}