<?php

declare(strict_types=1);

namespace Pst\Database\Query\Literals;

use Pst\Core\Interfaces\ITryParse;
use Pst\Core\Interfaces\ICoreObject;

use Pst\Database\Query\IQueryable;

interface ILiteral extends ICoreObject, ITryParse, IQueryable {
    
}