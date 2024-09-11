<?php

declare(strict_types=1);

namespace Pst\Database\Structure;

use Pst\Database\Structure;

use Pst\Core\CoreObject;

use Pst\Database\Structure\Traits\TableRecordTrait;

class TableRecord extends CoreObject {
    use TableRecordTrait;
}