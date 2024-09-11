<?php

declare(strict_types=1);

namespace Pst\Database\Query\Builder;

interface IGetQueryParts {
    public function getQuerySql(): string;
    public function getQueryParameters(): array;
}