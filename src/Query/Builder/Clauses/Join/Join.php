<?php

declare(strict_types=1);

namespace Pst\Database\Query\Builder\Clauses\Join;

use Pst\Core\Types\Type;

use Pst\Database\Preg;
use Pst\Database\Enums\ComparisonOperator;
use Pst\Database\Query\Builder\Clauses\Clause;
use Pst\Database\Query\Builder\Clauses\ClauseExpressionsTrait;
use Pst\Database\Query\Identifiers\ColumnIdentifier;
use Pst\Database\Query\Identifiers\TableIdentifier;

class Join extends Clause implements IJoin {
    use ClauseExpressionsTrait;

    public function getQuerySql(): string {
        if ($this->querySql !== null) {
            return $this->querySql;
        }

        $expressionsSql = array_map(function($expression, $key) {
            $returnValue = "";

            return rtrim($returnValue . $expression->getQuerySql()) . "\n";
        }, $this->getExpressions(), array_keys($this->getExpressions()));

        $sql =  rtrim(implode("", $expressionsSql));

        return $this->querySql = $sql . "\n";
    }


    public static function getBeginClauseStatement(): string {
        return "";
    }

    public static function getExpressionInterfaceType(): Type {
        return Type::new(IJoinExpression::class);
    }

    public static function new(...$expressions): self {
        return new self(...$expressions);
    }
}

Join::registerExpressionConstructor(
    "Columns Compare Parser",
    function($string): ?IJoinExpression {
        if (!is_string($string)) {
            return null;
        }

        $tablePattern = Preg::TABLE_IDENTIFIER_WITH_OPTIONAL_ALIAS_PATTERN;
        $operandPattern = Preg::COLUMN_IDENTIFIER_PATTERN;
        $operatorPattern = "(?:" . ComparisonOperator::getPregMatchPattern() . ")";

        $pattern = $tablePattern . "(?:\s+on\s+)" . $operandPattern . $operatorPattern . $operandPattern . "\s*\$";

        if (!preg_match("/^" . $pattern . "\s*\$/i", $string, $matches)) {
            return null;
        }

        $table = TableIdentifier::new($matches[2], $matches[1], $matches[3]);
        $leftOperand = ColumnIdentifier::new($matches[6], $matches[5], $matches[4], null);
        $operator = ComparisonOperator::tryFrom($matches[7]);
        $rightOperand = ColumnIdentifier::new($matches[10], $matches[9], $matches[8], null);

        return new class($table, $leftOperand, $operator, $rightOperand) extends JoinExpression implements IJoinExpression {
            public function __construct(TableIdentifier $table, ColumnIdentifier $leftOperand, ComparisonOperator $operator, ColumnIdentifier $rightOperand) {
                parent::__construct([$table, $leftOperand, $operator, $rightOperand]);
            }

            public function getQuerySql(): string {
                list ($table, $leftOperand, $operator, $rightOperand) = $this->getExpression();

                $sql = $table->getQuerySql() . " ON " . $leftOperand->getQuerySql() . " " . $operator . " " . $rightOperand->getQuerySql();

                return $table->getQuerySql() . " ON " . $leftOperand->getQuerySql() . " " . $operator . " " . $rightOperand->getQuerySql();
            }

            public function getQueryParameters(): array {
                list ($table, $leftOperand, $operator, $rightOperand) = $this->getExpression();
                return $leftOperand->getQueryParameters() + $rightOperand->getQueryParameters();
            }
        };
    }
, 0);