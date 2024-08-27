<?php

declare(strict_types=1);

namespace Sdn\RectorCustomRules\Helpers;

/**
 * @package:    Sdn\RectorCustomRules\Helpers
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2024, SoftDev Nord
 * @Class:      StringHelper
 */
final class StringHelper
{
    public static function stringToCamelCase(string $string): string
    {
        // Konvertiere den gesamten String in Kleinbuchstaben
        $string = strtolower($string);
        // Ersetze '-' und '_' durch Leerzeichen
        $words = explode(' ', str_replace(['-', '_'], ' ', $string));

        // Mache den ersten Buchstaben jedes Wortes groß
        $studlyWords = array_map(static fn($word) => ucfirst($word), $words);

        // Verbinde die Wörter und mache den ersten Buchstaben klein (camelCase)
        return lcfirst(implode($studlyWords));
    }
}
