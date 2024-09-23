<?php

declare(strict_types=1);

namespace Pst\Database\Exceptions;

use Pst\Core\Interfaces\ICoreObject;
use Pst\Core\CoreObjectTrait;

use Exception;

class DatabaseException extends Exception implements ICoreObject{
    use CoreObjectTrait {
        __toString as private coreObjectToString;
    }

    public function __toString(): string {
        return parent::__toString();
    }
}