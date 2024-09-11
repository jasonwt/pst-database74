<?php

declare(strict_types=1);

namespace Pst\Database\Query\Builder\Select\Interfaces;

use Pst\Core\ICoreObject;

use Pst\Database\Query\IQuery;
use Pst\Database\Query\Builder\IAutoJoiner;

interface IOffsetClause extends ICoreObject {
    public function getQuery(?IAutoJoiner $autoJoiner = null): IQuery;
}