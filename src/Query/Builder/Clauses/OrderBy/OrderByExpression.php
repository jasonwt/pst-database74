<?php

declare(strict_types=1);

namespace Pst\Database\Query\Builder\Clauses\OrderBy;

use Pst\Database\Query\Builder\Clauses\ClauseExpression;
use Pst\Database\Query\Builder\Identifiers\IColumnIdentifier;

use InvalidArgumentException;

class OrderByExpression extends ClauseExpression implements IOrderByExpression {
    public function __construct(IColumnIdentifier $columnIdentifier, ?string $direction = null) {
        if (!is_null($direction) && !in_array($direction = trim(strtoupper($direction)), ['ASC', 'DESC'])) {
            throw new InvalidArgumentException("Invalid direction: '$direction'");
        }

        parent::__construct((object) [
            'columnIdentifier' => $columnIdentifier,
            'direction' => $direction
        ]);
    }

    public function getQueryParameters(): array {
        return [];
    }

    public function getQuerySql(): string {
        $expression = $this->getExpression();
        
        $columnIdentifier = $expression->columnIdentifier;
        $direction = $expression->direction;

        return (string) $columnIdentifier . ($direction !== null ? " $direction" : '');
    }
}