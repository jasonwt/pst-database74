<?php

declare(strict_types=1);

namespace Pst\Database;

use Pst\Core\Collections\IReadonlyCollection;
use Pst\Core\Interfaces\ICoreObject;


interface ITableObject extends ICoreObject {
    public static function columns(): IReadonlyCollection;
}