<?php

declare(strict_types=1);

namespace Pst\Database\Query;

use Pst\Core\Collections\IEnumerable;

interface IQueryResults extends IEnumerable {
    public function fetchNext(): ?array;
    public function fetchAll(): array;
    public function rowCount(): int;
    public function columnCount(): int;
}