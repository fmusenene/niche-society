<?php
/**
 * One-time script to translate ALL published blog articles into Arabic.
 *
 * - Uses TRANSLATE_API_KEY in config/config.php (Google Cloud Translate API)
 * - Updates title_ar, excerpt_ar, content_ar for every published post that needs it
 * - Safe to run multiple times; skips rows that already have proper Arabic
 *
 * HOW TO RUN (browser):
 *   1) In config/config.php set: define('TRANSLATE_API_KEY', 'your-google-api-key');
 *   2) Visit: https://yourdomain.com/backfill-blog-arabic.php?run=1
 *
 * HOW TO RUN (CLI):
 *   php backfill-blog-arabic.php
 *
 * After running, switch the site to Arabic to see titles/excerpts/content in Arabic.
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';

// Require API key so we don't overwrite with English
if (!defined('TRANSLATE_API_KEY') || trim((string)TRANSLATE_API_KEY) === '' || trim((string)TRANSLATE_API_KEY) === 'YOUR_GOOGLE_TRANSLATE_API_KEY_HERE') {
    if (php_sapi_name() === 'cli') {
        echo "ERROR: Set TRANSLATE_API_KEY in config/config.php first, then run this script again.\n";
    } else {
        echo "<p><strong>ERROR:</strong> Set TRANSLATE_API_KEY in config/config.php first, then run this script again.</p>";
    }
    exit(1);
}

// Simple gate: require CLI or ?run=1
if (php_sapi_name() !== 'cli') {
    if (!isset($_GET['run']) || $_GET['run'] !== '1') {
        echo "Backfill Arabic translations for RSS blog posts.\n";
        echo "To run, open: backfill-blog-arabic.php?run=1\n";
        exit;
    }
}

// ---------------------------------------------------------------------
// Translation helper (same behaviour as in rss-feed-aggregator.php)
// ---------------------------------------------------------------------

function backfillTranslateContent($text, $targetLang) {
    $text = trim((string)$text);
    $targetLang = trim((string)$targetLang);

    if ($text === '' || $targetLang === '') {
        return $text;
    }

    if (!defined('TRANSLATE_API_KEY') || !TRANSLATE_API_KEY) {
        // No key configured → keep original
        return $text;
    }

    if (mb_strlen($text, 'UTF-8') < 3) {
        return $text;
    }

    try {
        $apiKey = TRANSLATE_API_KEY;
        $endpoint = 'https://translation.googleapis.com/language/translate/v2';

        $payload = http_build_query([
            'q'      => $text,
            'target' => $targetLang,
            'format' => 'text',
            'key'    => $apiKey,
        ], '', '&', PHP_QUERY_RFC3986);

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => $endpoint,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $payload,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT        => 20,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/x-www-form-urlencoded; charset=utf-8',
            ],
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($response === false || $httpCode !== 200) {
            error_log("Backfill translation API error ({$httpCode}): {$curlError}");
            return $text;
        }

        $data = json_decode($response, true);
        if (!isset($data['data']['translations'][0]['translatedText'])) {
            error_log("Backfill translation API missing translatedText; falling back to original");
            return $text;
        }

        $translated = trim((string)$data['data']['translations'][0]['translatedText']);
        return $translated !== '' ? $translated : $text;
    } catch (Exception $e) {
        error_log("Backfill translation exception: " . $e->getMessage());
        return $text;
    }
}

// ---------------------------------------------------------------------
// Backfill logic
// ---------------------------------------------------------------------

/** @var PDO $pdo */
global $pdo;

$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Select ALL published posts that need Arabic (not only RSS; include every post)
$sql = "
    SELECT id, title_en, excerpt_en, content_en, title_ar, excerpt_ar, content_ar
    FROM blog_posts
    WHERE status = 'published'
";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

$updated = 0;
$checked = 0;

foreach ($rows as $row) {
    $checked++;

    $id         = (int)$row['id'];
    $titleEn    = (string)$row['title_en'];
    $excerptEn  = (string)$row['excerpt_en'];
    $contentEn  = (string)$row['content_en'];
    $titleAr    = (string)$row['title_ar'];
    $excerptAr  = (string)$row['excerpt_ar'];
    $contentAr  = (string)$row['content_ar'];

    // Skip if Arabic fields already look different from English (assume already translated/edited)
    $needsTitle   = ($titleAr === ''   || $titleAr === $titleEn);
    $needsExcerpt = ($excerptAr === '' || $excerptAr === $excerptEn);
    $needsContent = ($contentAr === '' || $contentAr === $contentEn);

    if (!$needsTitle && !$needsExcerpt && !$needsContent) {
        continue;
    }

    $newTitleAr   = $needsTitle   ? backfillTranslateContent($titleEn, 'ar')   : $titleAr;
    $newExcerptAr = $needsExcerpt ? backfillTranslateContent($excerptEn, 'ar') : $excerptAr;
    $newContentAr = $needsContent ? backfillTranslateContent($contentEn, 'ar') : $contentAr;

    // Don't overwrite with English if API failed (returned original text)
    if ($needsTitle   && trim($newTitleAr) === trim($titleEn)) $newTitleAr   = $titleAr;
    if ($needsExcerpt && trim($newExcerptAr) === trim($excerptEn)) $newExcerptAr = $excerptAr;
    if ($needsContent && trim($newContentAr) === trim($contentEn)) $newContentAr = $contentAr;

    $updateStmt = $pdo->prepare("
        UPDATE blog_posts
        SET title_ar = ?, excerpt_ar = ?, content_ar = ?, updated_at = NOW()
        WHERE id = ?
    ");
    $updateStmt->execute([$newTitleAr, $newExcerptAr, $newContentAr, $id]);
    $updated++;
}

if (php_sapi_name() === 'cli') {
    echo "Checked {$checked} RSS posts, updated {$updated} with Arabic translations.\n";
} else {
    echo "<p>Checked <strong>{$checked}</strong> RSS posts, updated <strong>{$updated}</strong> with Arabic translations.</p>";
    echo "<p>You can now delete <code>backfill-blog-arabic.php</code> from the server if everything looks good.</p>";
}

