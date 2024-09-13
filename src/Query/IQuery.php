<?php

declare(strict_types=1);

namespace Pst\Database\Query;

interface IQuery {
    public function getSql(): string;
    public function getParameters(): array;
}