<?php

declare(strict_types=1);

namespace Pst\Database\Structure;

use Pst\Core\CoreObject;

use Pst\Database\Validator;

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
}