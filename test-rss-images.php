<?php
/**
 * Test RSS Images - Debug Script
 * Run this to see what images are being extracted from RSS feeds
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';

// Get latest RSS articles
$stmt = $pdo->query("
    SELECT id, slug, title_en, featured_image, tags, category 
    FROM blog_posts 
    WHERE tags LIKE 'source_url:%'
    ORDER BY created_at DESC 
    LIMIT 10
");
$articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<h1>RSS Feed Image Test</h1>";
echo "<style>
    body { font-family: Arial, sans-serif; padding: 20px; max-width: 1200px; margin: 0 auto; }
    .article { border: 1px solid #ddd; padding: 15px; margin: 15px 0; background: #f9f9f9; }
    .article img { max-width: 300px; height: auto; border: 2px solid #602234; }
    .image-ok { color: green; }
    .image-error { color: red; }
    .image-url { word-break: break-all; font-size: 12px; color: #666; }
</style>";

echo "<p>Found " . count($articles) . " RSS articles. Testing images...</p>";

foreach ($articles as $article) {
    echo "<div class='article'>";
    echo "<h3>" . htmlspecialchars($article['title_en']) . "</h3>";
    echo "<p><strong>Category:</strong> " . htmlspecialchars($article['category']) . "</p>";
    echo "<p class='image-url'><strong>Image URL:</strong> " . htmlspecialchars($article['featured_image']) . "</p>";
    
    // Check if URL is absolute or relative
    $isAbsolute = preg_match('/^https?:\/\//i', $article['featured_image']);
    echo "<p><strong>URL Type:</strong> " . ($isAbsolute ? '<span class="image-ok">Absolute URL</span>' : '<span class="image-error">Relative URL</span>') . "</p>";
    
    // Try to display image
    $imageUrl = $isAbsolute ? $article['featured_image'] : url($article['featured_image']);
    echo "<p><strong>Display URL:</strong> <span class='image-url'>" . htmlspecialchars($imageUrl) . "</span></p>";
    
    echo "<div style='margin: 10px 0;'>";
    echo "<img src='" . htmlspecialchars($imageUrl) . "' 
              alt='Test Image' 
              onerror=\"this.style.border='2px solid red'; this.alt='Image failed to load'; console.error('Image failed:', '" . htmlspecialchars($imageUrl) . "');\"
              onload=\"this.style.border='2px solid green'; console.log('Image loaded:', '" . htmlspecialchars($imageUrl) . "');\">";
    echo "</div>";
    
    // Check tags for source URL
    if (!empty($article['tags']) && preg_match('/source_url:([^\s]+)/', $article['tags'], $matches)) {
        echo "<p><strong>Source:</strong> <a href='" . htmlspecialchars($matches[1]) . "' target='_blank'>" . htmlspecialchars($matches[1]) . "</a></p>";
    }
    
    echo "</div>";
}

echo "<hr>";
echo "<h2>Summary</h2>";
echo "<p>This test shows:</p>";
echo "<ul>";
echo "<li>Which images are absolute URLs (should work)</li>";
echo "<li>Which images are relative URLs (may need fixing)</li>";
echo "<li>Whether images actually load in the browser</li>";
echo "<li>Check browser console (F12) for image loading errors</li>";
echo "</ul>";
?>
