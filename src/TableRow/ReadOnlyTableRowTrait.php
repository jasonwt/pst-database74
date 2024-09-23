<?php

declare(strict_types=1);

namespace Pst\Database\TableRow;

use Pst\Core\Events\IEventSubscriptions;
use Pst\Core\Enumerable\Enumerator;
use Pst\Core\Collections\IReadonlyCollection;
use Pst\Core\DynamicPropertiesObject\DynamicPropertiesObjectTrait;

use Pst\Database\Column\ColumnDefaultValue;

use Traversable;
use InvalidArgumentException;

trait aTableRowTrait {
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

    public function __construct(iterable $columnValues = []) {
        $columns = static::columns();

        $columnValues = ($columnValues instanceof Traversable) ? iterator_to_array($columnValues) : $columnValues;

        $columnValues = Enumerator::new($columnValues + static::defaultValues())->
            toReadonlyCollection();

        $invalidColumnNames = $columnValues->
            where(fn ($_, $columnName) => !$columns->containsKey($columnName))->
            select(fn ($_, $columnName) => $columnName)->
            toArray();

        if (count($invalidColumnNames) > 0) {
            throw new InvalidArgumentException("Invalid column names: " . implode(", ", $invalidColumnNames));
        }

        $this->dynamicPropertiesObjectConstruct($columnValues);
    }

    protected function fromInternalPropertyValue(string $columnName, $internalColumnValue) {
        if ($internalColumnValue instanceof ColumnDefaultValue) {
            if ($internalColumnValue->value() === "NULL") {
                return null;
            }
        }
        return $internalColumnValue;
    }

    protected function toInternalPropertyValue(string $columnName, $externalColumnValue) {
        if (is_null($externalColumnValue)) {
            $externalColumnValue = ColumnDefaultValue::NULL();
        }

        print_r($externalColumnValue);
        
        return $externalColumnValue;
    }

    protected function validatePropertyValue(string $columnName, $columnValue): bool {
        $this->exceptions()->clearExceptions(__FUNCTION__);

        if (!static::validateColumnValue($columnName, $columnValue, $exceptionMessage)) {
            $this->exceptions()->addException(__FUNCTION__, new InvalidArgumentException($exceptionMessage), $columnName);
            return false;
        }

        return true;
    }

    public function columnValuesSetEvent(): IEventSubscriptions {
        return $this->propertyValuesSetEventObject();
    }

    public function getColumnValues(): IReadonlyCollection {
        return $this->getPropertyValues();
    }

    public function tryGetColumnValue(string $columnName, &$columnValue): bool {
        return $this->tryGetPropertyValue($columnName, $columnValue);
    }

    public function getColumnValue(string $columnName) {
        return $this->getPropertyValue($columnName);
    }

    public function setColumnValues(iterable $columnValues, bool $noThrow = true): bool {
        return $this->setPropertyValues($columnValues, $noThrow);
    }

    public function setColumnValue(string $columnName, $columnValue, bool $noThrow = true): bool {
        return $this->setPropertyValue($columnName, $columnValue, $noThrow);
    }

    // public static function create(iterable $columnValues, &$tableRowOrError, $noThrow = true): bool {
    // //     $tableRowOrError = null;

    // //     $errors = [];

    // //     $columnValues = Enumerator::new($columnValues)->
    // //         selectKey(function($columnValue, $columnName) use ($errors) {
    // //             if (!static::validatePropertyName($columnName = trim($columnName))) {
    // //                 $errors[$columnName] = "$columnName: invalid column name.";
    // //             } else if (!static::columnExists($columnName)) {
    // //                 $errors[$columnName] = "$columnName: column does not exist.";
    // //             }

    // //             $columnValue = static::toInternal

    // //             if (!static::validateColumnValue($columnName, $columnValue, $exceptionMessage)) {
    // //                 $errors[$columnName] = $exceptionMessage;
    // //             }

    // //             return $columnName;
    // //         })->


    // //         toArray(function($columnValue, $columnName) use ($errors) {
    // //             if (!static::validateColumn)
    // //             return [$columnName, $columnValue];
    // //         });
    // //     $columnValues += static::defaultValues();

    // //     $invalidColumnNames = Enumerator::new($columnValues)->
    // //         where(function ($_, $columnName) {
    // //             return !static::columnExists($columnName);
    // //         })->
    // //         select(fn ($_, $columnName) => $exceptionMessage)->
    // //         toArray();
    // }
}