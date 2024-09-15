<?php

declare(strict_types=1);

namespace Pst\Database\Connections;

use Pst\Core\CoreObject;

use Pst\Database\Query\IQuery;
use Pst\Database\Query\IQueryResults;

use InvalidArgumentException;

/**
 * Represents a database connection
 */
abstract class DatabaseConnection extends CoreObject implements IDatabaseConnection {
    protected abstract function implQuery(string $query, array $parameters = []): IQueryResults;

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
    public function query($query, ?array $parameters = null): IQueryResults {
        if ($query instanceof IQuery) {
            if ($parameters !== null) {
                throw new InvalidArgumentException("Parameters should not be passed when using a query object");
            }

            $parameters = $query->getParameters();
            $query = $query->getSql();
        } else if (!is_string($query)) {
            throw new InvalidArgumentException("Invalid query type: '" . gettype($query). "'");
        }

        return $this->implQuery($query, $parameters ?? []);
    }

    /**
     * Escapes a string for use in a query (should be overwritten by the implementation)
     * 
     * @param string $str 
     * 
     * @return string 
     */
    public function escapeString(string $str): string {
        $search = ['\\', "\x00", "\n", "\r", "'", '"', "\x1a"];
        $replace = ['\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z'];

        // Replace occurrences of these characters with their escaped versions
        return str_replace($search, $replace, $str);
    }

    /**
     * Replaces parameter placeholders in a query with the actual parameter values
     * 
     * @param string $query 
     * @param array $parameters 
     * 
     * @return string 
     * 
     * @throws InvalidArgumentException 
     */
    public function applyParametersToQuery(string $query, array $parameters): string {
        foreach ($parameters as $key => $value) {
            if (is_string($value)) {
                $value = "'" . $this->escapeString($value) . "'";
            } else if (is_bool($value)) {
                $value = $value ? 1 : 0;
            } else if (is_null($value)) {
                $value = "NULL";
            } else if (!is_int($value) && !is_float($value)) {
                throw new InvalidArgumentException("Invalid parameter type");
            }

            if (!strpos($query, ":$key")) {
                throw new InvalidArgumentException("Parameter $key not found in query");
            }

            $query = str_replace(":$key", $value, $query);
        }

        return $query;
    }
}