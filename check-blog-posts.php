<?php
/**
 * Check Blog Posts in Database
 * Diagnostic script to see what's in the blog_posts table
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';

echo "<h1>Blog Posts Database Check</h1>";

try {
    // Check total posts
    $countStmt = $pdo->query("SELECT COUNT(*) as total FROM blog_posts");
    $total = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
    echo "<p><strong>Total posts in database:</strong> {$total}</p>";
    
    // Check posts by status
    $statusStmt = $pdo->query("SELECT status, COUNT(*) as count FROM blog_posts GROUP BY status");
    $statuses = $statusStmt->fetchAll(PDO::FETCH_ASSOC);
    echo "<h2>Posts by Status:</h2><ul>";
    foreach ($statuses as $status) {
        echo "<li>{$status['status']}: {$status['count']}</li>";
    }
    echo "</ul>";
    
    // Check published posts
    $publishedStmt = $pdo->query("SELECT COUNT(*) as total FROM blog_posts WHERE status = 'published' AND published_at <= NOW()");
    $published = $publishedStmt->fetch(PDO::FETCH_ASSOC)['total'];
    echo "<p><strong>Published posts (status='published' AND published_at <= NOW()):</strong> {$published}</p>";
    
    // Show all posts
    $allStmt = $pdo->query("SELECT id, slug, title_en, status, published_at, created_at FROM blog_posts ORDER BY created_at DESC LIMIT 20");
    $allPosts = $allStmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($allPosts)) {
        echo "<p style='color: red;'><strong>No posts found in database!</strong></p>";
        echo "<p>Please run: <a href='seed-blog-articles.php'>seed-blog-articles.php</a></p>";
    } else {
        echo "<h2>All Posts (last 20):</h2>";
        echo "<table border='1' cellpadding='10' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>ID</th><th>Slug</th><th>Title</th><th>Status</th><th>Published At</th><th>Created At</th></tr>";
        foreach ($allPosts as $post) {
            $statusColor = $post['status'] === 'published' ? 'green' : 'orange';
            echo "<tr>";
            echo "<td>{$post['id']}</td>";
            echo "<td>{$post['slug']}</td>";
            echo "<td>" . htmlspecialchars($post['title_en']) . "</td>";
            echo "<td style='color: {$statusColor};'>{$post['status']}</td>";
            echo "<td>{$post['published_at']}</td>";
            echo "<td>{$post['created_at']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    // Check if table exists
    $tableCheck = $pdo->query("SHOW TABLES LIKE 'blog_posts'");
    if ($tableCheck->rowCount() === 0) {
        echo "<p style='color: red;'><strong>ERROR: blog_posts table does not exist!</strong></p>";
        echo "<p>Please run the database schema first.</p>";
    }
    
} catch (PDOException $e) {
    echo "<p style='color: red;'><strong>Database Error:</strong> " . $e->getMessage() . "</p>";
}
?>
