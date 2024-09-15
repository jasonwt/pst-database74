<?php

declare(strict_types=1);

namespace Pst\Database\Query\Builder\Clauses;

use Pst\Core\CoreObject;
use Pst\Database\Query\IAliasable;
use Pst\Database\Query\Identifiers\IColumnIdentifier;
use Pst\Database\Query\Identifiers\ITableIdentifier;

abstract class ClauseExpression extends CoreObject implements IClauseExpression {
    private $expression;
    private array $queryInfo = [
        "schemas" => [],
        "tables" => [],
        "columns" => [],
        "aliases" => []
    ];

    public function __construct($expression) {
        $this->expression = $expression;
    }

    public function getIdentifiers(): array {
        $identifiers = [
            "schemas" => [],
            "tables" => [],
            "columns" => [],
            "aliases" => []
        ];
        
        $expression = !is_array($this->expression) ? [$this->expression] : $this->expression;

        foreach ($expression as $item) {
            $itemIsColumnIdentifier = $item instanceof IColumnIdentifier;
            $itemIsTableIdentifier = $item instanceof ITableIdentifier;

            if ($itemIsColumnIdentifier || $itemIsTableIdentifier) {
                $schemaName = $item->getSchemaName();
                $tableName = $item->getTableName();
                $alias = $item->getAlias();

                if ($schemaName !== null) {
                    $identifiers["schemas"][$schemaName] ??= $schemaName;
                }

                if ($tableName !== null) {
                    $identifiers["tables"][((!is_null($schemaName) ? $schemaName . "." : "") . $tableName)] ??= $tableName;
                }

                if ($itemIsColumnIdentifier) {
                    $columnName = $item->getColumnName();
                    $identifiers["columns"][((!is_null($tableName) ? $tableName . "." : "") . $columnName)] ??= $columnName;
                }

            } else if ($item instanceof IClauseExpression || $item instanceof IClause) {
                $clauseIdentifiers = $item->getIdentifiers();

                foreach ($clauseIdentifiers as $key => $value) {
                    $identifiers[$key] += $value;
                    
                }
            }

            if ($item instanceof IAliasable) {
                if (($alias = $item->getAlias()) !== null) {
                    $identifiers["aliases"][$alias] ??= $item;
                }
            }

            
        }

        return $identifiers;
    }

    protected function getExpression() {
        return $this->expression;
    }

    public function getQueryParameters(): array {
        return $this->expression->getQueryParameters();
    }

    public function getQuerySql(): string {
        return $this->expression->getQuerySql();
    }

    public function getInQuerySchemas(): array {
        if ($this->expression instanceof ITableIdentifier || $this->expression instanceof IColumnIdentifier) {
            return [$this->expression->getSchemaName() => $this->expression->getSchemaName()];
        }
        return [];
    }

    public function getInQueryTables(): array {
        if ($this->expression instanceof ITableIdentifier || $this->expression instanceof IColumnIdentifier) {
            $key = $this->expression->getTableName();

            if (($schemaName = $this->expression->getSchemaName()) !== null) {
                $key = $schemaName . "." . $key;
            }

            return [$key => $this->expression->getTableName()];
            
        }
        return [];
    }

    public function getInQueryColumns(): array {
        if ($this->expression instanceof IColumnIdentifier) {
            $key = $this->expression->getColumnName();
            
            if (($tableName = $this->expression->getTableName()) !== null) {
                $key = $tableName . "." . $key;
            }

            if (($schemaName = $this->expression->getSchemaName()) !== null) {
                $key = $schemaName . "." . $key;
            }

            return [$key => $this->expression->getColumnName()];
        }
        return [];
    }

    public function getInQueryAliases(): array {
        if ($this->expression instanceof IAliasable) {
            if (($alias = $this->expression->getAlias()) !== null) {
                return [$alias => $this->expression];
            }
        }
        return [];
    }
}