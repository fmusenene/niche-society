<?php
/**
 * Test RSS Feeds - Debug Script
 * Tests all RSS feed URLs to see which ones work
 */

require_once __DIR__ . '/config/config.php';

echo "<h1>RSS Feed Test</h1>";
echo "<style>
    body { font-family: Arial, sans-serif; padding: 20px; max-width: 1200px; margin: 0 auto; }
    .success { color: green; font-weight: bold; }
    .error { color: red; font-weight: bold; }
    .test { border: 1px solid #ddd; padding: 15px; margin: 15px 0; background: #f9f9f9; }
</style>";

$testFeeds = [
    'Al Jazeera' => 'https://www.aljazeera.com/xml/rss/all.xml',
    'Google News - Simple' => 'https://news.google.com/rss/search?q=property+management&hl=en-US&gl=US&ceid=US:en',
    'Google News - Saudi' => 'https://news.google.com/rss/search?q=property+Saudi+Arabia&hl=en-US&gl=SA&ceid=SA:en',
];

foreach ($testFeeds as $name => $url) {
    echo "<div class='test'>";
    echo "<h3>{$name}</h3>";
    echo "<p><strong>URL:</strong> <code>" . htmlspecialchars($url) . "</code></p>";
    
    // Test URL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_NOBODY, true); // HEAD request only
    
    curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);
    
    if ($httpCode === 200) {
        echo "<p class='success'>✓ HTTP {$httpCode} - Feed is accessible</p>";
    } elseif ($httpCode === 400) {
        echo "<p class='error'>✗ HTTP {$httpCode} - Bad Request (URL may be malformed or blocked)</p>";
    } elseif ($httpCode === 403) {
        echo "<p class='error'>✗ HTTP {$httpCode} - Forbidden (Access denied - likely anti-bot protection)</p>";
    } else {
        echo "<p class='error'>✗ HTTP {$httpCode} - {$curlError}</p>";
    }
    
    echo "</div>";
}

echo "<hr>";
echo "<h2>Recommendation</h2>";
echo "<p>If Google News feeds return 400/403 errors, consider:</p>";
echo "<ul>";
echo "<li>Using alternative RSS sources (Al Jazeera, Reuters, etc.)</li>";
echo "<li>Setting up an RSS proxy service</li>";
echo "<li>Using RSS Bridge or similar aggregator services</li>";
echo "<li>Focusing on direct RSS feeds from news websites rather than Google News</li>";
echo "</ul>";
?>
