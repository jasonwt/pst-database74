<?php

declare(strict_types=1);

namespace Pst\Database\Query\Builder\Literals;

use Pst\Core\ICoreObject;

use Pst\Database\Query\Builder\Expressions\ILeftExpressionOperand;
use Pst\Database\Query\Builder\Expressions\IRightExpressionOperand;

interface INumericLiteral extends ILiteral {
    public function getValue(): float;
}