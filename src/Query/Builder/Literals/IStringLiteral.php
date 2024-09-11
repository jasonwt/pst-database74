<?php

declare(strict_types=1);

namespace Pst\Database\Query\Builder\Literals;

use Pst\Core\ICoreObject;

use Pst\Database\Query\Builder\IAliasable;
use Pst\Database\Query\Builder\Expressions\ILeftExpressionOperand;
use Pst\Database\Query\Builder\Expressions\IRightExpressionOperand;

interface IStringLiteral extends ILiteral, IAliasable {
    public function getValue(): string;
}