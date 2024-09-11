<?php

declare(strict_types=1);

interface IQueryPart {
    public function getParameters(): array;
    public function getSql(): string;
}