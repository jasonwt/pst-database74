<?php

declare(strict_types=1);

namespace Pst\Database\Query;

use Pst\Core\Types\ITypeHint;
use Pst\Core\Collections\Traits\LinqTrait;
use Pst\Core\Exceptions\NotImplementedException;

use Closure;
use Iterator;
use Traversable;

class QueryResults implements IQueryResults {
    use LinqTrait {
        count as private linqCount;
    }

    private Iterator $resultsIterator;
    private ?int $resultsCount = null;
    private array $resultsArray = [];
    
    private int $rowCount = 0;
    private int $columnCount = 0;

    public function __construct(Iterator $resultsIterator, int $rowCount, int $columnCount) {
        $this->resultsIterator = $resultsIterator;

        $this->rowCount = $rowCount;
        $this->columnCount = $columnCount;
    }

    public function count(?Closure $predicate = null): int {
        while ($this->fetchNext() !== null);
        
        if ($predicate === null) {
            return count($this->resultsArray);
        }
        
        return $this->linqCount($predicate);
    }

    public function T(): ITypeHint {
        throw new NotImplementedException();
    }

    public function getIterator(): Traversable {
        for ($i = 0; !is_null($value = ($this->resultsArray[$i] ?? $this->fetchNext())); $i ++) {
            yield $value;
        }
    }

    public function fetchNext(): ?array {
        if ($this->resultsIterator->valid()) {
            $value = $this->resultsIterator->current();

            $this->resultsArray[] = $value;
            $this->resultsIterator->next();
            return $value;
        }

        $this->resultsCount ??= count($this->resultsArray);

        return null;
    }

    public function fetchAll(): array {
        while ($this->fetchNext() !== null);

        return $this->resultsArray;
    }

    public function rowCount(): int {
        return $this->rowCount;
    }

    public function columnCount(): int {
        return $this->columnCount;
    }
}