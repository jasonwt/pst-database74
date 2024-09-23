<?php

declare(strict_types=1);

namespace Pst\Database\TableRow;

use Pst\Core\Interfaces\IToString;
use Pst\Core\Collections\IReadonlyCollection;

use Pst\Database\DB;
use Pst\Database\Column\ColumnDefaultValue;
use Pst\Database\Table\Table;
use Pst\Database\Schema\Schema;
use Pst\Database\Schema\ISchemaReader;
use Pst\Database\Connections\IDatabaseConnection;

use Pst\Core\Exceptions\NotImplementedException;

use InvalidArgumentException;


trait TableRowInfoTrait {
    private static array $staticTableRowTrait = [];

    protected static function db(?IDatabaseConnection $db = null): ?IDatabaseConnection {
        return $db ?? DB::db();
    }

    protected static function dbs(?ISchemaReader $dbs = null): ?ISchemaReader {
        return $dbs ?? DB::dbs();
    }

    public static abstract function schemaName(): string;

    public static function tableName(): string {
        if (isset(static::$staticTableRowTrait['tableName'])) {
            return static::$staticTableRowTrait['tableName'];
        }

        $classNameArray = explode("\\", static::class);
        $className = $classNameArray[count($classNameArray) - 1];
        $namespace = (count($classNameArray) > 1) ? implode("\\", array_slice($classNameArray, 0, count($classNameArray) - 1)) : null;

        if (empty($pascalCaseTerms = trim($className))) {
            throw new InvalidArgumentException("Word cannot be empty");
        }

        // from snake case from pascal case
        if (empty($snakeCaseTerms = strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $pascalCaseTerms)))) {
            throw new InvalidArgumentException("Word cannot be empty");
        }

        // convert the PascalCase class name to snake_case

        // Same word in plural
        $directReplacements = [
            'advice' => 'advice', 'alms' => 'alms', 'aircraft' => 'aircraft', 'aluminum' => 'aluminum', 'barracks' => 'barracks', 'bison' => 'bison', 'binoculars' => 'binoculars',
            'bourgeois' => 'bourgeois', 'breadfruit' => 'breadfruit', 'cannon' => 'cannon', 'caribou' => 'caribou', 'cattle' => 'cattle', 'chalk' => 'chalk', 'chassis' => 'chassis', 'chinos' => 'chinos',
            'clippers' => 'clippers', 'clothing' => 'clothing', 'cod' => 'cod', 'concrete' => 'concrete', 'corps' => 'corps', 'correspondence' => 'correspondence', 'crossroads' => 'crossroads', 'deer' => 'deer',
            'dice' => 'dice', 'doldrums' => 'doldrums', 'dungarees' => 'dungarees', 'education' => 'education', 'eggfruit' => 'eggfruit', 'elk' => 'elk', 'eyeglasses' => 'eyeglasses', 'fish' => 'fish',
            'flares' => 'flares', 'flour' => 'flour', 'food' => 'food', 'fruit' => 'fruit', 'furniture' => 'furniture', 'gallows' => 'gallows', 'goldfish' => 'goldfish', 'grapefruit' => 'grapefruit',
            'greenfly' => 'greenfly', 'grouse' => 'grouse', 'haddock' => 'haddock', 'halibut' => 'halibut', 'headquarters' => 'headquarters', 'help' => 'help', 'homework' => 'homework', 'hovercraft' => 'hovercraft',
            'ides' => 'ides', 'insignia' => 'insignia', 'jeans' => 'jeans', 'knickers' => 'knickers', 'knowledge' => 'knowledge', 'kudos' => 'kudos', 'leggings' => 'leggings', 'lego' => 'lego',
            'luggage' => 'luggage', 'moose' => 'moose', 'monkfish' => 'monkfish', 'mullet' => 'mullet', 'nailclippers' => 'nailclippers', 'news' => 'news', 'offspring' => 'offspring', 'oxygen' => 'oxygen',
            'pants' => 'pants', 'pyjamas' => 'pyjamas', 'passionfruit' => 'passionfruit', 'pike' => 'pike', 'pliers' => 'pliers', 'police' => 'police', 'premises' => 'premises', 'reindeer' => 'reindeer',
            'rendezvous' => 'rendezvous', 'salmon' => 'salmon', 'scissors' => 'scissors', 'series' => 'series', 'shambles' => 'shambles', 'sheep' => 'sheep', 'shellfish' => 'shellfish', 'shorts' => 'shorts',
            'shrimp' => 'shrimp', 'smithereens' => 'smithereens', 'spacecraft' => 'spacecraft', 'species' => 'species', 'squid' => 'squid', 'starfruit' => 'starfruit', 'stone' => 'stone', 'sugar' => 'sugar',
            'swine' => 'swine', 'tongs' => 'tongs', 'trousers' => 'trousers', 'trout' => 'trout', 'tuna' => 'tuna', 'tweezers' => 'tweezers', 'you' => 'you', 'wheat' => 'wheat', 'whitebait' => 'whitebait',
            'wood' => 'wood'
        ];

        // Irregular plurals and other exceptions
        $directReplacements += [
            'abyss' => 'abysses', 'alumnus' => 'alumni', 'analysis' => 'analyses', 'aquarium' => 'aquaria', 'arch' => 'arches', 'atlas' => 'atlases', 'axe' => 'axes', 'baby' => 'babies',
            'bacterium' => 'bacteria', 'batch' => 'batches', 'beach' => 'beaches', 'brush' => 'brushes', 'bus' => 'buses', 'calf' => 'calves', 'chateau' => 'chateaux', 'cherry' => 'cherries',
            'child' => 'children', 'church' => 'churches', 'circus' => 'circuses', 'city' => 'cities', 'copy' => 'copies', 'crisis' => 'crises', 'curriculum' => 'curricula', 'deer' => 'deer',
            'dictionary' => 'dictionaries', 'domino' => 'dominoes', 'dwarf' => 'dwarves', 'echo' => 'echoes', 'elf' => 'elves', 'emphasis' => 'emphases', 'family' => 'families', 'fax' => 'faxes',
            'fish' => 'fish', 'foot' => 'feet', 'fungus' => 'fungi', 'goose' => 'geese', 'half' => 'halves', 'hero' => 'heroes', 'hippopotamus' => 'hippopotami', 'hoax' => 'hoaxes',
            'hoof' => 'hooves', 'index' => 'indexes', 'iris' => 'irises', 'kiss' => 'kisses', 'knife' => 'knives', 'leaf' => 'leaves', 'life' => 'lives', 'loaf' => 'loaves',
            'man' => 'men', 'mango' => 'mangoes', 'memorandum' => 'memoranda', 'moose' => 'moose', 'mouse' => 'mice', 'neurosis' => 'neuroses', 'nucleus' => 'nuclei', 'oasis' => 'oases',
            'octopus' => 'octopi', 'party' => 'parties', 'penny' => 'pennies', 'person' => 'people', 'plateau' => 'plateaux', 'potato' => 'potatoes', 'quiz' => 'quizzes', 'reflex' => 'reflexes',
            'runner-up' => 'runners-up', 'scarf' => 'scarves', 'sheaf' => 'sheaves', 'sheep' => 'sheep', 'shelf' => 'shelves', 'species' => 'species', 'spy' => 'spies', 'story' => 'stories',
            'syllabus' => 'syllabi', 'tax' => 'taxes', 'thesis' => 'theses', 'thief' => 'thieves', 'tomato' => 'tomatoes', 'tooth' => 'teeth', 'tornado' => 'tornadoes', 'try' => 'tries',
            'volcano' => 'volcanoes', 'waltz' => 'waltzes', 'wife' => 'wives', 'woman' => 'women'
        ];

        foreach ($directReplacements as $key => $value) {
            if ($snakeCaseTerms === $key) {
                return static::$staticTableRowTrait['tableName'] ??= $value;
            }

            $toMatches = "_{$key}";

            if (substr($snakeCaseTerms, -strlen($toMatches)) === $toMatches) {
                return static::$staticTableRowTrait['tableName'] ??= (substr($snakeCaseTerms, 0, -strlen($toMatches)) . "_" . $value);
            }
        }

        // Handling irregular suffixes
        $suffixRules = [
            '/us$/' => 'i',    // alumnus -> alumni
            '/is$/' => 'es',   // crisis -> crises
            '/on$/' => 'a',    // criterion -> criteria
            '/um$/' => 'a',    // bacterium -> bacteria
            '/a$/' => 'ae',    // antenna -> antennae
            '/ex$/' => 'ices', // apex -> apices
            '/x$/' => 'ces',   // matrix -> matrices
            '/y$/' => function($word) {
                return static::$staticTableRowTrait['tableName'] ??= (substr($word, 0, -1) . 'ies'); // city -> cities (but day -> days)
            },
            '/fe$/' => 'ves',  // knife -> knives
            '/f$/' => 'ves'    // leaf -> leaves
        ];

        // Apply suffix rules
        foreach ($suffixRules as $pattern => $replacement) {
            if (preg_match($pattern, $snakeCaseTerms)) {
                return static::$staticTableRowTrait['tableName'] ??= (is_callable($replacement) ? $replacement($snakeCaseTerms) : preg_replace($pattern, $replacement, $snakeCaseTerms));
            }
        }

        // Default plural form: just add 's'
        return $snakeCaseTerms . "s";
    }

    public static function columnExists(string $columnName): bool {
        return static::columns()->containsKey($columnName);
    }

    public static function schema(?ISchemaReader $dbs = null): Schema {
        return static::$staticTableRowTrait['schema'] ??= 
            static::dbs($dbs)->readSchema(static::schemaName());
    }
    public static function table(?ISchemaReader $dbs = null): Table {
        return static::$staticTableRowTrait['schema'] ??= 
        static::dbs($dbs)->readTable(static::schemaName(), static::tableName());
    }
    public static function columns(): IReadonlyCollection {
        return static::$staticTableRowTrait['columns'] ??= static::table()->columns();
    }
    public static function indexes(): IReadonlyCollection {
        return static::$staticTableRowTrait['indexes'] ??= static::table()->indexes();
    }

    public static function defaultValues(): array {
        return static::$staticTableRowTrait['defaultValues'] ??= static::columns()->
            select(fn($column) => $column->defaultValue())
            ->toArray();
    }

    /**
     * Validates a column value against the column type.
     * 
     * @param string $columnName 
     * @param mixed $columnValue 
     * @param string $exceptionMessage
     * 
     * @return bool
     * 
     * @throws InvalidArgumentException 
     * @throws NotImplementedException 
     */
    public static function validateColumnValue(string $columnName, $columnValue, string &$exceptionMessage = null): bool {
        $columns = static::columns();

        if (($column = $columns[$columnName = trim($columnName)] ?? null) === null) {
            $exceptionMessage = "Invalid column name: '{$columnName}'";
            return false;
        }

        $columnType = $column->type();
        $columnLength = $column->length();
        $isNullable = $column->isNullable();

        $columnString = $columnType->toString() . ($columnLength !== null ? "({$columnLength})" : "");

        if ($columnValue instanceof ColumnDefaultValue) {
            if ($columnValue == ColumnDefaultValue::NONE()) {
                if ($columnType->isAutoIncrementing()) {
                    return true;
                }
                $exceptionMessage = "'{$columnName}': is required.";
                return false;
            } else if ($columnValue == ColumnDefaultValue::NULL()) {
                if ($column->isNullable()) {
                    return true;
                }

                $exceptionMessage = "'{$columnName}': is not nullable.";
                return false;
            } else if ($columnValue == ColumnDefaultValue::CURRENT_TIMESTAMP()) {
                return true;
            } else if ($columnValue == ColumnDefaultValue::UUID()) {
                throw new NotImplementedException("UUID not implemented.");
            }
        }

        if ($columnValue === null) {
            if ($isNullable) {
                return true;
            } else {
                $exceptionMessage = "'{$columnName}': can not be null";
                return false;
            }
        }

        if ($columnType->isStringType()) {
            $maxCharacterLength = $column->length();

            if (is_object($columnValue)) {
                if (!$columnValue instanceof IToString) {
                    $exceptionMessage = "'{$columnName}': can not assign object of type: " . get_class($columnValue) . " to column type: {$columnString}";
                    return false;
                }

                $columnValue = $columnValue->toString();
            } else if (!is_string($columnValue)) {
                $exceptionMessage = "'{$columnName}': can not assign value type: " . gettype($columnValue) . " to column type: {$columnString}";
                return false;
            }

            if ($maxCharacterLength !== null && strlen($columnValue) > $maxCharacterLength) {
                $columnValueLength = strlen($columnValue);
                $columnValue = (strlen($columnValue) > 50) ? substr($columnValue, 0, 50) . "..." : $columnValue;

                $exceptionMessage = "'{$columnName}': value: '{$columnValue}' length: {$columnValueLength} exceeds maximum character length: {$maxCharacterLength}";
                return false;
            }
        } else if ($columnType->isNumericType()) {
            $isIntegerType = $columnType->isIntegerType();

            $columnTypeIsUnsigned = $columnType->isUnsigned();

            if (is_string($columnValue)) {
                if (!is_numeric($columnValue)) {
                    $exceptionMessage = "'{$columnName}': can not assign non-numeric value: '{$columnValue}' to column type: {$columnString}";
                    return false;
                }

                $columnValue = (strpos($columnValue, ".") !== false) ? (float) $columnValue : (int) $columnValue;
            }
            
            if (is_float($columnValue)) {
                if (strpos((string) $columnValue, ".") !== false && $isIntegerType) {
                    $exceptionMessage = "'{$columnName}': can not assign floating point value: {$columnValue} to column type: {$columnString}";
                    return false;
                }

                $columnValue = $isIntegerType ? (int) $columnValue : (float) $columnValue;
            } else if (is_int($columnValue)) {
                $columnValue = $isIntegerType ? (int) $columnValue : (float) $columnValue;
            } else {
                $exceptionMessage = "'{$columnName}': can not assign value type: " . gettype($columnValue) . " to column type: {$columnString}";
                return false;
            }

            if ($columnValue < 0 && $columnTypeIsUnsigned) {
                $exceptionMessage = "'{$columnName}': can not assign negative value: {$columnValue} to column type {$columnString}.";
                return false;
            }

            if ($isIntegerType) {
                $maxValue = $columnType->isTinyInt() ? 127 : (
                    $columnType->isSmallInt() ? 32767 : (
                        $columnType->isMediumInt() ? 8388607 : (
                            $columnType->isInt() ? 2147483647 : 9223372036854775807
                        )
                    )
                );

                $minValue = -($maxValue + 1);

                if ($columnTypeIsUnsigned) {
                    $maxValue = ($maxValue * 2) + 1;
                    $minValue = 0;
                }

                if ($columnValue < $minValue || $columnValue > $maxValue) {
                    $exceptionMessage = "'{$columnName}': value: $columnValue out of range for column type: {$columnString}";
                    return false;
                }
            }
        } else {
            throw new NotImplementedException("Type: {$columnString} is not implemented");
        }

        return true;
    }
}