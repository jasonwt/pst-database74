<?php

declare(strict_types=1);

namespace Pst\Database\Query\Builder\Clauses\Join;

use Pst\Database\Query\Builder\Clauses\Clause;

use Pst\Database\Query\Builder\Expressions\Expression;
use Pst\Database\Query\Builder\Expressions\IExpression;



use InvalidArgumentException;
use Pst\Database\Query\Builder\Clauses\Enums\JoinType;
use Pst\Database\Enums\ComparisonOperator;

class Join extends Clause {
    /**
     * Creates a new Join clause
     * 
     * @param string|IExpression|Join ...$columnIdentifiers 
     */
    public function __construct(... $expressions) {
        $expressions = array_reduce($expressions, function($carry, $expression) {
            if (is_string($expression)) {
                if (($expression = Expression::tryConstructFromString($expression)) === null) {
                    throw new InvalidArgumentException("Invalid expression: '$expression'");
                }
            } else if ($expression instanceof self) {
                $carry[] = $expression;
                return $carry;

            } else if (!($expression instanceof IExpression)) {
                throw new InvalidArgumentException("Invalid expression type: '" . is_object($expression) ? get_class($expression) : gettype($expression) . "'");
            }

            $carry[] = $expression;
            return $carry;
        }, []);

        parent::__construct($expressions);
    }

    

    public static function getColumnIdentifierPreg(): string {
        return '((?:(?:[a-zA-Z0-9_]+|`[a-zA-Z0-9_]+`)\.)?' . '(?:(?:[a-zA-Z0-9_]+|`[a-zA-Z0-9_]+`)\.)?' . '(?:[a-zA-Z0-9_]+|`[a-zA-Z0-9_]+`))';
    }

    public static function getTableIdentifierPreg(): string {
        return '((?:(?:[a-zA-Z0-9_]+\.)?' . "(?:[a-zA-Z0-9_]+)))" . '(?:\s+as\s+([a-zA-Z0-9_]+))?\s*';
    }

    public static function getJoinOnPreg(string $delimiter = "/"): string {
        $joinTypesPreg = JoinType::getPregMatchPattern($delimiter);
        $tableIdentifierPreg = self::getTableIdentifierPreg();
        $columnIdentifierPreg = self::getColumnIdentifierPreg();
        $operandPreg = Expression::getOperandPreg();
        $comparisonOperatorsPreg = ComparisonOperator::getPregMatchPattern($delimiter);

        $expressionPattern = "{$columnIdentifierPreg}\s*{$comparisonOperatorsPreg}\s*{$operandPreg}";

        return "(?:(?:{$joinTypesPreg}\s+)?JOIN\s+{$tableIdentifierPreg}\s+(ON)\s+{$expressionPattern})";

        //return "{$joinTypePattern}\s+JOIN\s+(.+)\s+ON\s+(.+)";
    }

    public static function getJoinUsingPreg(string $delimiter = "/"): string {
        $joinTypesPreg = JoinType::getPregMatchPattern($delimiter);
        $tableIdentifierPreg = self::getTableIdentifierPreg();
        $columnIdentifierPreg = self::getColumnIdentifierPreg();
        $columnNamePreg = '(?:[a-zA-Z0-9_]+|`[a-zA-Z0-9_]+`)';
        $multipleColumnNamesPreg = '(?:[a-zA-Z0-9_]+|`[a-zA-Z0-9_]+`)(?:\s*,\s*(?:[a-zA-Z0-9_]+|`[a-zA-Z0-9_]+`))*';
        

        return "(?:(?:{$joinTypesPreg}\s+)?JOIN\s+{$tableIdentifierPreg}\s+(USING)\s+\(({$multipleColumnNamesPreg})\))";
    }

    

    public static function tryParse(string $expression): ?self {
        $joinOnPattern = self::getJoinOnPreg();
        $joinUsingPattern = self::getJoinUsingPreg();

        $patterns = "(?:{$joinOnPattern}?|{$joinUsingPattern})";

        if (preg_match('/^' . $patterns . '$/i', $expression, $matches) === false) {
            return null;
        }

        $matchedString = array_shift($matches);

        while (count($matches) > 0) {
            if (!empty($match = array_shift($matches))) {
                array_unshift($matches, $match);
                break;
            }
        }

        $matches = array_map(fn($match) => empty($match) ? null : $match, $matches);

        print_r($matches);

        return null;
    }

    /**
     * Creates a new Join clause
     * 
     * @param string|IExpression ...$columnIdentifiers 
     * 
     * @return Join
     */
    public static function new(...$expressions): Join {
        return new self(...$expressions);
    }
}