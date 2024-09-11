<?php

declare(strict_types=1);

namespace Pst\Database\Connections;

use InvalidArgumentException;
use Pst\Core\CoreObject;
use Pst\Database\Query\IQueryResults;

abstract class DatabaseConnection extends CoreObject implements IDatabaseConnection {
    public function escapeString(string $str): string {
        $search = ['\\', "\x00", "\n", "\r", "'", '"', "\x1a"];
        $replace = ['\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z'];

        // Replace occurrences of these characters with their escaped versions
        return str_replace($search, $replace, $str);
    }

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