<?php
/**
 * Legacy URL — redirects to CMS service page
 */
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/functions/helpers.php';
header('Location: ' . url('service.php?slug=staff-recruitment'), true, 301);
exit;
