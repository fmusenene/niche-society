<?php
/**
 * Admin work documents — free-form text with proposal-style print
 */

function cmsEnsureWorkDocumentsTable(PDO $pdo): void
{
    $pdo->exec("CREATE TABLE IF NOT EXISTS work_documents (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL DEFAULT '',
        subject VARCHAR(255) NOT NULL DEFAULT '',
        body LONGTEXT NOT NULL,
        language VARCHAR(2) NOT NULL DEFAULT 'en',
        offer_date VARCHAR(50) NOT NULL DEFAULT '',
        prepared_by VARCHAR(255) NOT NULL DEFAULT '',
        tel VARCHAR(100) NOT NULL DEFAULT '',
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_updated (updated_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
}

/** @return array<int, array<string, mixed>> */
function cmsGetAllWorkDocuments(PDO $pdo): array
{
    cmsEnsureWorkDocumentsTable($pdo);
    $stmt = $pdo->query('SELECT id, title, subject, body, language, updated_at, created_at FROM work_documents ORDER BY updated_at DESC, id DESC');

    return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
}

function cmsGetWorkDocument(PDO $pdo, int $id): ?array
{
    cmsEnsureWorkDocumentsTable($pdo);
    $stmt = $pdo->prepare('SELECT * FROM work_documents WHERE id = ? LIMIT 1');
    $stmt->execute([$id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    return $row ?: null;
}

function cmsCreateWorkDocument(PDO $pdo): int
{
    cmsEnsureWorkDocumentsTable($pdo);
    $stmt = $pdo->prepare('INSERT INTO work_documents (title, subject, body, language, offer_date) VALUES (?, ?, ?, ?, ?)');
    $stmt->execute(['', '', '', 'en', '']);
    return (int) $pdo->lastInsertId();
}

function cmsWorkBodyPlainSnippet(string $body, int $maxLen = 80): string
{
    $text = trim(strip_tags(str_replace(['<br>', '<br/>', '<br />'], ' ', $body)));
    $text = preg_replace('/\s+/', ' ', $text) ?? '';
    if ($text === '') {
        return 'Untitled document';
    }
    if (function_exists('mb_strlen') && function_exists('mb_substr')) {
        return mb_strlen($text) > $maxLen ? mb_substr($text, 0, $maxLen - 1) . '…' : $text;
    }

    return strlen($text) > $maxLen ? substr($text, 0, $maxLen - 1) . '…' : $text;
}

function cmsSanitizeWorkBodyHtml(string $html): string
{
    $allowedTags = '<p><br><strong><b><em><i><u><s><strike><ul><ol><li><h1><h2><h3><h4><div><span><blockquote>';
    $clean = strip_tags($html, $allowedTags);
    $clean = preg_replace('/\s*on\w+\s*=\s*("|\').*?\1/i', '', $clean) ?? $clean;
    $clean = preg_replace('/javascript\s*:/i', '', $clean) ?? $clean;
    $clean = preg_replace_callback('/\sstyle="([^"]*)"/i', static function (array $matches): string {
        if (preg_match('/text-align\s*:\s*(left|center|right|justify)/i', $matches[1], $align)) {
            return ' style="text-align:' . strtolower($align[1]) . '"';
        }

        return '';
    }, $clean) ?? $clean;

    return trim($clean);
}

function cmsWorkBodyToPrintHtml(string $body): string
{
    $body = trim($body);
    if ($body === '') {
        return '';
    }
    if (preg_match('/<[a-z][\s\S]*>/i', $body)) {
        return cmsSanitizeWorkBodyHtml($body);
    }

    $html = '';
    foreach (preg_split('/\r\n|\r|\n/', $body) as $paragraph) {
        $paragraph = trim($paragraph);
        if ($paragraph === '') {
            $html .= '<p class="work-body-spacer">&nbsp;</p>';
            continue;
        }
        $html .= '<p>' . nl2br(htmlspecialchars($paragraph, ENT_QUOTES, 'UTF-8')) . '</p>';
    }

    return $html;
}

/** @param array<string, mixed> $data */
function cmsSaveWorkDocument(PDO $pdo, int $id, array $data): bool
{
    cmsEnsureWorkDocumentsTable($pdo);
    $existing = cmsGetWorkDocument($pdo, $id);
    if (!$existing) {
        return false;
    }

    $language = ($data['language'] ?? 'en') === 'ar' ? 'ar' : 'en';
    $body = cmsSanitizeWorkBodyHtml((string) ($data['body'] ?? ''));
    $stmt = $pdo->prepare('UPDATE work_documents SET
        body = ?,
        language = ?
        WHERE id = ?');
    $stmt->execute([
        $body,
        $language,
        $id,
    ]);

    return true;
}

function cmsDeleteWorkDocument(PDO $pdo, int $id): bool
{
    cmsEnsureWorkDocumentsTable($pdo);
    $stmt = $pdo->prepare('DELETE FROM work_documents WHERE id = ?');
    $stmt->execute([$id]);

    return $stmt->rowCount() > 0;
}
