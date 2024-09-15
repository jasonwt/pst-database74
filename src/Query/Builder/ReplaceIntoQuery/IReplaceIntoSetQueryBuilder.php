<?php

declare(strict_types=1);

namespace Pst\Database\Query\Builder\ReplaceIntoQuery;

use Pst\Database\Query\Builder\IQueryBuilder;

interface IReplaceIntoSetQueryBuilder extends IQueryBuilder {
    public function set(...$setExpressions): IReplaceIntoQueryBuilder;
}