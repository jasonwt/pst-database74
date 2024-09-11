<?php

declare(strict_types=1);

namespace Pst\Database\Query\Builder\Clauses\Where;

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

class Where extends Clause implements IWhere {
    use ExpressionsTrait;

    public function getQuerySql(): string {
        if ($this->querySql !== null) {
            return $this->querySql;
        }

        $expressionsSql = array_map(function($expression, $key) {
            $returnValue = "";

            if ($key > 0) {
                if ($expression instanceof IOrWhere) {
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

    public function and(... $expressions): Where {
        if (count($expressions) === 0) {
            throw new InvalidArgumentException("No expressions provided");
        }

        $expressions = array_map(function($expression) {
            return new class($expression) extends Where implements IAndWhere {
                public function __construct($expression) {
                    parent::__construct($expression);
                }
            };

        }, $expressions);

        return new Where(...$this->getExpressions(), ...$expressions);
    }

    public function or(... $expressions): Where {
        if (count($expressions) === 0) {
            throw new InvalidArgumentException("No expressions provided");
        }

        $expressions = array_map(function($expression) {
            return new class($expression) extends Where implements IOrWhere {
                public function __construct($expression) {
                    parent::__construct($expression);
                }
            };

        }, $expressions);

        return new Where(...$this->getExpressions(), ...$expressions);
    }

    public static function getExpressionInterfaceType(): Type {
        return Type::fromTypeName(IWhereExpression::class);
    }

    public static function new(...$expressions): self {
        return new self(...$expressions);
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

        $operandPattern = PregPatterns::COLUMN_IDENTIFIER_PATTERN;
        $operatorPattern = "(?:" . ComparisonOperator::getPregMatchPattern() . ")";

        $pattern = $operandPattern . $operatorPattern . $operandPattern . "\s*\$";

        if (!preg_match("/^" . $pattern . "\s*\$/i", $string, $matches)) {
            return null;
        }

        $leftOperand = ColumnIdentifier::new($matches[3], $matches[2], $matches[1], null);
        $operator = ComparisonOperator::tryFrom($matches[4]);
        $rightOperand = ColumnIdentifier::new($matches[7], $matches[6], $matches[5], null);

        return new class($leftOperand, $operator, $rightOperand) extends CoreObject implements IWhereExpression {
            private ColumnIdentifier $leftOperand;
            private ComparisonOperator $operator;
            private ColumnIdentifier $rightOperand;

            public function __construct(ColumnIdentifier $leftOperand, ComparisonOperator $operator, ColumnIdentifier $rightOperand) {
                $this->leftOperand = $leftOperand;
                $this->operator = $operator;
                $this->rightOperand = $rightOperand;
            }

            public function getQuerySql(): string {
                return $this->leftOperand . " " . $this->operator . " " . $this->rightOperand;
            }

            public function getQueryParameters(): array {
                return [];
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

        $operandPattern = "(" . PregPatterns::SINGLE_QUOTED_STRING_PATTERN . "|" . PregPatterns::DOUBLE_QUOTED_STRING_PATTERN . "|" . PregPatterns::NUMERIC_PATTERN . ")\s*";
        $operatorPattern = "(?:" . ComparisonOperator::getPregMatchPattern() . ")";

        $pattern = $operandPattern . $operatorPattern . $operandPattern . "\s*\$";

        if (!preg_match("/^" . $pattern . "\s*\$/i", $string, $matches)) {
            return null;
        }

        $leftOperand = ($matches[1][0] === "'" || $matches[1][0] === '"') ? StringLiteral::new($matches[1]) : NumericLiteral::new((float) $matches[1]);
        $operator = ComparisonOperator::tryFrom($matches[2]);
        $rightOperand = ($matches[3][0] === "'" || $matches[3][0] === '"') ? StringLiteral::new($matches[3]) : NumericLiteral::new((float) $matches[3]);

        return new class($leftOperand, $operator, $rightOperand) extends CoreObject implements IWhereExpression {
            private $leftOperand;
            private ComparisonOperator $operator;
            private $rightOperand;

            public function __construct($leftOperand, ComparisonOperator $operator, $rightOperand) {
                $this->leftOperand = $leftOperand;
                $this->operator = $operator;
                $this->rightOperand = $rightOperand;
            }

            public function getQuerySql(): string {
                return $this->leftOperand . " " . $this->operator . " " . $this->rightOperand;
            }

            public function getQueryParameters(): array {
                return [];
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

        return new class($leftOperand, $operator, $rightOperand) extends CoreObject implements IWhereExpression {
            private $leftOperand;
            private ComparisonOperator $operator;
            private $rightOperand;

            public function __construct($leftOperand, ComparisonOperator $operator, $rightOperand) {
                $this->leftOperand = $leftOperand;
                $this->operator = $operator;
                $this->rightOperand = $rightOperand;
            }

            public function getQuerySql(): string {
                return $this->leftOperand . " " . $this->operator . " " . $this->rightOperand;
            }

            public function getQueryParameters(): array {
                return [];
            }
        };        
    }
, 0);

// class Where extends Clause {
//     /**
//      * Creates a new where clause
//      * 
//      * @param string|IExpression|Where ...$columnIdentifiers 
//      */
//     public function __construct(... $expressions) {
//         $expressions = array_reduce($expressions, function($carry, $expression) {
//             if (is_string($expression)) {
//                 if (($expression = Expression::tryConstructFromString($expression)) === null) {
//                     throw new InvalidArgumentException("Invalid expression: '$expression'");
//                 }
//             } else if ($expression instanceof self) {
//                 $carry[] = $expression;
//                 return $carry;

//             } else if (!($expression instanceof IExpression)) {
//                 throw new InvalidArgumentException("Invalid expression type: '" . is_object($expression) ? get_class($expression) : gettype($expression) . "'");
//             }
            
//             if (count($carry) > 0) {
//                 $expression = new class($expression) extends Where implements IAndWhere {
//                     public function __construct(IExpression $expression) {
//                         parent::__construct($expression);
//                     }
//                 };
//             }

//             $carry[] = $expression;
//             return $carry;
//         }, []);

//         parent::__construct($expressions);
//     }

//     /**
//      * creates a new query with the existing expressions and an added and expression
//      * 
//      * @param string|IExpression ...$columnIdentifiers 
//      * 
//      * @return Where
//      */
//     public function and(... $expressions): Where {
//         if (count($expressions) === 0) {
//             throw new InvalidArgumentException("No expressions provided");
//         }

//         return new Where(...$this->getValues(), ...$expressions);
//     }

//     /**
//      * creates a new query with the existing expressions and an added or expression
//      * 
//      * @param string|IExpression ...$columnIdentifiers 
//      * 
//      * @return Where
//      */
//     public function or(... $expressions): Where {
//         if (count($expressions) === 0) {
//             throw new InvalidArgumentException("No expressions provided");
//         }

//         $orExpressions = new class($expressions) extends Where implements IOrWhere {
//             public function __construct(array $expressions) {
//                 parent::__construct(...$expressions);
//             }
//         };

//         return new Where(... array_merge($this->getValues(), [$orExpressions]));
//     }

//     /**
//      * Creates a new where clause
//      * 
//      * @param string|IExpression ...$columnIdentifiers 
//      * 
//      * @return Where
//      */
//     public static function new(...$expressions): Where {
//         return new self(...$expressions);
//     }
// }