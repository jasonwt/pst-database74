<?php

declare(strict_types=1);

namespace Pst\Database\Query\Builder\Clauses;

use Pst\Core\CoreObject;
use Pst\Database\Query\IQueryable;

abstract class Clause extends CoreObject implements IClause {
    private array $expressions = [];
    private ?array $queryParameters = null;
    protected ?string $querySql = null;

    protected function __construct(... $expressions) {
        $this->expressions = array_map(function($expression) {
            if (($constructedExpression = static::tryConstructExpression($expression)) === null) {
                throw new \Exception("Invalid expression: $expression");
            }

            if (!$constructedExpression instanceof IQueryable) {
                throw new \Exception("Expression must implement IQueryable");
            }

            return $constructedExpression;
        }, $expressions);
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

    public static function getClauseName(): string {
        // remove all but the classname
        $classNameParts = explode('\\', static::class);

        // add a space before each capital letter after the first one
        $clauseName = preg_replace('/(?<!^)([A-Z])/', ' $1', end($classNameParts));
        
        // return the clause name in lowercase
        return strtoupper($clauseName);
    }
}