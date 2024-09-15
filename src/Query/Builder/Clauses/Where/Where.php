<?php

declare(strict_types=1);

namespace Pst\Database\Query\Builder\Clauses\Where;

use Pst\Core\Types\Type;

use Pst\Database\Preg;
use Pst\Database\Enums\ComparisonOperator;
use Pst\Database\Query\Builder\Clauses\Clause;
use Pst\Database\Query\Literals\NumericLiteral;
use Pst\Database\Query\Literals\StringLiteral;
use Pst\Database\Query\Identifiers\ColumnIdentifier;
use Pst\Database\Query\Builder\Clauses\ClauseExpressionsTrait;

class Where extends Clause implements IWhere {
    use ClauseExpressionsTrait;

    private WhereExpressionType $whereExpressionType;

    protected function __construct(WhereExpressionType $whereExpressionType, ...$expressions) {
        $this->whereExpressionType = $whereExpressionType;

        parent::__construct(...$expressions);
    }

    public function getQuerySql(): string {
        if ($this->querySql !== null) {
            return $this->querySql;
        }

        $expressionsSql = array_map(function($expression, $key) {
            return rtrim($expression->getQuerySql()) . " ";
        }, $this->getExpressions(), array_keys($this->getExpressions()));

        $sql = 
            trim((string) $this->whereExpressionType . " " .
            (count($expressionsSql) > 1 ? "(" . rtrim(implode("", $expressionsSql)) . ")" : $expressionsSql[0]));

        return $this->querySql = $sql . "\n";
    }

    public function and($expression): IWhere {
        $newExpression = new Where(WhereExpressionType::AND(), $expression);
        return new Where($this->whereExpressionType, ...array_merge($this->getExpressions(), [$newExpression]));
    }

    public function or($expression): IWhere {
        $newExpression = new Where(WhereExpressionType::OR(), $expression);
        return new Where($this->whereExpressionType, ...array_merge($this->getExpressions(), [$newExpression]));
    }

    public static function getExpressionInterfaceType(): Type {
        return Type::new(IWhereExpression::class);
    }

    /**
     * Creates a new where clause
     * 
     * @param mixed $expression 
     * 
     * @return Where 
     */
    public static function new($expression): self {
        return new static(WhereExpressionType::WHERE(), $expression);
    }
}

Where::registerExpressionConstructor(
    "Sub Where",
    function($where): ?IWhereExpression {
        if (!($where instanceof Where)) {
            return null;
        }

        return $where;
    }
);

Where::registerExpressionConstructor(
    "Columns Compare Parser",
    function($string): ?IWhereExpression {
        if (!is_string($string)) {
            return null;
        }

        $operandPattern = Preg::COLUMN_IDENTIFIER_PATTERN;
        $operatorPattern = "(?:" . ComparisonOperator::getPregMatchPattern() . ")";

        $pattern = $operandPattern . $operatorPattern . $operandPattern . "\s*\$";

        if (!preg_match("/^" . $pattern . "\s*\$/i", $string, $matches)) {
            return null;
        }

        $leftOperand = ColumnIdentifier::new($matches[3], $matches[2], $matches[1], null);
        $operator = ComparisonOperator::tryFrom($matches[4]);
        $rightOperand = ColumnIdentifier::new($matches[7], $matches[6], $matches[5], null);

        return new class($leftOperand, $operator, $rightOperand) extends WhereExpression implements IWhereExpression {
            public function __construct($leftOperand, ComparisonOperator $operator, $rightOperand) {
                parent::__construct([$leftOperand, $operator, $rightOperand]);
            }

            public function getQuerySql(): string {
                list ($leftOperand, $operator, $rightOperand) = $this->getExpression();
                return $leftOperand->getQuerySql() . " " . $operator . " " . $rightOperand->getQuerySql();
            }

            public function getQueryParameters(): array {
                list ($leftOperand, $operator, $rightOperand) = $this->getExpression();
                return $leftOperand->getQueryParameters() + $rightOperand->getQueryParameters();
            }
        };
    }
, 0);

Where::registerExpressionConstructor(
    "Literals Compare Parser",
    function($string): ?IWhereExpression {
        if (!is_string($string)) {
            return null;
        }

        $operandPattern = "(" . Preg::SINGLE_QUOTED_STRING_PATTERN . "|" . Preg::DOUBLE_QUOTED_STRING_PATTERN . "|" . Preg::NUMERIC_PATTERN . ")\s*";
        $operatorPattern = "(?:" . ComparisonOperator::getPregMatchPattern() . ")";

        $pattern = $operandPattern . $operatorPattern . $operandPattern . "\s*\$";

        if (!preg_match("/^" . $pattern . "\s*\$/i", $string, $matches)) {
            return null;
        }

        $leftOperand = ($matches[1][0] === "'" || $matches[1][0] === '"') ? StringLiteral::new($matches[1]) : NumericLiteral::new((float) $matches[1]);
        $operator = ComparisonOperator::tryFrom($matches[2]);
        $rightOperand = ($matches[3][0] === "'" || $matches[3][0] === '"') ? StringLiteral::new($matches[3]) : NumericLiteral::new((float) $matches[3]);

        return new class($leftOperand, $operator, $rightOperand) extends WhereExpression implements IWhereExpression {
            public function __construct($leftOperand, ComparisonOperator $operator, $rightOperand) {
                parent::__construct([$leftOperand, $operator, $rightOperand]);
            }

            public function getQuerySql(): string {
                list ($leftOperand, $operator, $rightOperand) = $this->getExpression();
                return $leftOperand->getQuerySql() . " " . $operator . " " . $rightOperand->getQuerySql();
            }

            public function getQueryParameters(): array {
                list ($leftOperand, $operator, $rightOperand) = $this->getExpression();
                return $leftOperand->getQueryParameters() + $rightOperand->getQueryParameters();
            }
        };
    }
, 0);

Where::registerExpressionConstructor(
    "Mixed Compare Parser",
    function($string): ?IWhereExpression {
        if (!is_string($string)) {
            return null;
        }

        $leftOperandPattern = "(" . Preg::SINGLE_QUOTED_STRING_PATTERN . "|" . Preg::DOUBLE_QUOTED_STRING_PATTERN . "|" . Preg::NUMERIC_PATTERN . ")\s*";
        $rightOperandPattern = Preg::COLUMN_IDENTIFIER_PATTERN;
        $operatorPattern = "(?:" . ComparisonOperator::getPregMatchPattern() . ")";

        $leftOperand = null;
        $operator = null;
        $rightOperand = null;

        $pattern = $leftOperandPattern . $operatorPattern . $rightOperandPattern . "\s*\$";

        if (preg_match("/^" . $pattern . "\s*\$/i", $string, $matches)) {
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

        return new class($leftOperand, $operator, $rightOperand) extends WhereExpression implements IWhereExpression {
            public function __construct($leftOperand, ComparisonOperator $operator, $rightOperand) {
                parent::__construct([$leftOperand, $operator, $rightOperand]);
            }

            public function getQuerySql(): string {
                list ($leftOperand, $operator, $rightOperand) = $this->getExpression();
                return $leftOperand->getQuerySql() . " " . $operator . " " . $rightOperand->getQuerySql();
            }

            public function getQueryParameters(): array {
                list ($leftOperand, $operator, $rightOperand) = $this->getExpression();
                return $leftOperand->getQueryParameters() + $rightOperand->getQueryParameters();
            }
        };
    }
, 0);