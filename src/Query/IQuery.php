<?php

declare(strict_types=1);

namespace Pst\Database\Query;

interface IQuery {
    public function getParameters(): array;
    public function getSql(): string;
}