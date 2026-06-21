<?php
/**
 * English → Arabic translation for CMS admin (MyMemory free API, chunked).
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
    if (!in_array($fromLang, ['en', 'ar'], true) || !in_array($toLang, ['en', 'ar'], true)) {
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
    $url = 'https://api.mymemory.translated.net/get?' . http_build_query([
        'q' => $text,
        'langpair' => $fromLang . '|' . $toLang,
    ]);

    $json = cmsTranslateHttpGet($url);
    if ($json === null) {
        return null;
    }

    $data = json_decode($json, true);
    if (!is_array($data)) {
        return null;
    }

    if (!empty($data['responseStatus']) && (int) $data['responseStatus'] !== 200) {
        $detail = $data['responseDetails'] ?? 'Translation failed';
        throw new RuntimeException(is_string($detail) ? $detail : 'Translation failed');
    }

    $translated = $data['responseData']['translatedText'] ?? null;
    if (!is_string($translated) || $translated === '') {
        return null;
    }

    // MyMemory sometimes returns the source when quota exceeded
    if (stripos($translated, 'MYMEMORY WARNING') !== false) {
        throw new RuntimeException('Daily translation limit reached. Wait or translate Arabic manually.');
    }

    return $translated;
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
        if (!function_exists('curl_multi_init')) {
            foreach ($batchKeys as $key) {
                $chunk = cmsTranslateChunk($indexedTexts[$key], $fromLang, $toLang);
                if ($chunk === null) {
                    throw new RuntimeException('Translation service unavailable. Try again in a moment.');
                }
                $results[$key] = $chunk;
            }
            continue;
        }

        $mh = curl_multi_init();
        $handles = [];

        foreach ($batchKeys as $key) {
            $url = 'https://api.mymemory.translated.net/get?' . http_build_query([
                'q' => $indexedTexts[$key],
                'langpair' => $fromLang . '|' . $toLang,
            ]);
            $ch = cmsTranslateCreateCurlHandle($url);
            $handles[$key] = $ch;
            curl_multi_add_handle($mh, $ch);
        }

        $running = null;
        do {
            $status = curl_multi_exec($mh, $running);
            if ($running > 0) {
                curl_multi_select($mh, 1.0);
            }
        } while ($running > 0 && $status === CURLM_OK);

        foreach ($handles as $key => $ch) {
            $body = curl_multi_getcontent($ch);
            $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_multi_remove_handle($mh, $ch);
            if (PHP_VERSION_ID < 80500) {
                curl_close($ch);
            }

            if ($body === false || $code >= 400) {
                curl_multi_close($mh);
                throw new RuntimeException('Translation service unavailable. Try again in a moment.');
            }

            $data = json_decode($body, true);
            if (!is_array($data)) {
                curl_multi_close($mh);
                throw new RuntimeException('Translation service unavailable. Try again in a moment.');
            }

            if (!empty($data['responseStatus']) && (int) $data['responseStatus'] !== 200) {
                $detail = $data['responseDetails'] ?? 'Translation failed';
                curl_multi_close($mh);
                throw new RuntimeException(is_string($detail) ? $detail : 'Translation failed');
            }

            $translated = $data['responseData']['translatedText'] ?? null;
            if (!is_string($translated) || $translated === '') {
                curl_multi_close($mh);
                throw new RuntimeException('Translation service unavailable. Try again in a moment.');
            }

            if (stripos($translated, 'MYMEMORY WARNING') !== false) {
                curl_multi_close($mh);
                throw new RuntimeException('Daily translation limit reached. Wait or translate Arabic manually.');
            }

            $results[$key] = $translated;
        }

        if (PHP_VERSION_ID < 80500) {
            curl_multi_close($mh);
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
        CURLOPT_TIMEOUT => 20,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTPHEADER => ['Accept: application/json'],
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
        'http' => ['timeout' => 25, 'header' => "Accept: application/json\r\n"],
        'ssl' => [
            'verify_peer' => $sslVerify,
            'verify_peer_name' => $sslVerify,
        ],
    ]);
    $body = @file_get_contents($url, false, $ctx);
    return $body !== false ? $body : null;
}
