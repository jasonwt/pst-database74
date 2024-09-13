<?php

declare(strict_types=1);

namespace Pst\Database\Query;

interface IQueryable {
    public function getQuerySql(): string;
    public function getQueryParameters(): array;
}