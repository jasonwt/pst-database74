<?php

declare(strict_types=1);

namespace Pst\Database\Structure\Column;

use Pst\Core\CoreObject;

use Pst\Database\Enums\IndexType;
use Pst\Database\Enums\ColumnType;
use Pst\Database\Enums\ColumnDefaultValue;
use Pst\Database\Structure\Validator;

use Pst\Core\Exceptions\NotImplementedException;

use InvalidArgumentException;

class Column extends CoreObject{
    private string $schemaName;
    private string $tableName;
    private string $name;
    private ColumnType $type;
    private ?int $length;
    private $defaultValue;
    private bool $isNullable;
    private ?IndexType $indexType;

    /**
     * Constructs a new instance of Column
     * 
     * @param string $schemaName 
     * @param string $tableName 
     * @param string $name 
     * @param ColumnType $type 
     * @param null|int $length 
     * @param int|string|ColumnDefaultValue $defaultValue 
     * @param bool $isNullable 
     * @param null|IndexType $indexType 
     * 
     * @return void 
     * 
     * @throws InvalidArgumentException 
     */
    public function __construct(string $schemaName, string $tableName, string $name, ColumnType $type, ?int $length = null, $defaultValue = null, bool $isNullable = false,  ?IndexType $indexType = null) {
        $defaultValue ??= ColumnDefaultValue::NONE();

        if (Validator::validateSchemaName($this->schemaName = $schemaName) !== true) {
            throw new \InvalidArgumentException("Invalid schema name: '$schemaName'.");
        }

        if (Validator::validateTableName($this->tableName = $tableName) !== true) {
            throw new \InvalidArgumentException("Invalid table name: '$tableName'.");
        }

        if (Validator::validateColumnName($this->name = $name) !== true) {
            throw new \InvalidArgumentException("Invalid column name: '$name'.");
        }

        $this->type = $type;
        $this->length = $length;
        $this->defaultValue = $defaultValue;
        $this->isNullable = $isNullable;
        $this->indexType = $indexType;
    }

    public function schemaName(): string {
        return $this->schemaName;
    }

    public function tableName(): string {
        return $this->tableName;
    }

    public function name(): string {
        return $this->name;
    }

    public function type(): ColumnType {
        return $this->type;
    }

    public function length(): ?int {
        return $this->length;
    }

    /**
     * Get the default value of the column
     * 
     * @return int|string|ColumnDefaultValue
     */
    public function defaultValue() {
        return $this->defaultValue;
    }

    public function isNullable(): bool {
        return $this->isNullable;
    }

    public function indexType(): ?IndexType {
        return $this->indexType;
    }

    public function tryGetDefaultEvaluatedValue(&$evaluatedValue): bool {
        $columnType = $this->type();
        $columnDefaultValue = $this->defaultValue();

        if (!$columnDefaultValue instanceof ColumnDefaultValue) {
            $evaluatedValue = $columnDefaultValue;
        } else {
            if ($columnDefaultValue == ColumnDefaultValue::NONE()) {
                $evaluatedValue = null;
                return false;
            } else if ($columnDefaultValue == ColumnDefaultValue::NULL()) {
                $evaluatedValue = null;
            } else if ($columnDefaultValue == ColumnDefaultValue::CURRENT_TIMESTAMP()) {
                if ($columnType == ColumnType::TIMESTAMP()) {
                    $evaluatedValue = time();
                } else if ($columnType == ColumnType::DATETIME()) {
                    $evaluatedValue = date("Y-m-d H:i:s");
                } else if ($columnType == ColumnType::DATE()) {
                    $evaluatedValue = date("Y-m-d");
                } else if ($columnType == ColumnType::TIME()) {
                    $evaluatedValue = date("H:i:s");
                }
            } else {
                $evaluatedValue = null;
                return false;
            }
        }

        return true;
    }

    public function tryGetEvaluatedValue($value, &$evaluatedValue): bool {
        $columnType = $this->type();

        throw new NotImplementedException("Not implemented");
    }


    public function defaultPhpValue() {
        if ($this->defaultValue === ColumnDefaultValue::NULL()) {
            return null;
        } else if ($this->defaultValue === ColumnDefaultValue::NONE()) {
            if ($this->type()->isIntegerType()) {
                return 0;
            } else if ($this->type()->isStringType()) {
                return "";
            } else {
                throw new NotImplementedException("Default value not implemented");
            }
        } else if ($this->defaultValue === ColumnDefaultValue::CURRENT_TIMESTAMP()) {
            return time();
        } else if ($this->defaultValue === ColumnDefaultValue::UUID()) {
            throw new NotImplementedException("UUID not implemented");
        } else {
            return $this->defaultValue;
        }
    }
}