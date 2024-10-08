<?php

declare(strict_types=1);

namespace Pst\Database\Query\Builder\Clauses;

use Pst\Core\Func;
use Pst\Core\Types\Type;
use Pst\Core\Types\TypeUnion;
use Pst\Core\Types\TypeHintFactory;

use Pst\Database\Query\Builder\Clauses\IClauseExpression;

use Closure;
use InvalidArgumentException;

trait ClauseExpressionsTrait {
    private static array $expressionTraitConstructors = [];
    private static array $expressionTraitFromTypes = [];

    public static abstract function getExpressionInterfaceType(): Type;

    /**
     * Register an expression parser class
     * 
     * @param Closure $expressionParsersClosure
     * @param int $priority 
     * 
     * @return void 
     * @throws InvalidArgumentException 
     */
    public static function registerExpressionConstructor(string $name, Closure $expressionConstructor, int $priority = 0): void {
        if (empty($name = trim($name))) {
            throw new InvalidArgumentException("Expression parser name cannot be empty");
        }

        //$expressionConstructor = Func::new($expressionConstructor, TypeHintFactory::undefined(), TypeHintFactory::interface(true, static::getExpressionInterfaceType()->fullName()));
        $expressionConstructor = Func::new($expressionConstructor, TypeHintFactory::undefined(), TypeUnion::create(Type::null(), static::getExpressionInterfaceType()));
        
        if (isset(static::$expressionTraitConstructors[$name])) {
            throw new InvalidArgumentException("Expression parser already registered: $name");
        }

        static::$expressionTraitConstructors[$name] = (object) [
            "name" => $name,
            "constructor" => $expressionConstructor, 
            "priority" => $priority
        ];

        // sort the expression parsers by priority
        uasort(static::$expressionTraitConstructors, fn($a, $b) => $a->priority <=> $b->priority);

        // echo "Registered expression constructor: '$name' with priority: $priority\n";
        // print_r(array_map(fn($p) => $p->priority, static::$expressionTraitConstructors));
    }

    /**
     * Try to construct an expression
     * 
     * @param mixed $expression 
     * 
     * @return IClauseExpression|null 
     */
    public static function tryConstructExpression($expression): ?IClauseExpression {
        $thisExpressionType = static::getExpressionInterfaceType();
        
        $expressionType = Type::typeOf($expression);

        if ($thisExpressionType->isAssignableFrom($expressionType)) {
            return $expression;
        }

        foreach (static::$expressionTraitConstructors as $expressionConstructor) {
            $constructor = $expressionConstructor->constructor;

            if (($constructedExpression = $constructor($expression)) !== null) {
                return $constructedExpression;
            }            
        }

        return null;
    }
}