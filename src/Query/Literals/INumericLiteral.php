<?php

declare(strict_types=1);

namespace Pst\Database\Query\Literals;

use Pst\Core\Interfaces\ICoreObject;

use Pst\Database\Query\Builder\Expressions\ILeftExpressionOperand;
use Pst\Database\Query\Builder\Expressions\IRightExpressionOperand;

interface INumericLiteral extends ILiteral {
    public function getValue(): float;
}