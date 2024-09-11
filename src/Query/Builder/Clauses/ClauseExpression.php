<?php

declare(strict_types=1);

namespace Pst\Database\Query\Builder\Clauses;

use Pst\Core\CoreObject;

abstract class ClauseExpression extends CoreObject implements IClauseExpression {
    private $expression;

    public function __construct($expression) {
        $this->expression = $expression;
    }

    public function getQueryParameters(): array {
        return $this->expression->getQueryParameters();
    }

    public function getQuerySql(): string {
        return $this->expression->getQuerySql();
    }
}