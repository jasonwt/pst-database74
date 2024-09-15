<?php

declare(strict_types=1);

namespace Pst\Database\Query\Builder\InsertQuery;

use Pst\Database\Query\IQuery;

interface IInsertQueryBuilder extends IInsertSetQueryBuilder {
    public function on(...$onExpressions): IInsertQueryBuilder;
    
    public function getQuery(): IQuery;
}