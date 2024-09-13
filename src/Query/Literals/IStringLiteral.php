<?php

declare(strict_types=1);

namespace Pst\Database\Query\Literals;

use Pst\Database\Query\IAliasable;

interface IStringLiteral extends ILiteral, IAliasable {
    public function getValue(): string;
}