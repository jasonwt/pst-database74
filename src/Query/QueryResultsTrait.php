<?php

declare(strict_types=1);

namespace Pst\Database\Query;

use Closure;
use Pst\Core\Types\ITypeHint;

use Pst\Core\Collections\Traits\CollectionTrait;
use Pst\Core\Types\TypeHintFactory;

use InvalidArgumentException;

trait QueryResultsTrait {
    use CollectionTrait {
        tryAdd as private;
        add as private;
        clear as private;
        remove as private;
        offsetSet as private collectionTraitOffsetSet;
        offsetUnset as private collectionTraitOffsetUnset;
        current as private;
        key as private;
        next as private;
        rewind as private;
        valid as private;
        count as private collectionTraitCount;
        __construct as protected collectionTraitConstruct;
    }

    private $iterable = null;
    private ?int $resultsCount = null;
    private int $rowCount = 0;
    private int $columnCount = 0;

    public function T(): ITypeHint {
        return TypeHintFactory::tryParse("array");
    }

    public function fetch(): ?array {
        $value = $this->current();
        $this->next();
        return $value;
    }

    public function __construct($iterable, int $rowCount, int $columnCount, ?int $resultsCount = null) {
        if (($this->rowCount = $rowCount) < 0) {
            throw new InvalidArgumentException("rowCount argument must be greater than or equal to 0.");
        }

        if (($this->columnCount = $columnCount) < 0) {
            throw new InvalidArgumentException("columnCount argument must be greater than or equal to 0.");
        }

        if (($this->resultsCount = $resultsCount) !== null && $resultsCount < 0) {
            throw new InvalidArgumentException("resultsCount argument must be greater than or equal to 0.");
        }

        $this->collectionTraitConstruct($iterable, TypeHintFactory::tryParse("array"));
    }

    public function count(?Closure $predicate = null): int {
        if ($predicate !== null) {
            return $this->linqCount($predicate);
        }

        return $this->resultsCount ?? $this->collectionTraitCount();
    }

    public function rowCount(): int {
        return $this->rowCount;
    }

    public function columnCount(): int {
        return $this->columnCount;
    }
}