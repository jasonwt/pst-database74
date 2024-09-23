<?php

declare (strict_types = 1);

namespace Pst\Database;

use Pst\Database\Connections\IDatabaseConnection;
use Pst\Database\Schema\ISchemaReader;

final class DB {
    private static array $connections = [];

    public static function addConnection(IDatabaseConnection $connection, ISchemaReader $schemaReader, ?string $name = null): void {
        $name ??= get_class($connection) . "-" . count(self::$connections);

        if (($name = trim($name)) === "") {
            throw new \InvalidArgumentException("Connection name cannot be empty");
        }

        if (isset(self::$connections[$name])) {
            throw new \InvalidArgumentException("Connection with name '{$name}' already exists");
        }

        self::$connections[$name] = [
            "connection" => $connection,
            "schemaReader" => $schemaReader
        ];
    }

    public static function db(?string $connectionName = null): IDatabaseConnection {
        $connectionName ??= array_key_first(self::$connections);

        if (!isset(self::$connections[$connectionName])) {
            throw new \InvalidArgumentException("Connection with name '{$connectionName}' does not exist");
        }

        return self::$connections[$connectionName]["connection"];
    }

    public static function dbs(?string $schemaReaderName = null): ISchemaReader {
        $schemaReaderName ??= array_key_first(self::$connections);

        if (!isset(self::$connections[$schemaReaderName])) {
            throw new \InvalidArgumentException("Schema reader with name '{$schemaReaderName}' does not exist");
        }

        return self::$connections[$schemaReaderName]["schemaReader"];
    }
}