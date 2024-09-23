<?php

declare(strict_types=1);

namespace Pst\Database\Query\Identifiers;

use Pst\Core\Interfaces\ITryParse;
use Pst\Core\Interfaces\ICoreObject;

use Pst\Database\Query\IAliasable;
use Pst\Database\Query\IQueryable;

interface IIdentifier extends ICoreObject, IAliasable, ITryParse, IQueryable {
}