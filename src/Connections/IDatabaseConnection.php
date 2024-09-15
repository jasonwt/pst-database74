<?php

declare(strict_types=1);

namespace Pst\Database\Connections;

use Pst\Database\Query\IQueryResults;

interface IDatabaseConnection {
    /**
     * Escapes a string for use in a query
     * 
     * @param string $str 
     * 
     * @return string 
     */
    public function escapeString(string $str): string;

    /**
     * Gets the name of the database
     * 
     * @return string 
     */
    public function getUsingSchema(): string;

    /**
     * Gets the last insert id
     * 
     * @return string 
     */
    public function lastInsertId(): string;

    /**
     * Perform a query on the database
     * 
     * @param string|IQuery $query 
     * @param null|array $parameters 
     * 
     * @return IQueryResults 
     * 
     * @throws InvalidArgumentException
     * @throws DatabaseException
     */
    public function query($query, ?array $parameters = null): IQueryResults;
}