<?php

declare(strict_types=1);

namespace Pst\Database;

// const SINGLE_QUOTED_STRING_PATTERN   = [{'match' => "(?:'(?:(?:[^'\\\\]|\\\\.)*)')"}];
// const BACKTICK_QUOTED_STRING_PATTERN = [{'replace' => "s/blah/blahrg/"}, {'match' => "(?:`(?:(?:[^`\\\\]|\\\\.)*)`)"}]

final class PregPatterns {
    const SINGLE_QUOTED_STRING_PATTERN = "(?:'(?:(?:[^'\\\\]|\\\\.)*)')";
    const DOUBLE_QUOTED_STRING_PATTERN = '(?:"(?:(?:[^"\\\\]|\\\\.)*)")';
    const BACKTICK_QUOTED_STRING_PATTERN = "(?:`(?:(?:[^`\\\\]|\\\\.)*)`)";

    const NESTED_PARENTHESIS_NOT_INSIDE_QUOTES = '\((?:[^()\'"`]+|\'[^\']*\'|`[^`]*`|\((?:[^()\'"`]+|\'[^\']*\'|`[^`]*`)*\))*\)';

    const INTEGER_PATTERN = '(?:(?:\-)?[0-9]+)';
    const NUMERIC_PATTERN = '(?:(?:\-)?[0-9]+(?:\.[0-9]+)?)';

    const IDENTIFIER_PATTERN = '([a-zA-Z_]+|`[a-zA-Z0-9_]+`)';
    const ALIAS_PATTERN = '(?:\s+as\s+([a-zA-Z0-9_]+|`[a-zA-Z0-9_]+`))';
    const SCHEMA_PATTERN = self::IDENTIFIER_PATTERN;
    const TABLE_IDENTIFIER_PATTERN = "(?:(?:" . self::IDENTIFIER_PATTERN . "\s*\.\s*)?" . self::IDENTIFIER_PATTERN . ")";
    const TABLE_IDENTIFIER_WITH_OPTIONAL_ALIAS_PATTERN = self::TABLE_IDENTIFIER_PATTERN . "(?:\s*" . self::ALIAS_PATTERN . ")?";
    const COLUMN_IDENTIFIER_PATTERN = "(?:" . self::TABLE_IDENTIFIER_PATTERN . "\s*\.\s*)?" . self::IDENTIFIER_PATTERN;
    const COLUMN_IDENTIFIER_WITH_OPTIONAL_ALIAS_PATTERN = self::COLUMN_IDENTIFIER_PATTERN . "(?:\s*" . self::ALIAS_PATTERN . ")?";

    const STRING_LITERAL_PATTERN = "(" . self::SINGLE_QUOTED_STRING_PATTERN . "|" . self::DOUBLE_QUOTED_STRING_PATTERN . ")";
    
    private function __construct() {}
}