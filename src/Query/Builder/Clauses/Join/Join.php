<?php

declare(strict_types=1);

namespace Pst\Database\Query\Builder\Clauses\Join;

use Pst\Database\Query\Builder\Clauses\Clause;

use Pst\Database\Query\Builder\Expressions\Expression;
use Pst\Database\Query\Builder\Expressions\IExpression;



use InvalidArgumentException;
use Pst\Database\Query\Builder\Clauses\Enums\JoinType;
use Pst\Database\Query\Builder\Clauses\Enums\ComparisonOperator;

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

    

    public static function getColumnIdentifierPregPattern(): string {
        return '((?:(?:[a-zA-Z0-9_]+|`[a-zA-Z0-9_]+`)\.)?' . '(?:(?:[a-zA-Z0-9_]+|`[a-zA-Z0-9_]+`)\.)?' . '(?:[a-zA-Z0-9_]+|`[a-zA-Z0-9_]+`))';
    }

    public static function getTableIdentifierPregPattern(): string {
        return '((?:(?:[a-zA-Z0-9_]+\.)?' . "(?:[a-zA-Z0-9_]+)))" . '(?:\s+as\s+([a-zA-Z0-9_]+))?\s*';
    }

    public static function getJoinOnPregPattern(string $delimiter = "/"): string {
        $joinTypesPregPattern = JoinType::getPregMatchPattern($delimiter);
        $tableIdentifierPregPattern = self::getTableIdentifierPregPattern();
        $columnIdentifierPregPattern = self::getColumnIdentifierPregPattern();
        $operandPregPattern = Expression::getOperandPregPattern();
        $comparisonOperatorsPregPattern = ComparisonOperator::getPregMatchPattern($delimiter);

        $expressionPattern = "{$columnIdentifierPregPattern}\s*{$comparisonOperatorsPregPattern}\s*{$operandPregPattern}";

        return "(?:(?:{$joinTypesPregPattern}\s+)?JOIN\s+{$tableIdentifierPregPattern}\s+(ON)\s+{$expressionPattern})";

        //return "{$joinTypePattern}\s+JOIN\s+(.+)\s+ON\s+(.+)";
    }

    public static function getJoinUsingPregPattern(string $delimiter = "/"): string {
        $joinTypesPregPattern = JoinType::getPregMatchPattern($delimiter);
        $tableIdentifierPregPattern = self::getTableIdentifierPregPattern();
        $columnIdentifierPregPattern = self::getColumnIdentifierPregPattern();
        $columnNamePregPattern = '(?:[a-zA-Z0-9_]+|`[a-zA-Z0-9_]+`)';
        $multipleColumnNamesPregPattern = '(?:[a-zA-Z0-9_]+|`[a-zA-Z0-9_]+`)(?:\s*,\s*(?:[a-zA-Z0-9_]+|`[a-zA-Z0-9_]+`))*';
        

        return "(?:(?:{$joinTypesPregPattern}\s+)?JOIN\s+{$tableIdentifierPregPattern}\s+(USING)\s+\(({$multipleColumnNamesPregPattern})\))";
    }

    

    public static function tryParse(string $expression): ?self {
        $joinOnPattern = self::getJoinOnPregPattern();
        $joinUsingPattern = self::getJoinUsingPregPattern();

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