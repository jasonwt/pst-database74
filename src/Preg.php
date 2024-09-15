<?php

declare(strict_types=1);

namespace Pst\Database;

// const SINGLE_QUOTED_STRING_PATTERN   = [{'match' => "(?:'(?:(?:[^'\\\\]|\\\\.)*)')"}];
// const BACKTICK_QUOTED_STRING_PATTERN = [{'replace' => "s/blah/blahrg/"}, {'match' => "(?:`(?:(?:[^`\\\\]|\\\\.)*)`)"}]

final class Preg {
    const SINGLE_QUOTED_STRING_PATTERN = "(?:'(?:(?:[^'\\\\]|\\\\.)*)')";
    const DOUBLE_QUOTED_STRING_PATTERN = '(?:"(?:(?:[^"\\\\]|\\\\.)*)")';
    const BACKTICK_QUOTED_STRING_PATTERN = "(?:`(?:(?:[^`\\\\]|\\\\.)*)`)";

    const NESTED_PARENTHESIS_NOT_INSIDE_QUOTES = '\((?:[^()\'"`]+|\'[^\']*\'|`[^`]*`|\((?:[^()\'"`]+|\'[^\']*\'|`[^`]*`)*\))*\)';

    const INTEGER_PATTERN = '(?:(?:\-)?[0-9]+)';
    const NUMERIC_PATTERN = '(?:(?:\-)?[0-9]+(?:\.[0-9]+)?)';

    const IDENTIFIER_PATTERN = '([a-zA-Z_]+[a-zA-Z0-9_]*|`[a-zA-Z_]+[a-zA-Z0-9_]*`)';
    const ALIAS_PATTERN = '(?:\s+as\s+([a-zA-Z0-9_]+|`[a-zA-Z0-9_]+`))';
    const SCHEMA_PATTERN = self::IDENTIFIER_PATTERN;
    const TABLE_IDENTIFIER_PATTERN = "(?:(?:" . self::IDENTIFIER_PATTERN . "\s*\.\s*)?" . self::IDENTIFIER_PATTERN . ")";
    const TABLE_IDENTIFIER_WITH_OPTIONAL_ALIAS_PATTERN = self::TABLE_IDENTIFIER_PATTERN . "(?:\s*" . self::ALIAS_PATTERN . ")?";
    const COLUMN_IDENTIFIER_PATTERN = "(?:" . self::TABLE_IDENTIFIER_PATTERN . "\s*\.\s*)?" . self::IDENTIFIER_PATTERN;
    const COLUMN_IDENTIFIER_WITH_OPTIONAL_ALIAS_PATTERN = self::COLUMN_IDENTIFIER_PATTERN . "(?:\s*" . self::ALIAS_PATTERN . ")?";

    const STRING_LITERAL_PATTERN = "(" . self::SINGLE_QUOTED_STRING_PATTERN . "|" . self::DOUBLE_QUOTED_STRING_PATTERN . ")";
    const STRING_LITERAL_WITH_OPTIONAL_ALIAS_PATTERN = self::STRING_LITERAL_PATTERN . "(?:\s*" . self::ALIAS_PATTERN . ")?";
    
    private function __construct() {}

    public static function generateSplitPattern($termsToSplitOn): string {
        if (is_string($termsToSplitOn)) {
            $termsToSplitOn = [$termsToSplitOn];
        } else if (!is_array($termsToSplitOn)) {
            throw new \InvalidArgumentException("Invalid terms to split on");
        }
    
        // sort evaluation seperators by length in descending order
        usort($termsToSplitOn, fn($a, $b) => strlen($b) - strlen($a));
    
        $termsToSplitOn = array_map(fn($term) => ctype_alpha($term) ? "\s+" . preg_quote($term) . "\s+" : preg_quote($term), $termsToSplitOn);
        
        return '/(' . implode("|", $termsToSplitOn) . ')(?=(?:[^\'"\\\\]*(?:\\\\.|\'[^\']*\'|"[^"]*"))*[^\'"\\\\]*$)/';
    }
    
    public static function splitAndCaptureDelim(string $subject, string ...$delim): array {
        if (count($delim) === 0) {
            throw new \InvalidArgumentException("No delimiters provided");
        }
    
        $pattern = self::generateSplitPattern($delim);
        if (($result = preg_split($pattern, $subject, -1, PREG_SPLIT_DELIM_CAPTURE)) === false) {
            throw new \InvalidArgumentException("Invalid subject value: '$subject'");
        }
    
        return array_map(fn($v) => trim($v), $result);
    }
}