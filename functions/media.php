<?php
/**
 * Media upload helpers for CMS
 */

function cmsServiceUploadDir(): string
{
    return ROOT_PATH . '/assets/images/services';
}

function cmsServiceUploadUrlPrefix(): string
{
    return 'assets/images/services';
}

function cmsUploadServiceImage(array $file, string $slugHint = ''): array
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        $messages = [
            UPLOAD_ERR_INI_SIZE => 'File exceeds server upload limit.',
            UPLOAD_ERR_FORM_SIZE => 'File is too large.',
            UPLOAD_ERR_PARTIAL => 'Upload was incomplete. Try again.',
            UPLOAD_ERR_NO_FILE => 'No file was uploaded.',
        ];
        $code = (int) ($file['error'] ?? UPLOAD_ERR_NO_FILE);
        throw new InvalidArgumentException($messages[$code] ?? 'Upload failed.');
    }

    $maxBytes = 5 * 1024 * 1024;
    if (($file['size'] ?? 0) > $maxBytes) {
        throw new InvalidArgumentException('Image must be 5 MB or smaller.');
    }

    $tmp = $file['tmp_name'] ?? '';
    if ($tmp === '' || !is_uploaded_file($tmp)) {
        throw new InvalidArgumentException('Invalid upload.');
    }

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = $finfo ? finfo_file($finfo, $tmp) : null;
    if ($finfo) {
        finfo_close($finfo);
    }

    $allowed = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
        'image/gif' => 'gif',
    ];

    if (!isset($allowed[$mime])) {
        throw new InvalidArgumentException('Only JPG, PNG, WebP, and GIF images are allowed.');
    }

    $ext = $allowed[$mime];
    $dir = cmsServiceUploadDir();
    if (!is_dir($dir) && !mkdir($dir, 0755, true) && !is_dir($dir)) {
        throw new RuntimeException('Could not create upload folder.');
    }

    $slugHint = cmsNormalizeServiceCategory($slugHint);
    if ($slugHint === '' || $slugHint === 'general') {
        $slugHint = 'service';
    }
    $base = $slugHint . '-' . date('Ymd-His') . '-' . bin2hex(random_bytes(3));
    $filename = $base . '.' . $ext;
    $dest = $dir . '/' . $filename;

    if (!move_uploaded_file($tmp, $dest)) {
        throw new RuntimeException('Could not save uploaded image.');
    }

    $relative = cmsServiceUploadUrlPrefix() . '/' . $filename;

    return [
        'path' => $relative,
        'filename' => $filename,
        'url' => rtrim(SITE_URL, '/') . '/' . $relative,
    ];
}
