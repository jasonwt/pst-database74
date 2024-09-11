<?php

declare(strict_types=1);

namespace Pst\Database;

function generateSplitPattern($termsToSplitOn): string {
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

function splitAndCaptureDelim(string $subject, string ...$delim): array {
    if (count($delim) === 0) {
        throw new \InvalidArgumentException("No delimiters provided");
    }

    $pattern = generateSplitPattern($delim);
    if (($result = preg_split($pattern, $subject, -1, PREG_SPLIT_DELIM_CAPTURE)) === false) {
        throw new \InvalidArgumentException("Invalid subject value: '$subject'");
    }

    return array_map(fn($v) => trim($v), $result);
}