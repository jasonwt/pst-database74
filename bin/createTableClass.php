<?php

declare(strict_types=1);

use Pst\Core\Enumerable\Enumerator;
use Pst\Database\Connections\Impl\MysqlPdoConnection;
use Pst\Database\Column;
use Pst\Database\ColumnDefaultValue;
use Pst\Database\Readers\Impl\MysqlStructureReader;
use Pst\Database\Validator;

require_once __DIR__ . '/../vendor/autoload.php';

function usage(string $message) {
    echo "\n";

    if (!empty($message)) {
        echo trim($message) . "\n\n";
    }

    echo "Usage: php createTableClass.php --schema=<schema> --table=<table> --className=<className> --filePath=<filePath>\n\n";
    exit;
}

function makeSingular($word): string {
    $lastChar = substr($word, -1);

    if ($lastChar === 's') {
        return substr($word, 0, -1);
    }

    return $word;
}

function tableNameToClassName(string $tableName): string {
    $tableName = makeSingular($tableName);
    return ucfirst($tableName)[0] . strtolower(substr($tableName, 1));
}

function schemaNameToNamespace(string $schemaName): string {
    return ucfirst($schemaName)[0] . strtolower(substr($schemaName, 1));
}

// parse command line arguments
$shortopts = "";
$longopts = array(
    "schema:",
    "table:",
    "className:",
    "filePath:"
);

$options = getopt($shortopts, $longopts);

if (empty($schema = trim($options['schema'] ?? ''))) {
    usage("Schema is required.");
}

if (empty($table = trim($options['table'] ?? ''))) {
    usage("Table is required.");
}

if (empty($filePath = trim($options['filePath'] ?? '.'))) {
    usage("File path is required.");
} else if (($realPath = realpath($filePath)) === false) {
    usage("Invalid file path: '$filePath'.");
} else if (!is_dir($realPath)) {
    usage("Invalid file path: '$filePath'.");
} else if (!is_writable($realPath)) {
    usage("File path is not writable: '$filePath'.");
} else {
    $filePath = $realPath;
}

if (empty($className = trim($options['className'] ?? tableNameToClassName($table)))) {
    echo "File name is required.\n";
    exit(1);
} else if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $className)) {
    usage("Invalid class name: '$className'.");
}

$fileName = $filePath . DIRECTORY_SEPARATOR . $className . '.php';

$pdoConnection = new PDO('mysql:host=mariadb;dbname=information_schema', 'root', 'mbdcdevRootPassword');
$mysqlConnection = new MysqlPdoConnection($pdoConnection);
$databaseStructureReader = new MysqlStructureReader($mysqlConnection);

$tableStructure = $databaseStructureReader->readTable($schema, $table);

print_r($tableStructure);
$newTableRecord = $tableStructure->constructTableRecord();

// $properties = [];

// $keySelector = function(Column $column, $key) {
//     return $column->name();
// };

// $valueSelector = function(Column $column, $key) {
//     $columnIndexType = $column->indexType();
//     $columnDefaultValue = $column->defaultValue();
    
//     if ($columnDefaultValue instanceof ColumnDefaultValue) {
//         if ($columnDefaultValue->value() == "NULL") {
//             $columnDefaultValue = "null";
//         } else {
//             $columnDefaultValue = "ColumnDefaultValue::" . $columnDefaultValue->value() . "()";
//         }
//     }

//     return [
//         "name" => $column->name(),
//         "phpType" => $column->type()->toPhpType($column->isNullable()),
//         "defaultValue" => $columnDefaultValue,
//         "indexType" => $columnIndexType === null ? "null" : "IndexType::" . $columnIndexType->name() . "()",
//         "columnType" => "ColumnType::" . $column->type()->value() . "()",
//         "isNullable" => $column->isNullable() ? "true" : "false",
//         "length" => $column->length() ?? "null"
//     ];
// };

// $properties = $tableStructure->columns()->select($valueSelector, $keySelector)->toArray();

// $addPropertiesArray = array_reduce($properties, function($carry, $property) {
//     $propertyName = $property['name'];
//     $propertyDefaultValue = $property['defaultValue'];
//     $propertyColumnType = $property['columnType'];
//     $propertyValidator = "function(\$value) { return Validator::validateColumnValue(\$value, $propertyColumnType, {$property['isNullable']}, {$property['length']}); }";

//     $carry[] = "\$this->addProperty('$propertyName', $propertyDefaultValue, $propertyColumnType, $propertyValidator);";
//     return $carry;
// }, []);

// $addPropertiesCode = trim(implode("\n        ", $addPropertiesArray));
// $setPropertiesValuesCode = trim(implode("\n        ", Enumerator::new($properties)->select(function($property, $index) {
//     return "\$this->setPropertyValue('{$property['name']}', \${$property['name']});";
// })->toArray()));

// $propertiesGetters = trim(Enumerator::new($properties)->select(function($property, $index) {
//     return "public function {$property['name']}(): {$property['phpType']} {\n        return \$this->getPropertyValue('{$property['name']}');\n    }";
// })->join("\n\n    "));

// $propertySetters = trim(Enumerator::new($properties)->select(function($property, $index) {
//     return "public function set{$property['name']}(?{$property['phpType']} \${$property['name']}) {\n        \$this->setPropertyValue('{$property['name']}', \${$property['name']});\n    }";
// })->join("\n\n    "));

// $constructorParameters = trim(Enumerator::new($properties)->select(function($property, $index) {
//     return $property['phpType'] . " \${$property['name']}";
// })->join(", "));

// print_r($properties);

// $namespace = schemaNameToNamespace($schema);

// $phpClassSource = <<<PHP

// <?php

// declare(strict_types=1);

// namespace $namespace;

// use Pst\Core\CoreObject;
// use Pst\Core\Traits\PropertiesArrayTrait;

// use Pst\Database\IndexType;
// use Pst\Database\ColumnType;
// use Pst\Database\ColumnDefaultValue;

// class $className extends CoreObject {
//     use PropertiesArrayTrait {
//         propertiesIterator as private;
//         addProperty as private;
//         propertyExists as private;
//         getPropertyNames as private;
//         getProperty as private;
//         getProperties as private;
//         getPropertyValue as private;
//         getPropertyValues as private;
//         setPropertyValue as private;
//         resetPropertyValue as private;
//     }

//     private string \$schemaName = '$schema';
//     private string \$tableName = '$table';

//     public function __consruct($constructorParameters) {

//         $addPropertiesCode

//         $setPropertiesValuesCode
//     }

//     public function schemaName(): string {
//         return \$this->schemaName;
//     }

//     public function tableName(): string {
//         return \$this->tableName;
//     }

//     $propertiesGetters

//     $propertySetters

//     public function validateValue(string \$name, \$value) {
        
//     }
// }

// PHP;

// // example usage php createTableClass.php --schema=sct2 --table=administrators --className=Administrator --filePath=.

// echo $phpClassSource;

// echo "Schema: $schema\n";
// echo "Table: $table\n";
// echo "Class Name: $className\n";
// echo "File Path: $filePath\n";
// echo "File Name: $fileName\n";

// file_put_contents($fileName, $phpClassSource);
