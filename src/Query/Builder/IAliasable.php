<?php

declare(strict_types=1);

namespace Pst\Database\Query\Builder;

interface IAliasable {
    public function getAlias(): ?string;
}