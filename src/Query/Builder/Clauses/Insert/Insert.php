<?php

declare(strict_types=1);

namespace Pst\Database\Query\Builder\Clauses\Insert;

use Pst\Core\Types\Type;

use Pst\Database\Query\Builder\Clauses\Clause;
use Pst\Database\Query\Builder\Clauses\ClauseExpressionsTrait;
use Pst\Database\Query\Identifiers\TableIdentifier;

class Insert extends Clause implements IInsert {
    use ClauseExpressionsTrait;

    protected InsertType $insertType;

    protected function __construct($tableExpression, ?InsertType $insertType) {
        $this->insertType = $insertType ?? InsertType::INTO();
        
        parent::__construct($tableExpression);
    }

    public function getQuerySql(): string {
        return $this->insertType . " " . $this->getExpressions()[0]->getQuerySql() . "\n";
    }

    public static function getExpressionInterfaceType(): Type {
        return Type::new(IInsertExpression::class);
    }

    public static function new($tableExpression, ?InsertType $insertType = null): self {
        return new self($tableExpression, $insertType ?? InsertType::INTO());
    }
}

/**
 * An expression constructor that parses a string into an InsertExpression
 */
Insert::registerExpressionConstructor(
    "TableIdentifier String",
    function($string): ?IInsertExpression {
        if (!is_string($string) || ($tableIdentifier = TableIdentifier::tryParse($string)) === null) {
            return null;
        }

        return new InsertExpression($tableIdentifier);
    }
, 0);

/**
 * An expression constructor that parses a tableIdentifier into an InsertByExpression
 */
Insert::registerExpressionConstructor(
    "TableIdentifier Object",
    function($tableIdentifier): ?IInsertExpression {
        if (!($tableIdentifier instanceof TableIdentifier)) {
            return null;
        }

        return new InsertExpression($tableIdentifier);
    }
, 0);