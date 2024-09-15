<?php

declare(strict_types=1);

namespace Pst\Database\Query\Builder\ReplaceIntoQuery;

use Pst\Database\Query\IQuery;

interface IReplaceIntoQueryBuilder extends IReplaceIntoSetQueryBuilder {
    public function on(...$onExpressions): IReplaceIntoQueryBuilder;
    
    public function getQuery(): IQuery;
}