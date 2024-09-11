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
     * @param string $query 
     * @param array $parameters 
     * 
     * @return IQueryResults 
     */
    public function query(string $query, array $parameters = []);
}