<?php
/**
 * English ↔ Arabic translation for CMS admin (Google Translate).
 * Uses Cloud Translation API when TRANSLATE_API_KEY is set; otherwise Google gtx.
 */

function cmsTranslateEnToAr(string $text): string
{
    return cmsTranslateBetween($text, 'en', 'ar');
}

function cmsTranslateArToEn(string $text): string
{
    return cmsTranslateBetween($text, 'ar', 'en');
}

function cmsTranslateBetween(string $text, string $fromLang, string $toLang): string
{
    $text = trim($text);
    if ($text === '' || $fromLang === $toLang) {
        return $text;
    }
    if (!in_array($fromLang, ['en', 'ar', 'auto'], true) || !in_array($toLang, ['en', 'ar'], true)) {
        throw new InvalidArgumentException('Unsupported language pair.');
    }

    $chunks = cmsTranslateSplitChunks($text, 420);
    $out = [];
    foreach ($chunks as $i => $chunk) {
        $translated = cmsTranslateChunk($chunk, $fromLang, $toLang);
        if ($translated === null) {
            throw new RuntimeException('Translation service unavailable. Try again in a moment.');
        }
        $out[] = $translated;
        if ($i < count($chunks) - 1) {
            usleep(250000);
        }
    }

    return cmsTranslateJoinChunks($text, $out);
}

/**
 * Split text for API limits while keeping line breaks where possible.
 */
function cmsTranslateSplitChunks(string $text, int $maxLen): array
{
    if (mb_strlen($text) <= $maxLen) {
        return [$text];
    }

    $chunks = [];
    $lines = preg_split('/\r\n|\r|\n/', $text);
    $buffer = '';

    foreach ($lines as $line) {
        $line = (string) $line;
        if (mb_strlen($line) > $maxLen) {
            if ($buffer !== '') {
                $chunks[] = $buffer;
                $buffer = '';
            }
            foreach (cmsTranslateSplitLongLine($line, $maxLen) as $part) {
                $chunks[] = $part;
            }
            continue;
        }

        $candidate = $buffer === '' ? $line : $buffer . "\n" . $line;
        if (mb_strlen($candidate) <= $maxLen) {
            $buffer = $candidate;
        } else {
            if ($buffer !== '') {
                $chunks[] = $buffer;
            }
            $buffer = $line;
        }
    }

    if ($buffer !== '') {
        $chunks[] = $buffer;
    }

    return $chunks ?: [$text];
}

function cmsTranslateSplitLongLine(string $line, int $maxLen): array
{
    $parts = [];
    $remaining = $line;
    while (mb_strlen($remaining) > $maxLen) {
        $slice = mb_substr($remaining, 0, $maxLen);
        $break = mb_strrpos($slice, ' ');
        if ($break === false || $break < (int) ($maxLen * 0.4)) {
            $break = $maxLen;
        }
        $parts[] = trim(mb_substr($remaining, 0, $break));
        $remaining = trim(mb_substr($remaining, $break));
    }
    if ($remaining !== '') {
        $parts[] = $remaining;
    }
    return $parts;
}

function cmsTranslateJoinChunks(string $original, array $translated): string
{
    if (count($translated) === 1) {
        return $translated[0];
    }
    if (str_contains($original, "\n")) {
        return implode("\n", $translated);
    }
    return implode(' ', $translated);
}

function cmsTranslateEnToArChunk(string $text): ?string
{
    return cmsTranslateChunk($text, 'en', 'ar');
}

function cmsTranslateChunk(string $text, string $fromLang, string $toLang): ?string
{
    if (cmsTranslateHasGoogleApiKey()) {
        $official = cmsGoogleTranslateOfficial($text, $fromLang, $toLang);
        if ($official !== null) {
            return $official;
        }
    }

    return cmsGoogleTranslateGtx($text, $fromLang, $toLang);
}

function cmsTranslateHasGoogleApiKey(): bool
{
    if (!defined('TRANSLATE_API_KEY')) {
        return false;
    }
    $key = trim((string) TRANSLATE_API_KEY);
    return $key !== '' && $key !== 'YOUR_GOOGLE_TRANSLATE_API_KEY_HERE';
}

function cmsGoogleTranslateOfficial(string $text, string $fromLang, string $toLang): ?string
{
    if (!cmsTranslateHasGoogleApiKey()) {
        return null;
    }

    $endpoint = 'https://translation.googleapis.com/language/translate/v2';
    $params = [
        'q' => $text,
        'target' => $toLang,
        'format' => 'text',
        'key' => TRANSLATE_API_KEY,
    ];
    if ($fromLang !== 'auto') {
        $params['source'] = $fromLang;
    }
    $payload = http_build_query($params, '', '&', PHP_QUERY_RFC3986);

    $sslVerify = !(defined('IS_LOCAL') && IS_LOCAL);
    $ch = curl_init($endpoint);
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $payload,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 25,
        CURLOPT_SSL_VERIFYPEER => $sslVerify,
        CURLOPT_SSL_VERIFYHOST => $sslVerify ? 2 : 0,
        CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded; charset=utf-8'],
    ]);
    $body = curl_exec($ch);
    $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if (PHP_VERSION_ID < 80500) {
        curl_close($ch);
    }

    if ($body === false || $code >= 400) {
        return null;
    }

    $data = json_decode($body, true);
    $translated = $data['data']['translations'][0]['translatedText'] ?? null;
    if (!is_string($translated) || trim($translated) === '') {
        return null;
    }

    return html_entity_decode($translated, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

function cmsGoogleTranslateGtx(string $text, string $fromLang, string $toLang): ?string
{
    $url = 'https://translate.googleapis.com/translate_a/single?' . http_build_query([
        'client' => 'gtx',
        'sl' => $fromLang,
        'tl' => $toLang,
        'dt' => 't',
        'q' => $text,
    ], '', '&', PHP_QUERY_RFC3986);

    $json = cmsTranslateHttpGet($url);
    if ($json === null) {
        return null;
    }

    $data = json_decode($json, true);
    if (!is_array($data) || !isset($data[0]) || !is_array($data[0])) {
        return null;
    }

    $parts = [];
    foreach ($data[0] as $segment) {
        if (is_array($segment) && isset($segment[0]) && is_string($segment[0])) {
            $parts[] = $segment[0];
        }
    }

    $translated = trim(implode('', $parts));
    return $translated !== '' ? $translated : null;
}

/**
 * Translate many short strings in parallel (preserves input order).
 *
 * @param list<string> $texts
 * @return list<string>
 */
function cmsTranslateMany(array $texts, string $fromLang, string $toLang): array
{
    if ($fromLang === $toLang) {
        return array_map(static fn ($t) => trim((string) $t), $texts);
    }

    $results = [];
    $parallel = [];

    foreach ($texts as $i => $text) {
        $text = trim((string) $text);
        if ($text === '') {
            $results[$i] = '';
            continue;
        }
        if (mb_strlen($text) > 420) {
            $results[$i] = cmsTranslateBetween($text, $fromLang, $toLang);
            continue;
        }
        $parallel[$i] = $text;
    }

    if ($parallel !== []) {
        $translated = cmsTranslateParallelChunks($parallel, $fromLang, $toLang);
        foreach ($translated as $i => $value) {
            $results[$i] = $value;
        }
    }

    $ordered = [];
    foreach ($texts as $i => $_) {
        $ordered[] = $results[$i] ?? '';
    }

    return $ordered;
}

/**
 * @param array<int, string> $indexedTexts
 * @return array<int, string>
 */
function cmsTranslateParallelChunks(array $indexedTexts, string $fromLang, string $toLang, int $batchSize = 8): array
{
    $results = [];
    $keys = array_keys($indexedTexts);

    foreach (array_chunk($keys, $batchSize) as $batchKeys) {
        foreach ($batchKeys as $key) {
            $chunk = cmsTranslateChunk($indexedTexts[$key], $fromLang, $toLang);
            if ($chunk === null) {
                throw new RuntimeException('Google Translate is unavailable. Try again in a moment.');
            }
            $results[$key] = $chunk;
            usleep(120000);
        }
    }

    return $results;
}

function cmsTranslateCreateCurlHandle(string $url)
{
    $sslVerify = !(defined('IS_LOCAL') && IS_LOCAL);
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 25,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTPHEADER => [
            'Accept: application/json,text/plain,*/*',
            'User-Agent: Mozilla/5.0 (compatible; NicheSocietyCMS/1.0)',
        ],
        CURLOPT_SSL_VERIFYPEER => $sslVerify,
        CURLOPT_SSL_VERIFYHOST => $sslVerify ? 2 : 0,
    ]);

    return $ch;
}

function cmsTranslateHttpGet(string $url): ?string
{
    $sslVerify = !(defined('IS_LOCAL') && IS_LOCAL);

    if (function_exists('curl_init')) {
        $ch = cmsTranslateCreateCurlHandle($url);
        $body = curl_exec($ch);
        $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if (PHP_VERSION_ID < 80500) {
            curl_close($ch);
        }
        if ($body !== false && $code < 400) {
            return $body;
        }
    }

    $ctx = stream_context_create([
        'http' => [
            'timeout' => 25,
            'header' => "Accept: application/json,text/plain,*/*\r\nUser-Agent: Mozilla/5.0 (compatible; NicheSocietyCMS/1.0)\r\n",
        ],
        'ssl' => [
            'verify_peer' => $sslVerify,
            'verify_peer_name' => $sslVerify,
        ],
    ]);
    $body = @file_get_contents($url, false, $ctx);
    return $body !== false ? $body : null;
}
