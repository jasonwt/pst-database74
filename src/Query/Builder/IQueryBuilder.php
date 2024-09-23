<?php

declare(strict_types=1);

namespace Pst\Database\Query\Builder;

use Pst\Core\Enumerable\IEnumerable;
use Pst\Database\Query\Builder\Clauses\IClause;

interface IQueryBuilder {
    public function getClause(string $clauseClassName): ?IClause;
    public function getClauses(): IEnumerable;
    public function getIdentifiers(): array;
}