<?php

declare(strict_types=1);

namespace Pst\Database;

use Pst\Core\Collections\IReadOnlyCollection;
use Pst\Core\ICoreObject;


interface ITableObject extends ICoreObject {
    public static function columns(): IReadOnlyCollection;
}