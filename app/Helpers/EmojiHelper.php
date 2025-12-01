<?php

/**
 * Convert country code to emoji flag
 * @param string $countryCode ISO 3166-1 alpha-2 country code
 * @return string Unicode emoji flag
 */
function countryCodeToEmoji(string $countryCode): string
{
    $countryCode = strtoupper($countryCode);

    if (strlen($countryCode) !== 2) {
        return '';
    }

    // Convert country code to regional indicator symbols
    $firstChar = mb_chr(0x1F1E6 + ord($countryCode[0]) - ord('A'));
    $secondChar = mb_chr(0x1F1E6 + ord($countryCode[1]) - ord('A'));

    return $firstChar . $secondChar;
}

/**
 * Strip emoji from text
 * @param string $text
 * @return string
 */
function stripEmoji(string $text): string
{
    return preg_replace('/[\x{1F600}-\x{1F64F}]/u', '', $text) // Emoticons
        ?? preg_replace('/[\x{1F300}-\x{1F5FF}]/u', '', $text) // Misc Symbols and Pictographs
        ?? preg_replace('/[\x{1F680}-\x{1F6FF}]/u', '', $text) // Transport and Map
        ?? preg_replace('/[\x{2600}-\x{26FF}]/u', '', $text) // Misc symbols
        ?? preg_replace('/[\x{2700}-\x{27BF}]/u', '', $text) // Dingbats
        ?? preg_replace('/[\x{1F900}-\x{1F9FF}]/u', '', $text) // Supplemental Symbols and Pictographs
        ?? preg_replace('/[\x{1F1E6}-\x{1F1FF}]/u', '', $text); // Flags
}

/**
 * Check if string contains emoji
 * @param string $text
 * @return bool
 */
function containsEmoji(string $text): bool
{
    return preg_match('/[\x{1F600}-\x{1F64F}]|[\x{1F300}-\x{1F5FF}]|[\x{1F680}-\x{1F6FF}]|[\x{2600}-\x{26FF}]|[\x{2700}-\x{27BF}]|[\x{1F900}-\x{1F9FF}]|[\x{1F1E6}-\x{1F1FF}]/u', $text) === 1;
}

/**
 * Convert emoji to HTML entity
 * @param string $emoji
 * @return string
 */
function emojiToHtmlEntity(string $emoji): string
{
    $result = '';
    $length = mb_strlen($emoji);

    for ($i = 0; $i < $length; $i++) {
        $char = mb_substr($emoji, $i, 1);
        $code = mb_ord($char);
        $result .= '&#' . $code . ';';
    }

    return $result;
}
