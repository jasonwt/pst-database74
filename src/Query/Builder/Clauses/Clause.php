<?php

declare(strict_types=1);

namespace Pst\Database\Query\Builder\Clauses;

use Pst\Core\CoreObject;
use Pst\Core\Func;
use Pst\Core\Types\TypeHintFactory;
use Pst\Database\Query\IQueryable;

abstract class Clause extends CoreObject implements IClause {
    private array $expressions = [];

    protected ?string $querySql = null;

    protected function __construct(... $expressions) {
        $this->addExpressions(...$expressions);
    }

    public function getIdentifiers(): array {
        $identifiers = [
            "schemas" => [],
            "tables" => [],
            "columns" => [],
            "aliases" => []
        ];

        foreach ($this->expressions as $expression) {
            if ($expression === null) {
                continue;
            }

            foreach ($expression->getIdentifiers() as $key => $value) {
                $identifiers[$key] += $value;
            }
        }

        return $identifiers;
    }

    /**
     * Adds expressions to the clause
     * 
     * @param string|IClauseExpression ...$expressions 
     * 
     * @return void 
     */
    protected function addExpressions(...$expressions): void {
        foreach ($expressions as $expression) {
            if (!$expression instanceof IClauseExpression) {
                if (($expression = static::tryConstructExpression($expression)) === null) {
                    
                    throw new \Exception("Invalid expression: $expression");
                }
            }

            $validateFunc = Func::new(function($expression): bool {
                return $expression instanceof IQueryable;
            }, static::getExpressionInterfaceType(), TypeHintFactory::bool());

            if (!$validateFunc($expression)) {
                throw new \Exception("Expression must implement IQueryable, type: '" . (string) static::getExpressionInterfaceType() . "' provided.");
            }

            //$expressionValue = $expression->getEx
            
            $this->expressions[] = $expression;
        }
    }

    public function getExpressions(): array {
        return $this->expressions;
    }

    public function getQueryParameters(): array {
        return $this->queryParameters ??= array_reduce($this->expressions, function($carry, $expression) {
            if ($expression === null) {
                return $carry;
            }

            $carry += $expression->getQueryParameters();
            return $carry;
        }, []);
    }

    public static function getBeginClauseStatement(): string {
        return static::getClauseName();
    }

    public static function getClauseName(): string {
        // remove all but the classname
        $classNameParts = explode('\\', static::class);

        // add a space before each capital letter after the first one
        $clauseName = preg_replace('/(?<!^)([A-Z])/', ' $1', end($classNameParts));
        
        // return the clause name in lowercase
        return strtoupper($clauseName);
    }
}