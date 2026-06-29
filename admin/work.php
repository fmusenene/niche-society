<?php
/**
 * Printable work document
 */
require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/lib/work-print.php';

if (!adminIsAuthenticated()) {
    adminRedirect();
}

$workId = !empty($_GET['id']) ? (int) $_GET['id'] : 0;
$autoPrint = !empty($_GET['print']);

if ($workId <= 0) {
    adminFlash('danger', 'Document not found.');
    adminRedirect('section=work');
}

require_once dirname(__DIR__) . '/functions/work-documents.php';
if (!cmsGetWorkDocument($pdo, $workId)) {
    adminFlash('danger', 'Document not found.');
    adminRedirect('section=work');
}

header('Content-Type: text/html; charset=UTF-8');
echo adminRenderWorkPrint($pdo, $workId, $autoPrint);
