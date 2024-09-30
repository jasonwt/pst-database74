<?php

declare(strict_types=1);

namespace Pst\Database\Query;

use Pst\Core\CoreObject;
use Pst\Core\Types\ITypeHint;
use Pst\Core\Types\TypeHintFactory;
use Pst\Core\Enumerable\IEnumerable;
use Pst\Core\Enumerable\Linq\EnumerableLinqTrait;
use Pst\Core\Enumerable\Iterators\CountableIterator;

use Closure;
use Iterator;
use ArrayIterator;
use IteratorAggregate;

class QueryResults extends CoreObject implements IteratorAggregate, IEnumerable, IQueryResults {
    use EnumerableLinqTrait {
        count as private linqCount;
    }

    private CountableIterator $resultsIterator;
    private int $rowCount = 0;
    private int $columnCount = 0;

    public function __construct(iterable $source, int $rowCount, int $columnCount) {
        if (is_array($source)) {
            $this->resultsIterator = new ArrayIterator($source);
        } else {
            if (!$source instanceof CountableIterator) {
                $source = new CountableIterator($source);
            }
        }

        $this->rowCount = $rowCount;
        $this->columnCount = $columnCount;
        $this->resultsIterator = $source;
    }

    public function T(): ITypeHint {
        return TypeHintFactory::array();
    }

    public function TKey(): ITypeHint{
        return TypeHintFactory::keyTypes();
    }

    public function isRewindable(): bool {
        return false;
    }

    public function getIterator(): Iterator {
        return $this->resultsIterator;
    }

    public function fetchNext(): ?array {
        if ($this->resultsIterator->valid()) {
            $value = $this->resultsIterator->current();
            $this->resultsIterator->next();
            return $value;
        }

        return null;
    }

    public function fetchAll(): array {
        return iterator_to_array($this->resultsIterator);
    }

    public function rowCount(): int {
        if ($this->rowCount === 0) {
            $this->rowCount = count($this->resultsIterator);
        }

        return $this->rowCount;
    }

    public function columnCount(): int {
        return $this->columnCount;
    }

    public function count(?Closure $predicate = null): int {
        if ($predicate !== null) {
            return $this->linqCount($predicate);
        }

        return $this->rowCount;
    }
}