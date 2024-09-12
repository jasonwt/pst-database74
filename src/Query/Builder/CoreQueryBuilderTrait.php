<?php

declare(strict_types=1);

namespace Pst\Database\Query\Builder;

use Pst\Database\Query\IQuery;
use Pst\Database\Query\Builder\IAutoJoiner;
use Pst\Database\Query\Builder\Clauses\From\From;
use Pst\Database\Query\Builder\Clauses\Join\Join;
use Pst\Database\Query\Builder\Clauses\Limit\Limit;
use Pst\Database\Query\Builder\Clauses\Where\Where;
use Pst\Database\Query\Builder\Clauses\Offset\Offset;
use Pst\Database\Query\Builder\Clauses\Select\Select;
use Pst\Database\Query\Builder\Clauses\Having\Having;
use Pst\Database\Query\Builder\Clauses\GroupBy\GroupBy;
use Pst\Database\Query\Builder\Clauses\OrderBy\OrderBy;

use Pst\Core\Exceptions\NotImplementedException;

use InvalidArgumentException;

trait CoreQueryBuilderTrait {
    private array $selectQueryTraitClauses = [
        Select::class => null,
        From::class => null,
        Join::class => null,
        Where::class => null,
        GroupBy::class => null,
        Having::class => null,
        OrderBy::class => null,
        Limit::class => null,
        Offset::class => null
    ];

    public function __construct(array $clauses = []) {
        foreach ($clauses as $clauseName => $clauseValue) {
            if (!is_object($clauseValue)) {
                throw new InvalidArgumentException("Invalid clause value: $clauseValue");
            }

            $thisClauseClass = null;

            $clauseValueClass = get_class($clauseValue);
            
            if (array_key_exists($clauseValueClass, $this->selectQueryTraitClauses)) {
                $thisClauseClass = $clauseValueClass;
            } else {
                foreach ($this->selectQueryTraitClauses as $clauseClass => $clause) {
                    if (is_a($clauseValue, $clauseClass, true)) {
                        $thisClauseClass = $clauseClass;
                        break;
                    }
                }
            }

            if ($thisClauseClass === null) {
                throw new InvalidArgumentException("Invalid clause value: $clauseValue");
            }

            if ($this->selectQueryTraitClauses[$thisClauseClass] !== null) {
                throw new InvalidArgumentException("Clause already set: $thisClauseClass");
            }

            $this->selectQueryTraitClauses[$thisClauseClass] = $clauseValue;
        }
    }

    /**
     * Gets the query
     * 
     * @param null|AutoJoiner $autoJoiner
     * 
     * @return IQuery 
     */
    public function getQuery(?IAutoJoiner $autoJoiner = null): IQuery {
        
        $querySql = "";
        $queryParameters = [];

        foreach ($this->selectQueryTraitClauses as $clauseClass => $clause) {
            if ($clause === null) {
                continue;
            }

            $querySql .= $clauseClass::getClauseName() . " " . rtrim($clause->getQuerySql()) . "\n";

            $queryParameters += $clause->getQueryParameters();
        }

        return new class($querySql, $queryParameters) implements IQuery {
            private string $querySql;
            private array $queryParameters;

            public function __construct(string $querySql, array $queryParameters) {
                $this->querySql = $querySql;
                $this->queryParameters = $queryParameters;
            }

            public function getSql(): string {
                return $this->querySql;
            }

            public function getParameters(): array {
                return $this->queryParameters;
            }

            public function getParameterlessQuery(): string {
                throw new NotImplementedException();
                // $query = self::getSql();
        
                // foreach ($this->clauses as $parameterKey => $v) {
                //     if ($v["parameter"] === null) {
                //         continue;
                //     }
        
                //     $query = str_replace(":" . $parameterKey, $v["parameter"], $query);
                // }
                
                // return $query;
            }

            // public function getQueryParameters(?Closure $escapeStringFunc = null): array {
            //     $escapeStringFunc ??= function(string $str): string {
            //         // List of characters to be escaped
            //         $search = ['\\', "\x00", "\n", "\r", "'", '"', "\x1a"];
            //         $replace = ['\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z'];
        
            //         // Replace occurrences of these characters with their escaped versions
            //         return str_replace($search, $replace, $str);
            //     };
        
            //     $escapeStringFunc = Func::new($escapeStringFunc, Type::string(), Type::string());
        
            //     $parameters = [];
        
            //     foreach ($this->clauses as $parameterKey => $v) {
            //         if ($v["parameter"] !== null && strlen($v["parameter"]) >= 2) {
            //             $pHead = $v["parameter"][0];
            //             $pTail = $v["parameter"][strlen($v["parameter"]) - 1];
                        
            //             if ($pHead == "'" || $pHead == '"' || $pTail == "'" || $pTail == '"') {
            //                 if ($pHead !== $pTail) {
            //                     throw new \Exception("Invalid parameter value");
            //                 }
        
            //                 $parameters[$parameterKey] = $escapeStringFunc(trim($v["parameter"], "\"'"));
            //             }
            //         }
            //     }
        
            //     return $parameters;
            // }
        };
    }
}