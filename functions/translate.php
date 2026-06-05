<?php
/**
 * English → Arabic translation for CMS admin (MyMemory free API, chunked).
 */

function cmsTranslateEnToAr(string $text): string
{
    $text = trim($text);
    if ($text === '') {
        return '';
    }

    $chunks = cmsTranslateSplitChunks($text, 420);
    $out = [];
    foreach ($chunks as $i => $chunk) {
        $translated = cmsTranslateEnToArChunk($chunk);
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
    $url = 'https://api.mymemory.translated.net/get?' . http_build_query([
        'q' => $text,
        'langpair' => 'en|ar',
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

function cmsTranslateHttpGet(string $url): ?string
{
    $sslVerify = !(defined('IS_LOCAL') && IS_LOCAL);

    if (function_exists('curl_init')) {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 25,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTPHEADER => ['Accept: application/json'],
            CURLOPT_SSL_VERIFYPEER => $sslVerify,
            CURLOPT_SSL_VERIFYHOST => $sslVerify ? 2 : 0,
        ]);
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
