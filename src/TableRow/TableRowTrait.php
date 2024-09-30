<?php

declare(strict_types=1);

namespace Pst\Database\TableRow;

use Pst\Core\Events\IEventSubscriptions;
use Pst\Core\Enumerable\Enumerable;
use Pst\Core\Collections\IReadonlyCollection;
use Pst\Core\Collections\ReadonlyCollection;
use Pst\Core\DynamicPropertiesObject\DynamicPropertiesObjectTrait;
use Pst\Database\Column\ColumnDefaultValue;

use Pst\Core\Exceptions\InvalidStateException;
use Pst\Database\Exceptions\QueryConstraintException;

use Traversable;
use InvalidArgumentException;

trait TableRowTrait {
    use TableRowInfoTrait;

    use DynamicPropertiesObjectTrait {
        propertyValuesSetEventObject as private;
        propertyExists as private;
        getPropertyValues as private;
        tryGetPropertyValue as private;
        getPropertyValue as private;
        setPropertyValues as private;
        setPropertyValue as private;
        
        fromInternalPropertyValue as private dynamicPropertiesObjectTraitFromInternalPropertyValue;
        __construct as private dynamicPropertiesObjectConstruct;
    }

    private array $tableRowTrait = [
        "isExistingDatabaseRecord" => false
    ];
    
    /**
     * Creates a new instance of the class using the TableRowTrait
     * 
     * @param iterable $columnValues
     * 
     * @throws InvalidArgumentException 
     */
    public function __construct(iterable $columnValues = []) {
        $columns = static::columns();

        $columnValues = ($columnValues instanceof Traversable) ? iterator_to_array($columnValues) : $columnValues;

        $columnValues = Enumerable::create($columnValues + static::defaultValues())->
            toReadonlyCollection();

        $invalidColumnNames = $columnValues->
            where(fn ($_, $columnName) => !$columns->containsKey($columnName))->
            select(fn ($_, $columnName) => $columnName)->
            toArray();

        if (count($invalidColumnNames) > 0) {
            throw new InvalidArgumentException("Invalid column names: " . implode(", ", $invalidColumnNames));
        }

        $this->dynamicPropertiesObjectConstruct($columnValues);

        $this->columnValuesSetEvent()->attach(function(object $sender, $columnName, $previousColumnValue, $newColumnValue) {
            if ($previousColumnValue !== $newColumnValue) {
                if (!$this->inSyncColumnValues()->containsKey($columnName)) {
                    $this->tableRowTrait["inSyncColumnValues"] = $this->inSyncColumnValues()->
                        append($previousColumnValue, $columnName)->
                        toReadonlyCollection();
                }
            }
        });
    }

    ///////////////////////////////////////////////////////////// PRIVATE METHODS //////////////////////////////////////////////////////////////
    

    /**
     * Filters the query parameters
     * 
     * @param iterable $queryParameters
     * 
     * @return IReadonlyCollection
     */
    private static function filterQueryParameters(iterable $queryParameters): IReadonlyCollection {
        return Enumerable::create($queryParameters)->select(function($columnValue, $_) {
            if ($columnValue instanceof ColumnDefaultValue) {
                if ($columnValue->value() === "NULL") {
                    return ColumnDefaultValue::NULL();
                }
            }

            return $columnValue;

        })->where(function($columnValue, $_) {
            return !$columnValue instanceof ColumnDefaultValue;
            
        })->toReadonlyCollection();
    }

    /**
     * Sanitizes the and validates the predicates
     * 
     * @param iterable $predicates
     * @param mixed $collectionOrErrors
     * 
     * @return bool
     */
    private static function sanitizePredicates(iterable $predicates, &$collectionOrErrors): bool {
        $errors = Enumerable::create($predicates)->
            select(function($_, $columnName) {
                if (!static::validatePropertyName($columnName = trim($columnName))) {
                    return "$columnName: invalid column name.";
                } else if (!static::columnExists($columnName)) {
                    return "$columnName: column does not exist.";
                }

                return null;
            })->
            where(fn($value) => $value !== null)->
            toArray();

        if (count($errors) > 0) {
            $collectionOrErrors = new InvalidArgumentException(implode("\n", $errors));
            return false;
        }

        $collectionOrErrors = static::filterQueryParameters(Enumerable::create($predicates));
        return true;
    }

    //////////////////////////////////////////////////////////// PROTECTED METHODS /////////////////////////////////////////////////////////////

    /**
     * Sets the value of the isExistingDatabaseRecord property
     * 
     * @param bool $isExistingDatabaseRecord
     */
    protected function setIsExistingDatabaseRecord(bool $isExistingDatabaseRecord) {
        $this->tableRowTrait["isExistingDatabaseRecord"] = $isExistingDatabaseRecord;
    }

    /**
     * Implementation specific method to convert from the input column value (setColumnValue, setColumnValues) to the internal column value
     * 
     * @param string $columnName
     * @param mixed $columnValue
     * @param string $exceptionMessage
     * 
     * @return bool
     */
    protected function fromInternalPropertyValue(string $columnName, $internalColumnValue) {
        if ($internalColumnValue instanceof ColumnDefaultValue) {
            if ($internalColumnValue->value() === "NULL") {
                return null;
            }
        }
        return $internalColumnValue;
    }

    /**
     * Implementation specific method to convert from the internal column value to the output column value (getColumnValue, getColumnValues)
     * 
     * @param string $columnName
     * @param mixed $externalColumnValue
     * 
     * @return mixed
     */
    protected function toInternalPropertyValue(string $columnName, $externalColumnValue) {
        if (is_null($externalColumnValue)) {
            $externalColumnValue = ColumnDefaultValue::NULL();
        }

        print_r($externalColumnValue);
        
        return $externalColumnValue;
    }

    protected function inSyncColumnValues(): IReadonlyCollection {
        return $this->tableRowTrait["inSyncColumnValues"] ??= ReadonlyCollection::empty();
    }

    // protected function fromDatabaseValue(string $columnName, $databaseValue) {
    //     return $databaseValue;
    // }

    // protected function toDatabaseValue(string $columnName, $internalValue) {
    //     return $internalValue;
    // }

    /**
     * Writes the objects column values to the database
     * 
     * @param mixed $columnValuesOrErrors
     * 
     * @return bool
     */
    protected function writeToDatabase(&$columnValuesOrErrors, bool $noThrow): bool {
        // the type of query that will be executed
        $queryType = $this->isExistingDatabaseRecord() ? "UPDATE" : "INSERT INTO";

        // the current column values
        $propertyValues = $this->getPropertyValues();

        // the column values that are in sync with the database but will be updated with the outOfSyncColumn Values
        $inSyncColumnValues = $this->inSyncColumnValues();

        // the column values that will replace the currently in sync column values
        $outOfSyncColumnValues = $this->getOutOfSyncColumnValues();

        // the auto incrementing column or null if there is none
        $autoIncrementingColumn = static::table()->autoIncrementingColumn();

        // the name of the auto incrementing column or null if there is none
        $autoIncreamentingColumnName = $autoIncrementingColumn !== null ? $autoIncrementingColumn->name() : null;
        
        // because $propertyValues replaces the existing database colume with the updated values to get
        // the current database column values we need to start with the previous column values and then
        // add the current column values to it (which will not replace the previous column values) with
        // the way php's array addition works
        $currentDatabaseColumnValues = ReadonlyCollection::create($inSyncColumnValues->toArray() + $propertyValues->toArray());
        
        $setEnumerator = $this->filterQueryParameters($queryType === "UPDATE" ? $outOfSyncColumnValues : $propertyValues);

        if ($queryType === "UPDATE" && $setEnumerator->isEmpty()) {
            $columnValuesOrErrors = null;
            return true;
        }

        $parametersArray = $setEnumerator->values()->toArray();

        $sql = "{$queryType} " . static::tableName() . 
            " SET " . $setEnumerator->select(fn($_, $columnName) => "$columnName=?")->join(", ");
        
        if ($queryType == "UPDATE") {
            $whereEnumerator = ($autoIncrementingColumn === null) ? $currentDatabaseColumnValues :
                ReadonlyCollection::create([$autoIncreamentingColumnName => $currentDatabaseColumnValues[$autoIncreamentingColumnName]]);

            if ($whereEnumerator->isEmpty()) {
                throw new InvalidStateException("to update a row, the row must have a primary key.");
            }

            $sql .= " WHERE " . $whereEnumerator->select(fn($_, $columnName) => "$columnName=?")->join(" AND ") . " LIMIT 1";

            $parametersArray = array_merge($parametersArray, $whereEnumerator->values()->toArray());
        }

        try {
            // perform the generated sql query
            static::db()->query($sql, $parametersArray);

            $newColumnValues = $propertyValues->toArray();

            // here we need to update the current objects column values for the database auto set value types (like auto incrementing columns, etc)
            foreach (static::columns() as $columnName => $column) {

                // skip any columns that are already in the pending database changes because they already exist in the object column values
                if ($outOfSyncColumnValues->containsKey($columnName)) {
                    continue;
                } 

                if ($queryType === "UPDATE") {
                    // if we are performing an update ...

                } else {
                    // if we are performing an insert ...
    
                    if ($columnName == $autoIncreamentingColumnName) {
                        $newColumnValues[$columnName] = static::db()->lastInsertId();
                    }
                                        
                    if ($propertyValues[$columnName] instanceof ColumnDefaultValue) {
                        // property values can only contain any special ColumnDefaultValue values if it
                        // is not an existing record in the database
                    }
                }
            }

            // replace the objects column values with the new column values
            $this->dynamicPropertiesObjectTrait["propertyValues"] = ReadonlyCollection::create($newColumnValues);

            if ($queryType == "UPDATE") {
                // now that the values have been updated in the database we can clear the previous column values
                $this->tableRowTrait["inSyncColumnValues"] = ReadonlyCollection::empty();
            } else {
                $this->setIsExistingDatabaseRecord(true);
            }

            // assign the new column values to the output parameter and return true
            $columnValuesOrErrors = $this->getPropertyValues();
            return true;
            
        } catch (QueryConstraintException $e) {
            $columnValuesOrErrors = $e;
        }

        if (!$noThrow) {
            throw $columnValuesOrErrors;
        }

        return false;
    }

    ////////////////////////////////////////////////////////////// PUBLIC METHODS //////////////////////////////////////////////////////////////

    /**
     * Returns the value of the isExistingDatabaseRecord property
     * 
     * @return bool
     */
    public function isExistingDatabaseRecord(): bool {
        return $this->tableRowTrait["isExistingDatabaseRecord"];
    }

    /**
     * Returns whether the object is in sync with the database
     * 
     * @return bool
     */
    public function isInSyncWithDatabase(): bool {
        return $this->isExistingDatabaseRecord() && $this->getOutOfSyncColumnValues()->isEmpty();
    }

    /**
     * returns the column changes that have not yet been saved to the database
     * 
     * @return IReadonlyCollection
     */
    public function getOutOfSyncColumnValues(): IReadonlyCollection {
        return $this->inSyncColumnValues()->select(function($columnValue, $columnName) {
            return $this->getPropertyValue($columnName);
        })->toReadonlyCollection();
    }

    /**
     * returns the event subscriptions for the column values set event
     * 
     * @return IEventSubscriptions
     */
    public function columnValuesSetEvent(): IEventSubscriptions {
        return $this->propertyValuesSetEventObject();
    }

    /**
     * returns the column values applying the internal to external conversion to each value
     * 
     * @return IReadonlyCollection
     */
    public function getColumnValues(): IReadonlyCollection {
        return $this->getPropertyValues();
    }

    /**
     * Tris to get the column value applying the internal to external conversion to the value
     * 
     * @param string $columnName
     * 
     * @return mixed
     */
    public function tryGetColumnValue(string $columnName, &$columnValue): bool {
        return $this->tryGetPropertyValue($columnName, $columnValue);
    }

    /**
     * returns the column value applying the internal to external conversion to the value
     * 
     * @param string $columnName
     * 
     * @return mixed
     */
    public function getColumnValue(string $columnName) {
        return $this->getPropertyValue($columnName);
    }

    /**
     * sets the column values applying the external to internal conversion to each value
     * 
     * @param iterable $columnValues
     * @param bool $noThrow
     * 
     * @return bool
     */
    public function setColumnValues(iterable $columnValues, bool $noThrow = false): bool {
        return $this->setPropertyValues($columnValues, $noThrow);
    }

    /**
     * sets the column value applying the external to internal conversion to the value
     * 
     * @param string $columnName
     * @param mixed $columnValue
     * @param bool $noThrow
     * 
     * @return bool
     */
    public function setColumnValue(string $columnName, $columnValue, bool $noThrow = false): bool {
        return $this->setPropertyValue($columnName, $columnValue, $noThrow);
    }

    /**
     * reads a single row from the database as an instance of the class that uses the TableRowTrait
     * 
     * @param iterable $predicates
     * @param mixed $tableRowOrException
     * @param bool $noThrow
     * 
     * @return bool
     * 
     * @throws InvalidArgumentException
     */
    public static function read(iterable $predicates, &$tableRowOrException, $noThrow = false): bool {
        $errors = [];

        if (!static::sanitizePredicates($predicates, $collectionOrErrors)) {
            $errors = $collectionOrErrors;

        } else {
            $predicates = $collectionOrErrors;

            if (count($predicates) === 0) {
                $errors["predicates"] = new InvalidArgumentException("No predicates provided.");
            }
        }

        if (count($errors) === 0) {
            $sql = "SELECT * FROM " . static::tableName() . " WHERE " . Enumerable::create($predicates)->
                select(fn($_, $key) => "$key = ?")->
                join(" AND ");

            try {
                $result = static::db()->query($sql, $predicates->values()->toArray())->toArray();

                if (count($result) === 0) {
                    $errors["result"] = new InvalidArgumentException("not found.");
                } else if (count($result) > 1) {
                    $errors["result"] = new InvalidArgumentException("multiple rows found.");
                } else {
                    $tableRowOrException = new static($result[0]);
                    return true;
                }
            } catch (\Exception $e) {
                $error["excpetion"] = $e;
            }
        }

        $tableRowOrException = new InvalidArgumentException(implode("\n", $errors));

        if ($noThrow) {
            return false;
        } else {
            throw $tableRowOrException;
        }
    }

    /**
     * reads multiple rows from the database
     * 
     * @param iterable $predicates
     * @param mixed $listOrException
     * @param bool $noThrow
     * 
     * @return bool
     * 
     * @throws InvalidArgumentException
     */
    public static function list(iterable $predicates, &$listOrException, $noThrow = false): bool {
        $errors = [];

        if (!static::sanitizePredicates($predicates, $collectionOrErrors)) {
            $errors = $collectionOrErrors;

        } else {
            $predicates = $collectionOrErrors;

            $sql = "SELECT * FROM " . static::tableName();

            if (count($predicates) > 0) {
                $sql .= " WHERE " . Enumerable::create($predicates)->
                    select(fn($_, $key) => "$key = ?")->
                    join(" AND ");
            }
            
            try {
                $result = static::db()->query($sql, $predicates->values()->toArray())->toArray();

                $listOrException = ReadonlyCollection::create($result);
                return true;

            } catch (\Exception $e) {
                $error["excpetion"] = $e;
            }
        }

        $listOrException = new InvalidArgumentException(implode("\n", $errors));

        if ($noThrow) {
            return false;
        } else {
            throw $listOrException;
        }
    }
}