<?php

declare(strict_types=1);

namespace Pst\Database\Query\Builder\Identifiers;

use Pst\Core\ITryParse;
use Pst\Core\ICoreObject;

use Pst\Database\Query\Builder\IAliasable;
use Pst\Database\Query\Builder\IGetQueryParts;

interface ITableIdentifier extends ICoreObject, IAliasable, ITryParse, IGetQueryParts {
    public function getSchemaName(): ?string;
    public function getTableName(): string;
}