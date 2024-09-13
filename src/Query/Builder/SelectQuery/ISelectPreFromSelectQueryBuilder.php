<?php

declare(strict_types=1);

namespace Pst\Database\Query\Builder\SelectQuery;

use Pst\Core\ICoreObject;

interface ISelectPreFromSelectQueryBuilder extends ICoreObject {
    public function select(...$expressions): ISelectPreFromSelectQueryBuilder;
    public function from(...$expressions): ISelectQueryBuilder;
}