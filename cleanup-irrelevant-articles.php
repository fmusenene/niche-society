<?php
/**
 * Cleanup Irrelevant Articles
 * Removes articles that don't match Niche Society's services
 * Run this to clean up existing articles in the database
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/functions/helpers.php';

// Include the same filtering function from RSS aggregator
function isArticleRelevant($title, $description, $content) {
    // Combine all text for searching
    $textToSearch = mb_strtolower($title . ' ' . $description . ' ' . $content, 'UTF-8');
    
    // EXCLUSION keywords - if these appear, article is NOT relevant
    $exclusionKeywords = [
        'electric vehicle', 'EV', 'electric car', 'automotive', 'car', 'vehicle',
        'cryptocurrency', 'bitcoin', 'crypto', 'blockchain',
        'stock market', 'trading', 'investment', 'financial market',
        'election', 'political', 'politics', 'government',
        'medical', 'healthcare', 'hospital', 'doctor', 'patient',
        'sports', 'game', 'match', 'player',
        'entertainment', 'movie', 'music', 'celebrity',
        'weather', 'climate', 'temperature',
        'Canada', 'US', 'USA', 'United States', 'Chinese', 'China', 'EVs', 'EV market'
        // Note: 'technology', 'tech', 'AI', 'education', 'school', 'university' removed - these topics are allowed
    ];
    
    // Check exclusion keywords first
    foreach ($exclusionKeywords as $exclusion) {
        if (mb_strpos($textToSearch, mb_strtolower($exclusion, 'UTF-8')) !== false) {
            return false; // Article is NOT relevant
        }
    }
    
    // REQUIRED keywords/phrases - must match specific phrases (more strict)
    $requiredPhrases = [
        // Estate & Property Management (must include "management" with property/estate context)
        'estate management', 'property management', 'household management',
        'property administration', 'real estate management', 'luxury property management',
        'private property management',
        
        // Concierge & Logistics (specific to services)
        'concierge service', 'VIP concierge', 'luxury concierge',
        'concierge', 'event logistics', 'operational services',
        'high-end operational',
        
        // Etiquette & Protocol (must be protocol-related)
        'etiquette', 'protocol', 'official protocol', 'royal protocol',
        'diplomatic protocol', 'business etiquette', 'formal etiquette',
        'protocol training', 'etiquette training',
        
        // High-end/Luxury Services (in service context)
        'luxury service', 'high-end service', 'premium service',
        'exclusive service', 'luxury lifestyle service',
        
        // Administrative & Organizational (in service context)
        'administrative solution', 'organizational solution',
        'administrative service', 'organizational service',
        'management solution',
        
        // Public Relations (only if related to services)
        'public relations service', 'corporate relations service',
        'client relations service',
        
        // Related service terms
        'butler service', 'household staff', 'staff management',
        'hospitality management', 'guest service', 'white glove service',
        'personalized service',
        
        // Technology in service context
        'technology', 'tech', 'AI', 'artificial intelligence', 'software',
        'smart home', 'smart property', 'property technology',
        'technology service', 'tech solution', 'digital solution',
        
        // Education & Training in service context
        'education', 'training', 'professional training', 'service training',
        'education service', 'training program', 'professional development',
        'staff training', 'management training'
    ];
    
    // Must match at least ONE required phrase
    foreach ($requiredPhrases as $phrase) {
        $phraseLower = mb_strtolower($phrase, 'UTF-8');
        if (mb_strpos($textToSearch, $phraseLower) !== false) {
            return true; // Found a relevant phrase
        }
    }
    
    // Additional check: multi-word combinations that indicate relevance
    $contextMatches = 0;
    
    // Check for "luxury" + service-related words
    if (mb_strpos($textToSearch, 'luxury') !== false) {
        $luxuryContext = ['property', 'estate', 'home', 'service', 'concierge', 'hospitality'];
        foreach ($luxuryContext as $context) {
            if (mb_strpos($textToSearch, $context) !== false) {
                $contextMatches++;
                break;
            }
        }
    }
    
    // Check for "high-end" + service-related words
    if (mb_strpos($textToSearch, 'high-end') !== false || mb_strpos($textToSearch, 'high end') !== false) {
        $highEndContext = ['property', 'estate', 'service', 'operational', 'residential'];
        foreach ($highEndContext as $context) {
            if (mb_strpos($textToSearch, $context) !== false) {
                $contextMatches++;
                break;
            }
        }
    }
    
    // Check for "property" + management/service context
    if (mb_strpos($textToSearch, 'property') !== false) {
        $propertyContext = ['management', 'administration', 'service', 'luxury', 'private', 'estate'];
        foreach ($propertyContext as $context) {
            if (mb_strpos($textToSearch, $context) !== false) {
                $contextMatches++;
                break;
            }
        }
    }
    
    // Need at least 1 context match AND a relevant phrase, or 2 context matches
    return $contextMatches >= 2;
}

echo "<h1>Cleanup Irrelevant Articles</h1>";
echo "<style>
    body { font-family: Arial, sans-serif; padding: 20px; max-width: 1200px; margin: 0 auto; }
    .relevant { color: green; }
    .irrelevant { color: red; }
    .deleted { color: orange; font-weight: bold; }
</style>";

try {
    // Get all articles
    $stmt = $pdo->query("SELECT id, slug, title_en, excerpt_en, content_en FROM blog_posts ORDER BY created_at DESC");
    $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $total = count($articles);
    $irrelevant = [];
    $relevant = [];
    $deleted = 0;
    
    echo "<p>Checking {$total} articles...</p>";
    echo "<hr>";
    
    foreach ($articles as $article) {
        $isRelevant = isArticleRelevant(
            $article['title_en'] ?? '',
            $article['excerpt_en'] ?? '',
            $article['content_en'] ?? ''
        );
        
        if ($isRelevant) {
            $relevant[] = $article;
            echo "<p class='relevant'>✓ RELEVANT: " . htmlspecialchars($article['title_en']) . "</p>";
        } else {
            $irrelevant[] = $article;
            echo "<p class='irrelevant'>✗ IRRELEVANT: " . htmlspecialchars($article['title_en']) . "</p>";
        }
    }
    
    echo "<hr>";
    echo "<h2>Summary</h2>";
    echo "<p><strong>Total articles:</strong> {$total}</p>";
    echo "<p class='relevant'><strong>Relevant articles:</strong> " . count($relevant) . "</p>";
    echo "<p class='irrelevant'><strong>Irrelevant articles:</strong> " . count($irrelevant) . "</p>";
    
    // Ask before deleting
    if (isset($_GET['delete']) && $_GET['delete'] === 'yes') {
        echo "<hr>";
        echo "<h2>Deleting Irrelevant Articles...</h2>";
        
        foreach ($irrelevant as $article) {
            $deleteStmt = $pdo->prepare("DELETE FROM blog_posts WHERE id = ?");
            $deleteStmt->execute([$article['id']]);
            $deleted++;
            echo "<p class='deleted'>Deleted: " . htmlspecialchars($article['title_en']) . "</p>";
        }
        
        echo "<hr>";
        echo "<h2>Cleanup Complete!</h2>";
        echo "<p class='deleted'><strong>Deleted {$deleted} irrelevant articles.</strong></p>";
        echo "<p>Remaining articles: " . count($relevant) . "</p>";
    } else {
        echo "<hr>";
        echo "<h2>Action Required</h2>";
        echo "<p>Found <strong>" . count($irrelevant) . " irrelevant articles</strong> that will be deleted.</p>";
        echo "<p><strong>⚠️ WARNING:</strong> This action cannot be undone!</p>";
        echo "<p><a href='?delete=yes' style='display: inline-block; padding: 10px 20px; background: #d32f2f; color: white; text-decoration: none; border-radius: 4px; margin-top: 10px;'>Delete Irrelevant Articles</a></p>";
        echo "<p><a href='blog.php' style='display: inline-block; padding: 10px 20px; background: #602234; color: white; text-decoration: none; border-radius: 4px; margin-top: 10px;'>Cancel - Go to Blog</a></p>";
    }
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}
?>
