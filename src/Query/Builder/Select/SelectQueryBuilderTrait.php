<?php

declare(strict_types=1);

namespace Pst\Database\Query\Builder\Select;

use Pst\Core\CoreObject;

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
use Pst\Database\Query\Builder\Expressions\IExpression;
use Pst\Database\Query\Builder\Identifiers\ColumnIdentifier;
use Pst\Database\Query\Builder\Identifiers\TableIdentifier;
use Pst\Database\Query\Builder\Select\Interfaces\IFromClause;
use Pst\Database\Query\Builder\Select\Interfaces\IJoinClause;
use Pst\Database\Query\Builder\Select\Interfaces\IWhereClause;
use Pst\Database\Query\Builder\Select\Interfaces\IGroupByClause;
use Pst\Database\Query\Builder\Select\Interfaces\IHavingClause;
use Pst\Database\Query\Builder\Select\Interfaces\IOrderByClause;
use Pst\Database\Query\Builder\Select\Interfaces\ILimitClause;
use Pst\Database\Query\Builder\Select\Interfaces\IOffsetClause;
use Pst\Database\Query\Builder\Clauses\Interfaces\IInnerJoin;
use Pst\Database\Query\Builder\Clauses\Interfaces\ILeftJoin;
use Pst\Database\Query\Builder\Clauses\Interfaces\IOuterJoin;
use Pst\Database\Query\Builder\Clauses\Interfaces\IRightJoin;

use Pst\Core\Exceptions\NotImplementedException;

use Exception;
use InvalidArgumentException;

trait SelectQueryBuilderTrait {
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
     * Assigns the tables to select from
     * 
     * @param string|TableIdentifier ...$columns
     * 
     * @return IFromClause 
     */
    private function from(...$tables): IFromClause {
        if (count($tables) === 0) {
            throw new InvalidArgumentException("No tables provided");
        }
        
        $this->selectQueryTraitClauses[From::class] = From::new(...$tables);

        return new class(array_filter($this->selectQueryTraitClauses, fn($v) => !empty($v))) extends CoreObject implements IFromClause {
            use SelectQueryBuilderTrait {
                innerJoin as public;
                leftJoin as public;
                rightJoin as public;
                outerJoin as public;
                where as public;
                groupBy as public;
                having as public;
                orderBy as public;
                limit as public;
                offset as public;
                getQuery as public;
            }
        };
    }

    private function join(Join ...$joins): IJoinClause {
        $this->selectQueryTraitClauses[Join::class] = array_merge($this->selectQueryTraitClauses[Join::class] ?? [], $joins);

        return new class(array_filter($this->selectQueryTraitClauses, fn($v) => !empty($v))) extends CoreObject implements IJoinClause {
            use SelectQueryBuilderTrait {
                innerJoin as public;
                leftJoin as public;
                rightJoin as public;
                outerJoin as public;
                where as public;
                groupBy as public;
                having as public;
                orderBy as public;
                limit as public;
                offset as public;
                getQuery as public;
            }
        };
    }
    
    private function innerJoin(...$expressions): IJoinClause {
        if (count($expressions) === 0) {
            throw new InvalidArgumentException("No expressions provided");
        }

        return $this->join(... array_map(fn($e) => new class($e) extends Join implements IInnerJoin {}, $expressions));
    }

    private function outerJoin(...$expressions): IJoinClause {
        if (count($expressions) === 0) {
            throw new InvalidArgumentException("No expressions provided");
        }

        return $this->join(... array_map(fn($e) => new class($e) extends Join implements IOuterJoin {}, $expressions));
    }

    private function leftJoin(...$expressions): IJoinClause {
        if (count($expressions) === 0) {
            throw new InvalidArgumentException("No expressions provided");
        }

        return $this->join(... array_map(fn($e) => new class($e) extends Join implements ILeftJoin {}, $expressions));
    }

    private function rightJoin(...$expressions): IJoinClause {
        if (count($expressions) === 0) {
            throw new InvalidArgumentException("No expressions provided");
        }

        return $this->join(... array_map(fn($e) => new class($e) extends Join implements IRightJoin {}, $expressions));
    }

    /**
     * Assigns the expressions as a where clause
     * 
     * @param string|IExpression|Where ...$expressions 
     * @return IWhereClause 
     */
    private function where(...$expressions): IWhereClause {
        if (count($expressions) === 0) {
            throw new InvalidArgumentException("No expressions provided");
        }

        $this->selectQueryTraitClauses[Where::class] = Where::new(...$expressions);

        return new class(array_filter($this->selectQueryTraitClauses, fn($v) => !empty($v))) extends CoreObject implements IWhereClause {
            use SelectQueryBuilderTrait {
                andWhere as public and;
                orWhere as public or;
                groupBy as public;
                having as public;
                orderBy as public;
                limit as public;
                offset as public;
                getQuery as public;
            }
        };
    }

    /**
     * Assigns the expressions as an and where clause
     * 
     * @param string|IExpression|Where ...$expressions 
     * 
     * @return IWhereClause 
     */
    private function andWhere(... $expressions): IWhereClause {
        if (count($expressions) === 0) {
            throw new InvalidArgumentException("No expressions provided");
        }

        $this->selectQueryTraitClauses[Where::class] = $this->selectQueryTraitClauses[Where::class]->and(...$expressions);

        return $this;
    }

    /**
     * Assigns the expressions as an or where clause
     * 
     * @param string|IExpression|Where ...$expressions 
     * 
     * @return IWhereClause 
     */
    private function orWhere(... $expressions): IWhereClause {
        if (count($expressions) === 0) {
            throw new InvalidArgumentException("No expressions provided");
        }

        $this->selectQueryTraitClauses[Where::class] = $this->selectQueryTraitClauses[Where::class]->or(...$expressions);

        return $this;
    }

    /**
     * Assigns the columns to group by
     * 
     * @param string|ColumnIdentifier ...$columns
     * 
     * @return IGroupByClause 
     */
    private function groupBy(...$columns): IGroupByClause {
        if (count($columns) === 0) {
            throw new InvalidArgumentException("No columns provided");
        }

        $this->selectQueryTraitClauses[GroupBy::class] = GroupBy::new(...$columns);
        
        return new class(array_filter($this->selectQueryTraitClauses, fn($v) => !empty($v))) extends CoreObject implements IGroupByClause {
            use SelectQueryBuilderTrait {
                having as public;
                orderBy as public;
                limit as public;
                offset as public;
                getQuery as public;
            }
        };
    }

    /**
     * Assigns the expressions as a having clause
     * 
     * @param string|IExpression|Having ...$expressions 
     * 
     * @return IHavingClause 
     * 
     * @throws InvalidArgumentException
     */
    private function having(...$expressions): IHavingClause {
        if (count($expressions) === 0) {
            throw new InvalidArgumentException("No expressions provided");
        }

        $this->selectQueryTraitClauses[Having::class] = Having::new(...$expressions);

        return new class(array_filter($this->selectQueryTraitClauses, fn($v) => !empty($v))) extends CoreObject implements IHavingClause {
            use SelectQueryBuilderTrait {
                andHaving as public and;
                orHaving as public or;
                orderBy as public;
                limit as public;
                offset as public;
                getQuery as public;
            }
        };
    }

    /**
     * Assigns the expressions as an and having clause
     * 
     * @param string|IExpression|Having ...$expressions 
     * 
     * @return IHavingClause 
     * 
     * @throws InvalidArgumentException
     */
    private function andHaving(... $expressions): IHavingClause {
        if (count($expressions) === 0) {
            throw new InvalidArgumentException("No expressions provided");
        }

        $this->selectQueryTraitClauses[Having::class] = $this->selectQueryTraitClauses[Having::class]->and(...$expressions);

        return $this;
    }

    /**
     * Assigns the expressions as an or having clause
     * 
     * @param string|IExpression|Having ...$expressions 
     * 
     * @return IHavingClause 
     * 
     * @throws InvalidArgumentException
     */
    private function orHaving(... $expressions): IHavingClause {
        if (count($expressions) === 0) {
            throw new InvalidArgumentException("No expressions provided");
        }

        $this->selectQueryTraitClauses[Having::class] = $this->selectQueryTraitClauses[Having::class]->or(...$expressions);

        return $this;
    }

    /**
     * Assigns the columns to order by
     * 
     * @param string|ColumnIdentifier ...$columns
     * 
     * @return IOrderByClause 
     * @throws Exception 
     */
    private function orderBy(...$columns): IOrderByClause {
        if (count($columns) === 0) {
            throw new InvalidArgumentException("No columns provided");
        }

        $this->selectQueryTraitClauses[OrderBy::class] = OrderBy::new(...$columns);

        return new class(array_filter($this->selectQueryTraitClauses, fn($v) => !empty($v))) extends CoreObject implements IOrderByClause {
            use SelectQueryBuilderTrait {
                limit as public;
                offset as public;
                getQuery as public;
            }
        };
    }

    /**
     * Assigns the limit
     * 
     * @param int $limit
     * 
     * @return ILimitClause 
     * 
     * @throws InvalidArgumentException 
     */
    private function limit(int $limit): ILimitClause {
        $this->selectQueryTraitClauses[Limit::class] = Limit::new($limit);

        return new class(array_filter($this->selectQueryTraitClauses, fn($v) => !empty($v))) extends CoreObject implements ILimitClause {
            use SelectQueryBuilderTrait {
                offset as public;
                getQuery as public;
            }
        };
    }

    /**
     * Assigns the offset
     * 
     * @param int $offset
     * 
     * @return IOffsetClause 
     * 
     * @throws InvalidArgumentException 
     */
    private function offset(int $offset): IOffsetClause {
        $this->selectQueryTraitClauses[Offset::class] = Offset::new($offset);

        return new class(array_filter($this->selectQueryTraitClauses, fn($v) => !empty($v))) extends CoreObject implements IOffsetClause {
            use SelectQueryBuilderTrait {
                getQuery as public;
            }
        };
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