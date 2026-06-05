<?php
/**
 * Legacy URL ? redirect to Site CMS admin (maintenance section).
 */
$query = isset($_SERVER['QUERY_STRING']) && $_SERVER['QUERY_STRING'] !== ''
    ? '?' . $_SERVER['QUERY_STRING']
    : '?section=maintenance';
if (strpos($query, 'section=') === false) {
    $query .= (strpos($query, '?') === false ? '?' : '&') . 'section=maintenance';
}

header('Location: index.php' . $query, true, 302);
exit;
