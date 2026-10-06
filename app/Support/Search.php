<?php

namespace App\Support;

class Search
{
    // Escapes LIKE wildcards so a user's search text is matched literally,
    // not interpreted as a wildcard pattern. '\' must be escaped first,
    // or escaping % and _ afterward would double-escape it.
    public static function likeTerm(string $term): string
    {
        $escaped = str_replace(['\\', '%', '_'], ['\\\\', '\%', '\_'], $term);

        return '%' . $escaped . '%';
    }
}