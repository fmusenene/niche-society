<?php
/**
 * Automatic RSS aggregator scheduling (background, non-blocking).
 */

function rssSchedulerPaths(): array
{
    $logs = defined('LOGS_PATH') ? LOGS_PATH : (ROOT_PATH . '/logs');
    return [
        'logs' => $logs,
        'lock' => $logs . '/rss-aggregator.lock',
        'last_run' => $logs . '/rss-last-run.txt',
        'script' => ROOT_PATH . '/rss-feed-aggregator.php',
    ];
}

function rssEnsureLogsDir(string $logsDir): void
{
    if (!is_dir($logsDir)) {
        @mkdir($logsDir, 0755, true);
    }
}

function rssGetLastRunTime(): int
{
    $paths = rssSchedulerPaths();
    if (!is_file($paths['last_run'])) {
        return 0;
    }
    $raw = trim((string) @file_get_contents($paths['last_run']));
    return ctype_digit($raw) ? (int) $raw : 0;
}

function rssIsAggregatorRunning(): bool
{
    $paths = rssSchedulerPaths();
    if (!is_file($paths['lock'])) {
        return false;
    }
    $age = time() - (int) filemtime($paths['lock']);
    // Stale lock after 45 minutes (crashed run)
    if ($age > 2700) {
        @unlink($paths['lock']);
        return false;
    }
    return true;
}

function rssShouldSkipAutoUpdate(): bool
{
    if (php_sapi_name() === 'cli') {
        return true;
    }
    if (defined('RSS_AUTO_UPDATE') && !RSS_AUTO_UPDATE) {
        return true;
    }

    $uri = $_SERVER['REQUEST_URI'] ?? '';
    $script = basename($_SERVER['SCRIPT_NAME'] ?? '');

    $skipScripts = [
        'rss-feed-aggregator.php',
        'check-rss-status.php',
        'test-rss-feeds.php',
        'backfill-blog-arabic.php',
    ];
    if (in_array($script, $skipScripts, true)) {
        return true;
    }

    if (stripos($uri, '/admin') !== false) {
        return true;
    }

    return false;
}

function rssFindPhpBinary(): string
{
    if (defined('PHP_BINARY') && PHP_BINARY && is_executable(PHP_BINARY)) {
        return PHP_BINARY;
    }
    if (defined('PHP_BINDIR')) {
        $candidate = PHP_BINDIR . DIRECTORY_SEPARATOR . 'php' . (PHP_OS_FAMILY === 'Windows' ? '.exe' : '');
        if (is_executable($candidate)) {
            return $candidate;
        }
    }
    return 'php';
}

/**
 * Start rss-feed-aggregator.php in the background (does not block the current request).
 */
function rssTriggerBackgroundAggregator(): bool
{
    $paths = rssSchedulerPaths();
    rssEnsureLogsDir($paths['logs']);

    if (!is_file($paths['script'])) {
        return false;
    }

    if (rssIsAggregatorRunning()) {
        return false;
    }

    $php = rssFindPhpBinary();
    $script = $paths['script'];
    $logOut = $paths['logs'] . '/rss-background.log';

    if (PHP_OS_FAMILY === 'Windows') {
        $cmd = 'start /B "" '
            . escapeshellarg($php) . ' '
            . escapeshellarg($script)
            . ' >> ' . escapeshellarg($logOut) . ' 2>&1';
        $handle = @popen($cmd, 'r');
        if ($handle === false) {
            return false;
        }
        pclose($handle);
        return true;
    }

    $cmd = escapeshellarg($php) . ' ' . escapeshellarg($script)
        . ' >> ' . escapeshellarg($logOut) . ' 2>&1 &';
    exec($cmd);
    return true;
}

/**
 * Try to claim spawn slot (avoids duplicate background processes on concurrent requests).
 */
function rssTryClaimSpawnSlot(): bool
{
    $paths = rssSchedulerPaths();
    rssEnsureLogsDir($paths['logs']);
    $claimFile = $paths['logs'] . '/rss-spawn-claim.lock';

    $fp = @fopen($claimFile, 'c+');
    if ($fp === false) {
        return true;
    }
    if (!flock($fp, LOCK_EX | LOCK_NB)) {
        fclose($fp);
        return false;
    }
    fwrite($fp, (string) time());
    fflush($fp);
    flock($fp, LOCK_UN);
    fclose($fp);
    return true;
}

/**
 * Run aggregator in background when interval has passed (call once per HTTP request).
 */
function rssMaybeRunInBackground(): void
{
    if (rssShouldSkipAutoUpdate()) {
        return;
    }

    $interval = defined('RSS_AUTO_UPDATE_INTERVAL') ? (int) RSS_AUTO_UPDATE_INTERVAL : 3600;
    if ($interval < 300) {
        $interval = 300;
    }

    if (rssIsAggregatorRunning()) {
        return;
    }

    $last = rssGetLastRunTime();
    if ($last > 0 && (time() - $last) < $interval) {
        return;
    }

    if (!rssTryClaimSpawnSlot()) {
        return;
    }

    rssTriggerBackgroundAggregator();
}

function rssMarkAggregatorFinished(): void
{
    $paths = rssSchedulerPaths();
    rssEnsureLogsDir($paths['logs']);
    @file_put_contents($paths['last_run'], (string) time());
    @unlink($paths['lock']);
}

function rssMarkAggregatorStarted(): bool
{
    $paths = rssSchedulerPaths();
    rssEnsureLogsDir($paths['logs']);

    if (rssIsAggregatorRunning()) {
        return false;
    }

    @file_put_contents($paths['lock'], (string) time());
    return true;
}
