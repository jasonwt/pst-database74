<?php

declare(strict_types=1);

namespace Pst\Database\Query;

interface IAliasable {
    public function getAlias(): ?string;
}