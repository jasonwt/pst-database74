<?php

declare(strict_types=1);

namespace Pst\Database\Connections\Impl;

use Pst\Database\Query\QueryResults;
use Pst\Database\Query\IQueryResults;
use Pst\Database\Connections\DatabaseConnection;

use Pst\Database\Structure\Readers\ISchemaReader;
use Pst\Database\Structure\Readers\Impl\MysqlStructureReader;

use Pst\Database\Exceptions\DatabaseException;

use PDO;
use Generator;
use InvalidArgumentException;

class MysqlPdoConnection extends DatabaseConnection implements IMysqlConnection {

    private PDO $pdo;
    private ISchemaReader $schemaReader;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
        $this->schemaReader = new MysqlStructureReader($this);
    }

    /**
     * Get the last insert id
     * 
     * @return string 
     */
    public function lastInsertId(): string {
        return $this->pdo->lastInsertId();
    }

    /**
     * Get the name of the schema being used
     * 
     * @return string 
     */
    public function getUsingSchema(): string {
        return $this->pdo->query('SELECT DATABASE() as usingSchema')->fetchColumn();
    }

    /**
     * Perform a query on the database
     * 
     * @param string $query 
     * @param array $parameters 
     * 
     * @return bool|IQueryResults 
     */
    public function query(string $query, array $parameters = []) {
        if (($query = trim($query)) === '') {
            throw new InvalidArgumentException("Query cannot be empty");
        }

        $stmt = null;

        if (count($parameters) > 0) {
            $stmt = $this->pdo->prepare($query);

            if ($stmt === false) {
                throw new DatabaseException("Error preparing query");
            }

            if ($stmt->execute($parameters) === false) {
                throw new DatabaseException("Error executing query");
            }
        } else {
            $stmt = $this->pdo->query($query);

            if ($stmt === false) {
                throw new DatabaseException("Error executing query");
            }
        }

        if ($stmt->errorCode() !== '00000') {
            throw new DatabaseException($stmt->errorInfo()[2]);
        }

        $resultsGenerator = (function() use ($stmt): Generator {
            while (($row = $stmt->fetch(PDO::FETCH_ASSOC)) !== false) {
                yield $row;
            }
        })();

        return new class($resultsGenerator, $stmt->rowCount(), $stmt->columnCount()) extends QueryResults {
        };
    }
}