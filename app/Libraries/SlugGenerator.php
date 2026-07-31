<?php

namespace App\Libraries;

class SlugGenerator
{
    public static function generate(string $value): string
    {
        $normalized = trim(mb_strtolower($value, 'UTF-8'));
        $normalized = strtr($normalized, [
            'á'=>'a','à'=>'a','â'=>'a','ã'=>'a','ä'=>'a',
            'é'=>'e','è'=>'e','ê'=>'e','ë'=>'e',
            'í'=>'i','ì'=>'i','î'=>'i','ï'=>'i',
            'ó'=>'o','ò'=>'o','ô'=>'o','õ'=>'o','ö'=>'o',
            'ú'=>'u','ù'=>'u','û'=>'u','ü'=>'u',
            'ç'=>'c','ñ'=>'n',
        ]);
        $normalized = preg_replace('/[^a-z0-9]+/', '-', $normalized) ?? $normalized;
        return trim($normalized, '-') ?: 'item';
    }

    /** Generate a slug that does not already exist according to the $exists callable. */
    public static function unique(string $base, callable $exists): string
    {
        $slug    = $base;
        $counter = 2;
        while ($exists($slug)) {
            $slug = $base . '-' . $counter++;
        }
        return $slug;
    }
}
