<?php

declare(strict_types=1);

namespace Pst\Database\Query\Builder\Clauses\Having;

use Pst\Core\CoreObject;
use Pst\Core\Types\Type;

use Pst\Database\PregPatterns;
use Pst\Database\Query\Builder\Clauses\Clause;
use Pst\Database\Query\Builder\Clauses\Enums\ComparisonOperator;
use Pst\Database\Query\Builder\Clauses\Traits\ExpressionsTrait;
use Pst\Database\Query\Builder\Identifiers\ColumnIdentifier;
use Pst\Database\Query\Builder\Literals\NumericLiteral;
use Pst\Database\Query\Builder\Literals\StringLiteral;

use InvalidArgumentException;

class Having extends Clause implements IHaving {
    use ExpressionsTrait;

    public function getQuerySql(): string {
        if ($this->querySql !== null) {
            return $this->querySql;
        }

        $expressionsSql = array_map(function($expression, $key) {
            $returnValue = "";

            if ($key > 0) {
                if ($expression instanceof IOrHaving) {
                    $returnValue = "OR ";
                } else {
                    $returnValue = "AND ";
                }
            }

            return $returnValue . $expression->getQuerySql();
        }, $this->getExpressions(), array_keys($this->getExpressions()));

        $sql = count($expressionsSql) > 1 ? "(" . rtrim(implode(" ", $expressionsSql)) . ")" : $expressionsSql[0];

        return $this->querySql = $sql . "\n";
    }

    public function and(... $expressions): Having {
        if (count($expressions) === 0) {
            throw new InvalidArgumentException("No expressions provided");
        }

        $expressions = array_map(function($expression) {
            return new class($expression) extends Having implements IAndHaving {
                public function __construct($expression) {
                    parent::__construct($expression);
                }
            };

        }, $expressions);

        return new Having(...$this->getExpressions(), ...$expressions);
    }

    public function or(... $expressions): Having {
        if (count($expressions) === 0) {
            throw new InvalidArgumentException("No expressions provided");
        }

        $expressions = array_map(function($expression) {
            return new class($expression) extends Having implements IOrHaving {
                public function __construct($expression) {
                    parent::__construct($expression);
                }
            };

        }, $expressions);

        return new Having(...$this->getExpressions(), ...$expressions);
    }

    public static function getExpressionInterfaceType(): Type {
        return Type::new(IHavingExpression::class);
    }

    public static function new(...$expressions): self {
        return new self(...$expressions);
    }
}

Having::registerExpressionConstructor(
    "Sub Having",
    function($Having): ?IHavingExpression {
        if (!($Having instanceof Having)) {
            return null;
        }

        return $Having;
    }
);

Having::registerExpressionConstructor(
    "Columns Compare Parser",
    function($string): ?IHavingExpression {
        if (!is_string($string)) {
            return null;
        }

        $operandPattern = PregPatterns::COLUMN_IDENTIFIER_PATTERN;
        $operatorPattern = "(?:" . ComparisonOperator::getPregMatchPattern() . ")";

        $pattern = $operandPattern . $operatorPattern . $operandPattern . "\s*\$";

        if (!preg_match("/^" . $pattern . "\s*\$/i", $string, $matches)) {
            return null;
        }

        $leftOperand = ColumnIdentifier::new($matches[3], $matches[2], $matches[1], null);
        $operator = ComparisonOperator::tryFrom($matches[4]);
        $rightOperand = ColumnIdentifier::new($matches[7], $matches[6], $matches[5], null);

        return new class($leftOperand, $operator, $rightOperand) extends CoreObject implements IHavingExpression {
            private ColumnIdentifier $leftOperand;
            private ComparisonOperator $operator;
            private ColumnIdentifier $rightOperand;

            public function __construct(ColumnIdentifier $leftOperand, ComparisonOperator $operator, ColumnIdentifier $rightOperand) {
                $this->leftOperand = $leftOperand;
                $this->operator = $operator;
                $this->rightOperand = $rightOperand;
            }

            public function getQuerySql(): string {
                return $this->leftOperand->getQuerySql() . " " . $this->operator . " " . $this->rightOperand->getQuerySql();
            }

            public function getQueryParameters(): array {
                return $this->leftOperand->getQueryParameters() + $this->rightOperand->getQueryParameters();
            }
        };
    }
, 0);

Having::registerExpressionConstructor(
    "Literals Compare Parser",
    function($string): ?IHavingExpression {
        if (!is_string($string)) {
            return null;
        }

        $operandPattern = "(" . PregPatterns::SINGLE_QUOTED_STRING_PATTERN . "|" . PregPatterns::DOUBLE_QUOTED_STRING_PATTERN . "|" . PregPatterns::NUMERIC_PATTERN . ")\s*";
        $operatorPattern = "(?:" . ComparisonOperator::getPregMatchPattern() . ")";

        $pattern = $operandPattern . $operatorPattern . $operandPattern . "\s*\$";

        if (!preg_match("/^" . $pattern . "\s*\$/i", $string, $matches)) {
            return null;
        }

        $leftOperand = ($matches[1][0] === "'" || $matches[1][0] === '"') ? StringLiteral::new($matches[1]) : NumericLiteral::new((float) $matches[1]);
        $operator = ComparisonOperator::tryFrom($matches[2]);
        $rightOperand = ($matches[3][0] === "'" || $matches[3][0] === '"') ? StringLiteral::new($matches[3]) : NumericLiteral::new((float) $matches[3]);

        return new class($leftOperand, $operator, $rightOperand) extends CoreObject implements IHavingExpression {
            private $leftOperand;
            private ComparisonOperator $operator;
            private $rightOperand;

            public function __construct($leftOperand, ComparisonOperator $operator, $rightOperand) {
                $this->leftOperand = $leftOperand;
                $this->operator = $operator;
                $this->rightOperand = $rightOperand;
            }

            public function getQuerySql(): string {
                return $this->leftOperand->getQuerySql() . " " . $this->operator . " " . $this->rightOperand->getQuerySql();
            }

            public function getQueryParameters(): array {
                return $this->leftOperand->getQueryParameters() + $this->rightOperand->getQueryParameters();
            }
        };
    }
, 0);

Having::registerExpressionConstructor(
    "Mixed Compare Parser",
    function($string): ?IHavingExpression {
        if (!is_string($string)) {
            return null;
        }

        $leftOperandPattern = "(" . PregPatterns::SINGLE_QUOTED_STRING_PATTERN . "|" . PregPatterns::DOUBLE_QUOTED_STRING_PATTERN . "|" . PregPatterns::NUMERIC_PATTERN . ")\s*";
        $rightOperandPattern = PregPatterns::COLUMN_IDENTIFIER_PATTERN;
        $operatorPattern = "(?:" . ComparisonOperator::getPregMatchPattern() . ")";

        $leftOperand = null;
        $operator = null;
        $rightOperand = null;

        $pattern = $leftOperandPattern . $operatorPattern . $rightOperandPattern . "\s*\$";

        if (preg_match("/^" . $pattern . "\s*\$/i", $string, $matches)) {
            print_r($matches);

            $leftOperand = ($matches[1][0] === "'" || $matches[1][0] === '"') ? StringLiteral::new($matches[1]) : NumericLiteral::new((float) $matches[1]);
            $operator = ComparisonOperator::tryFrom($matches[2]);
            $rightOperand = ColumnIdentifier::new($matches[5], $matches[4], $matches[3], null);

        } else {
            $pattern = $rightOperandPattern . $operatorPattern . $leftOperandPattern . "\s*\$";

            if (!preg_match("/^" . $pattern . "\s*\$/i", $string, $matches)) {
                return null;
            }

            $leftOperand = ColumnIdentifier::new($matches[3], $matches[2], $matches[1], null);
            $operator = ComparisonOperator::tryFrom($matches[4]);
            $rightOperand = ($matches[5][0] === "'" || $matches[5][0] === '"') ? StringLiteral::new($matches[5]) : NumericLiteral::new((float) $matches[5]);
        }

        return new class($leftOperand, $operator, $rightOperand) extends CoreObject implements IHavingExpression {
            private $leftOperand;
            private ComparisonOperator $operator;
            private $rightOperand;

            public function __construct($leftOperand, ComparisonOperator $operator, $rightOperand) {
                $this->leftOperand = $leftOperand;
                $this->operator = $operator;
                $this->rightOperand = $rightOperand;
            }

            public function getQuerySql(): string {
                return $this->leftOperand->getQuerySql() . " " . $this->operator . " " . $this->rightOperand->getQuerySql();
            }

            public function getQueryParameters(): array {
                return $this->leftOperand->getQueryParameters() + $this->rightOperand->getQueryParameters();
            }
        };        
    }
, 0);