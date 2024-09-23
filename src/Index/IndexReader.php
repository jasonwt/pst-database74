<?php

declare(strict_types=1);

namespace Pst\Database\Index;

abstract class IIndexReader implements IIndexReader {
    use IndexReaderTrait;
}