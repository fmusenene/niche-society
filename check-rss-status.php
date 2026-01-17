<?php
/**
 * Check RSS Aggregator Status
 * Shows when RSS aggregator last ran and how many articles were added
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';

echo "<h1>RSS Aggregator Status</h1>";
echo "<style>
    body { font-family: Arial, sans-serif; padding: 20px; max-width: 1200px; margin: 0 auto; }
    .info { background: #f0f0f0; padding: 15px; margin: 10px 0; border-left: 4px solid #602234; }
    .success { color: green; font-weight: bold; }
    .warning { color: orange; font-weight: bold; }
    .error { color: red; font-weight: bold; }
    table { border-collapse: collapse; width: 100%; margin: 20px 0; }
    th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
    th { background: #602234; color: white; }
</style>";

try {
    // Check log file for last run
    $logFile = __DIR__ . '/logs/rss-aggregator.log';
    $lastLogEntry = '';
    $lastRunTime = null;
    
    if (file_exists($logFile)) {
        $lines = file($logFile);
        if (!empty($lines)) {
            // Get last 10 lines
            $lastLines = array_slice($lines, -10);
            $lastLogEntry = implode('<br>', array_map('htmlspecialchars', $lastLines));
            
            // Try to extract timestamp from last "Completed" entry
            foreach (array_reverse($lines) as $line) {
                if (preg_match('/\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\] \[INFO\] === RSS Feed Aggregator Completed ===/', $line, $matches)) {
                    $lastRunTime = strtotime($matches[1]);
                    break;
                }
            }
        }
    }
    
    // Get article counts
    $totalStmt = $pdo->query("SELECT COUNT(*) as total FROM blog_posts WHERE tags LIKE 'source_url:%'");
    $totalArticles = $totalStmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    $todayStmt = $pdo->query("SELECT COUNT(*) as total FROM blog_posts WHERE tags LIKE 'source_url:%' AND DATE(created_at) = CURDATE()");
    $todayArticles = $todayStmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    $last24HoursStmt = $pdo->query("SELECT COUNT(*) as total FROM blog_posts WHERE tags LIKE 'source_url:%' AND created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)");
    $last24HoursArticles = $last24HoursStmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Get latest articles
    $latestStmt = $pdo->query("SELECT id, title_en, created_at FROM blog_posts WHERE tags LIKE 'source_url:%' ORDER BY created_at DESC LIMIT 5");
    $latestArticles = $latestStmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Calculate time since last run
    $timeSinceLastRun = null;
    if ($lastRunTime) {
        $timeSinceLastRun = time() - $lastRunTime;
        $hoursAgo = floor($timeSinceLastRun / 3600);
        $minutesAgo = floor(($timeSinceLastRun % 3600) / 60);
    }
    
    echo "<div class='info'>";
    echo "<h2>Current Status</h2>";
    echo "<p><strong>Total RSS Articles:</strong> {$totalArticles}</p>";
    echo "<p><strong>Articles Today:</strong> {$todayArticles}</p>";
    echo "<p><strong>Articles (Last 24 Hours):</strong> {$last24HoursArticles}</p>";
    
    if ($lastRunTime) {
        echo "<p><strong>Last Run:</strong> " . date('Y-m-d H:i:s', $lastRunTime) . " (" . ($hoursAgo > 0 ? "{$hoursAgo} hours, " : "") . "{$minutesAgo} minutes ago)</p>";
        if ($timeSinceLastRun > 7200) { // More than 2 hours
            echo "<p class='warning'>⚠️ RSS Aggregator hasn't run in over 2 hours. It should run every hour.</p>";
        } else {
            echo "<p class='success'>✓ RSS Aggregator is running regularly</p>";
        }
    } else {
        echo "<p class='error'>❌ No log entries found. RSS Aggregator may not have run yet.</p>";
    }
    echo "</div>";
    
    echo "<h2>Latest Articles</h2>";
    if (!empty($latestArticles)) {
        echo "<table>";
        echo "<tr><th>ID</th><th>Title</th><th>Created At</th></tr>";
        foreach ($latestArticles as $article) {
            echo "<tr>";
            echo "<td>{$article['id']}</td>";
            echo "<td>" . htmlspecialchars($article['title_en']) . "</td>";
            echo "<td>" . $article['created_at'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No RSS articles found in database.</p>";
    }
    
    echo "<h2>Last Log Entries</h2>";
    if ($lastLogEntry) {
        echo "<div style='background: #f9f9f9; padding: 15px; border: 1px solid #ddd; font-family: monospace; font-size: 12px; max-height: 300px; overflow-y: auto;'>";
        echo $lastLogEntry;
        echo "</div>";
    } else {
        echo "<p>No log entries found. Log file: " . htmlspecialchars($logFile) . "</p>";
    }
    
    echo "<hr>";
    echo "<h2>How to Update News</h2>";
    echo "<div class='info'>";
    echo "<h3>Option 1: Run Manually</h3>";
    echo "<p>Click this link to run the RSS aggregator now:</p>";
    echo "<p><a href='rss-feed-aggregator.php' target='_blank' style='display: inline-block; padding: 10px 20px; background: #602234; color: white; text-decoration: none;'>Run RSS Aggregator Now →</a></p>";
    
    echo "<h3>Option 2: Automatic Updates (Task Scheduler)</h3>";
    echo "<p>For automatic updates every hour, you need to set up Task Scheduler:</p>";
    echo "<ol>";
    echo "<li>Open <strong>Task Scheduler</strong> in Windows</li>";
    echo "<li>Create a new task named <strong>Niche Society RSS Aggregator</strong></li>";
    echo "<li>Set trigger: <strong>Daily</strong>, repeat <strong>every 1 hour</strong></li>";
    echo "<li>Action: Run program <code>C:\\xampp\\htdocs\\niche-society-main\\run-rss-aggregator.bat</code></li>";
    echo "</ol>";
    
    echo "<h3>Option 3: When Website is Hosted</h3>";
    echo "<p>On a hosted server, set up a cron job:</p>";
    echo "<code>0 * * * * cd /path/to/niche-society-main && php rss-feed-aggregator.php</code>";
    echo "<p>This runs every hour automatically.</p>";
    echo "</div>";
    
    echo "<hr>";
    echo "<p><a href='blog.php' style='display: inline-block; padding: 10px 20px; background: #602234; color: white; text-decoration: none;'>View Blog →</a></p>";
    
} catch (Exception $e) {
    echo "<p class='error'>Error: " . $e->getMessage() . "</p>";
}
?>
