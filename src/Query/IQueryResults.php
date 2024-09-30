<?php

declare(strict_types=1);

namespace Pst\Database\Query;

use Pst\Core\Enumerable\IEnumerable;

use Countable;

interface IQueryResults extends IEnumerable, Countable {
    public function fetchNext(): ?array;
    public function fetchAll(): array;
    public function rowCount(): int;
    public function columnCount(): int;
}