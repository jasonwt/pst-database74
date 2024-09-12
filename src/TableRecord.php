<?php

declare(strict_types=1);

namespace Pst\Database;

use Pst\Core\CoreObject;
use Pst\Core\Types\TypeHintFactory;
use Pst\Core\Collections\IEnumerable;
use Pst\Core\Collections\IReadOnlyCollection;
use Pst\Core\Collections\ReadOnlyCollection;

use Pst\Database\Structure\Column;

abstract class TableRecord extends CoreObject implements ITableRecord {
    private static ?IReadOnlyCollection $columns = null;

    /**
     * Gets the columns of the table.
     * 
     * @return array|IEnumerable The columns of the table.
     */
    protected static abstract function implColumns();

    public function __construct(array $columnValues = []) {
        
    }

    public static function columns(): IReadOnlyCollection {
        return self::$columns ??= new ReadOnlyCollection(self::implColumns(), TypeHintFactory::tryParse(Column::class));
    }
}