<?php

declare(strict_types=1);

namespace Pst\Database\Query\Builder\Literals;

use Pst\Core\ITryParse;
use Pst\Core\ICoreObject;

use Pst\Database\Query\Builder\IGetQueryParts;

interface ILiteral extends ICoreObject, ITryParse, IGetQueryParts {
    
}