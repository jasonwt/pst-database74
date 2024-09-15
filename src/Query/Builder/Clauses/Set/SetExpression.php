<?php

declare(strict_types=1);

namespace Pst\Database\Query\Builder\Clauses\Set;

use Pst\Database\Query\Builder\Clauses\ClauseExpression;
use Pst\Database\Query\Identifiers\ColumnIdentifier;

class SetExpression extends ClauseExpression implements ISetExpression {
    public function getInQuerySchemas(): array {
        if (($getExpression = $this->getExpression()[0]) instanceof ColumnIdentifier) {
            return [$getExpression->getSchemaName() => $getExpression->getSchemaName()];
        }
        
        return parent::getInQuerySchemas();
    }

    public function getInQueryTables(): array {
        if (($getExpression = $this->getExpression()[0]) instanceof ColumnIdentifier) {
            $key = $getExpression->getTableName();

            if (($schemaName = $getExpression->getSchemaName()) !== null) {
                $key = $schemaName . "." . $key;
            }

            return [$key => $getExpression->getTableName()];
        }
        
        return parent::getInQueryTables();
    }

    public function getInQueryColumns(): array {
        if (($getExpression = $this->getExpression()[0]) instanceof ColumnIdentifier) {
            $key = $getExpression->getColumnName();
            
            if (($tableName = $getExpression->getTableName()) !== null) {
                $key = $tableName . "." . $key;
            }

            if (($schemaName = $getExpression->getSchemaName()) !== null) {
                $key = $schemaName . "." . $key;
            }

            return [$key => $getExpression->getColumnName()];
        }
        
        return parent::getInQueryColumns();
    }
}