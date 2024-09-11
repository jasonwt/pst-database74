<?php

/*TDD*/

declare(strict_types=1);

namespace Pst\Database\Tests\MysqlConnectionTests;


use Pst\Database\Connections\Impl\MysqlPdoConnection;
use Pst\Database\Structure\Readers\Impl\MysqlStructureReader;

use Pst\Testing\Should;

use PDO;
use Exception;
use Pst\Database\Structure\ColumnDefaultValue;
use Pst\Database\Structure\ColumnType;
use Pst\Database\Structure\IndexType;

require_once __DIR__ . '/../../vendor/autoload.php';

Should::executeTests(function() {
    $pdoConnection = new PDO('mysql:host=mariadb;dbname=information_schema', 'root', 'mbdcdevRootPassword');
    $mysqlConnection = Should::notThrow(Exception::class, fn() => new MysqlPdoConnection($pdoConnection))[0];
    $databaseStructureReader = Should::notThrow(Exception::class, fn() => new MysqlStructureReader($mysqlConnection))[0];

    $sct2AdministratorsIdColumn = Should::notThrow(Exception::class, fn() => $databaseStructureReader->readColumn("sct2", "administrators", "id"))[0];
    Should::equal("sct2", $sct2AdministratorsIdColumn->schemaName());
    Should::equal("administrators", $sct2AdministratorsIdColumn->tableName());
    Should::equal("id", $sct2AdministratorsIdColumn->name());
    Should::equal(ColumnType::AUTO_INCREMENTING_INT(), $sct2AdministratorsIdColumn->type());
    Should::beFalse($sct2AdministratorsIdColumn->isNullable());
    Should::equal(ColumnDefaultValue::NONE(), $sct2AdministratorsIdColumn->defaultValue());
    Should::equal(null, $sct2AdministratorsIdColumn->length());
    Should::equal(IndexType::PRIMARY(), $sct2AdministratorsIdColumn->indexType());

    $sct2AdministratorsTable = Should::notThrow(Exception::class, fn() => $databaseStructureReader->readTable("sct2", "administrators"))[0];
    $expectedColumnNames = ["id", "name", "initials", "level", "password", "sesskey", "clients", "directcustomer", "email", "active", "projectcodes"];
    Should::equal($expectedColumnNames, $sct2AdministratorsTable->columns()->select(function($column, $index) {
        return $column->name();
    })->toArray());

    $sct2Schema = Should::notThrow(Exception::class, fn() => $databaseStructureReader->readSchema("sct2"))[0];
    $expectedTablesNames = ["new_files", "mixtureIngredients", "new_adminfiles", "requests", "mixtureLog", "rslnames", "new_chemfiles", "clients", "rsls", "tblChemAssess", "mixtures", "administrators", "rslupdates", "tfa", "logger", "states", "projects", "countries", "passwdreset"];
    Should::equal($expectedTablesNames, $sct2Schema->tables()->select(function($table, $index) {
        return $table->name();
    })->toArray());
});