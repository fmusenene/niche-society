<?php
/**
 * Check Blog Images - Diagnostic Tool
 * Shows what images are stored in the database and if they're accessible
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/functions/helpers.php';

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Blog Images Diagnostic</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; max-width: 1200px; margin: 0 auto; }
        table { border-collapse: collapse; width: 100%; margin: 20px 0; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        th { background: #602234; color: white; }
        .image-preview { max-width: 150px; max-height: 100px; object-fit: cover; }
        .error { color: red; }
        .success { color: green; }
        .warning { color: orange; }
    </style>
</head>
<body>
    <h1>Blog Images Diagnostic</h1>
    
    <?php
    try {
        // Get all blog posts with their images
        $stmt = $pdo->query("
            SELECT id, slug, title_en, featured_image, published_at, created_at
            FROM blog_posts 
            WHERE status = 'published'
            ORDER BY published_at DESC
            LIMIT 20
        ");
        $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<p><strong>Total published posts: " . count($posts) . "</strong></p>";
        echo "<table>";
        echo "<tr><th>ID</th><th>Title</th><th>Featured Image URL</th><th>Image Status</th><th>Preview</th></tr>";
        
        foreach ($posts as $post) {
            $imageUrl = $post['featured_image'];
            $imageStatus = 'Unknown';
            $imageClass = 'warning';
            
            if (empty($imageUrl) || $imageUrl === 'NULL' || $imageUrl === null) {
                $imageStatus = 'No Image';
                $imageClass = 'error';
                $imageUrl = 'N/A';
            } elseif (preg_match('/^https?:\/\//', $imageUrl)) {
                // External URL - check if accessible
                $headers = @get_headers($imageUrl, 1);
                if ($headers && strpos($headers[0], '200') !== false) {
                    $imageStatus = 'External - Accessible';
                    $imageClass = 'success';
                } else {
                    $imageStatus = 'External - Not Accessible';
                    $imageClass = 'error';
                }
            } else {
                // Local path
                $localPath = __DIR__ . '/' . ltrim($imageUrl, '/');
                if (file_exists($localPath)) {
                    $imageStatus = 'Local - Exists';
                    $imageClass = 'success';
                    $imageUrl = url($imageUrl);
                } else {
                    $imageStatus = 'Local - Not Found';
                    $imageClass = 'error';
                }
            }
            
            echo "<tr>";
            echo "<td>{$post['id']}</td>";
            echo "<td>" . htmlspecialchars(substr($post['title_en'], 0, 50)) . "...</td>";
            echo "<td style='word-break: break-all; max-width: 400px;'>" . htmlspecialchars($imageUrl) . "</td>";
            echo "<td class='{$imageClass}'>{$imageStatus}</td>";
            echo "<td>";
            if ($imageStatus !== 'No Image' && $imageStatus !== 'Local - Not Found' && $imageStatus !== 'External - Not Accessible') {
                $displayUrl = (preg_match('/^https?:\/\//', $post['featured_image'])) 
                    ? $post['featured_image'] 
                    : url($post['featured_image']);
                echo "<img src='" . htmlspecialchars($displayUrl) . "' class='image-preview' onerror='this.style.display=\"none\"; this.nextElementSibling.style.display=\"block\";'><span style='display:none; color:red;'>Failed to load</span>";
            } else {
                echo "<span style='color: #999;'>N/A</span>";
            }
            echo "</td>";
            echo "</tr>";
        }
        
        echo "</table>";
        
        // Summary
        $withImages = 0;
        $withoutImages = 0;
        foreach ($posts as $post) {
            if (!empty($post['featured_image']) && $post['featured_image'] !== 'NULL') {
                $withImages++;
            } else {
                $withoutImages++;
            }
        }
        
        echo "<h2>Summary</h2>";
        echo "<p>Posts with images: <strong>{$withImages}</strong></p>";
        echo "<p>Posts without images: <strong>{$withoutImages}</strong></p>";
        
        // Check default image exists
        $defaultImage = __DIR__ . '/assets/images/niche-society-homepage-1-scaled.jpg';
        if (file_exists($defaultImage)) {
            echo "<p class='success'>✓ Default fallback image exists: assets/images/niche-society-homepage-1-scaled.jpg</p>";
        } else {
            echo "<p class='error'>✗ Default fallback image NOT found: assets/images/niche-society-homepage-1-scaled.jpg</p>";
        }
        
    } catch (Exception $e) {
        echo "<p class='error'>Error: " . $e->getMessage() . "</p>";
    }
    ?>
    
    <hr>
    <p><a href="blog.php">← Back to Blog</a></p>
</body>
</html>
