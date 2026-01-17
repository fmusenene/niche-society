<?php
/**
 * RSS Feed Aggregator - Niche Society
 * Automatically fetches and publishes blog posts from RSS feeds
 * 
 * Usage:
 * - Run manually: php rss-feed-aggregator.php
 * - Set up cron: 0 * * * * php /path/to/rss-feed-aggregator.php (every hour)
 * 
 * RSS Feed Sources:
 * - Google News (luxury, estate management, property management)
 * - BBC Business
 * - Al Jazeera Business
 * - Reuters Business
 * - Industry blogs
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/functions/helpers.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Log file
$logFile = __DIR__ . '/logs/rss-aggregator.log';
$logDir = dirname($logFile);
if (!is_dir($logDir)) {
    mkdir($logDir, 0755, true);
}

/**
 * Log messages to file
 */
function logMessage($message, $type = 'INFO') {
    global $logFile;
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[{$timestamp}] [{$type}] {$message}" . PHP_EOL;
    file_put_contents($logFile, $logMessage, FILE_APPEND);
    echo $logMessage;
}

/**
 * Fetch RSS feed and parse it
 */
function fetchRSSFeed($url) {
    logMessage("Fetching RSS feed: {$url}");
    
    // First, try simplexml_load_file() - simpler and faster for most feeds
    libxml_use_internal_errors(true);
    
    // Create stream context with User-Agent for feeds that require it
    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'header' => [
                'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                'Accept: application/rss+xml, application/xml, text/xml, */*',
                'Accept-Language: en-US,en;q=0.9,ar;q=0.8'
            ],
            'timeout' => 30,
            'follow_location' => true,
            'max_redirects' => 5
        ],
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false
        ]
    ]);
    
    // Try simplexml_load_file() first (simpler approach as suggested)
    $feed = @simplexml_load_file($url, 'SimpleXMLElement', LIBXML_NOCDATA, $context);
    
    if ($feed !== false) {
        $itemCount = isset($feed->channel->item) ? count($feed->channel->item) : 0;
        logMessage("Successfully fetched RSS feed using simplexml_load_file(): {$url} ({$itemCount} items)");
        libxml_clear_errors();
        return $feed;
    }
    
    // If simplexml_load_file() fails, fall back to cURL (for feeds requiring custom headers)
    libxml_clear_errors();
    logMessage("simplexml_load_file() failed, trying cURL method for: {$url}");
    
    // Parse URL to encode query parameters correctly
    $parsedUrl = parse_url($url);
    if ($parsedUrl === false) {
        logMessage("Invalid URL format: {$url}", 'ERROR');
        return false;
    }
    
    // Rebuild URL with properly encoded query string
    $encodedUrl = $parsedUrl['scheme'] . '://' . $parsedUrl['host'];
    if (isset($parsedUrl['path'])) {
        $encodedUrl .= $parsedUrl['path'];
    }
    if (isset($parsedUrl['query'])) {
        // Parse and re-encode query parameters
        parse_str($parsedUrl['query'], $queryParams);
        $encodedUrl .= '?' . http_build_query($queryParams, '', '&', PHP_QUERY_RFC3986);
    }
    
    // Initialize cURL with better error handling
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $encodedUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_ENCODING, ''); // Accept any encoding
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Accept: application/rss+xml, application/xml, text/xml, */*',
        'Accept-Language: en-US,en;q=0.9,ar;q=0.8',
        'Accept-Encoding: gzip, deflate, br',
        'Connection: keep-alive'
    ]);
    
    $xml = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);
    
    if (!empty($curlError)) {
        logMessage("cURL error fetching RSS feed: {$url} - {$curlError}", 'ERROR');
        return false;
    }
    
    if ($httpCode === 400) {
        logMessage("400 Bad Request error for RSS feed: {$url} - URL may be malformed or too long", 'ERROR');
        logMessage("Original URL: {$url}", 'ERROR');
        logMessage("Encoded URL: {$encodedUrl}", 'ERROR');
        return false;
    }
    
    if ($httpCode !== 200 || empty($xml)) {
        logMessage("Failed to fetch RSS feed: {$url} (HTTP {$httpCode})", 'ERROR');
        return false;
    }
    
    // Parse XML from cURL response
    libxml_use_internal_errors(true);
    $feed = simplexml_load_string($xml);
    
    if ($feed === false) {
        $errors = libxml_get_errors();
        logMessage("Failed to parse RSS feed: {$url} - " . implode(', ', array_map(function($e) { return $e->message; }, $errors)), 'ERROR');
        libxml_clear_errors();
        return false;
    }
    
    $itemCount = isset($feed->channel->item) ? count($feed->channel->item) : 0;
    logMessage("Successfully fetched and parsed RSS feed using cURL: {$url} ({$itemCount} items)");
    libxml_clear_errors();
    return $feed;
}

/**
 * Extract text from HTML
 */
function extractTextFromHTML($html) {
    if (empty($html)) return '';
    
    // Strip HTML tags
    $text = strip_tags($html);
    // Decode HTML entities
    $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    // Clean up whitespace
    $text = preg_replace('/\s+/', ' ', $text);
    return trim($text);
}

/**
 * Generate excerpt from content
 */
function generateExcerpt($content, $length = 200) {
    $excerpt = extractTextFromHTML($content);
    if (mb_strlen($excerpt) > $length) {
        $excerpt = mb_substr($excerpt, 0, $length);
        // Find last space to avoid cutting words
        $lastSpace = mb_strrpos($excerpt, ' ');
        if ($lastSpace !== false) {
            $excerpt = mb_substr($excerpt, 0, $lastSpace);
        }
        $excerpt .= '...';
    }
    return $excerpt;
}

/**
 * Generate slug from title
 */
function generateSlug($title) {
    // Convert to lowercase
    $slug = mb_strtolower($title, 'UTF-8');
    // Remove HTML tags
    $slug = strip_tags($slug);
    // Replace non-alphanumeric characters with hyphens
    $slug = preg_replace('/[^a-z0-9]+/u', '-', $slug);
    // Remove leading/trailing hyphens
    $slug = trim($slug, '-');
    // Limit length
    if (mb_strlen($slug) > 200) {
        $slug = mb_substr($slug, 0, 200);
        $slug = rtrim($slug, '-');
    }
    return $slug;
}

/**
 * Translate text using Google Translate API (optional) or keep as-is
 * For now, we'll keep content as-is and let users know translation is needed
 */
function translateContent($text, $targetLang) {
    // For now, return the text as-is
    // In production, you could integrate Google Translate API or similar
    return $text;
}

/**
 * Check if article already exists
 */
function articleExists($slug, $pdo) {
    $stmt = $pdo->prepare("SELECT id FROM blog_posts WHERE slug = ? LIMIT 1");
    $stmt->execute([$slug]);
    return $stmt->fetch() !== false;
}

/**
 * Check if article matches relevant keywords
 */
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
        'staff training', 'management training',
        
        // Middle East country names (to prioritize Middle East articles)
        'Saudi Arabia', 'UAE', 'United Arab Emirates', 'Dubai', 'Abu Dhabi',
        'Qatar', 'Kuwait', 'Bahrain', 'Oman', 'Middle East', 'GCC',
        'Riyadh', 'Jeddah', 'Doha', 'Muscat', 'Manama'
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

/**
 * Check if article is from Middle East
 */
function isMiddleEastArticle($title, $description, $content, $source) {
    $middleEastKeywords = [
        'Saudi Arabia', 'Saudi', 'UAE', 'United Arab Emirates', 'Dubai', 'Abu Dhabi',
        'Qatar', 'Kuwait', 'Bahrain', 'Oman', 'Middle East', 'GCC',
        'Riyadh', 'Jeddah', 'Doha', 'Muscat', 'Manama',
        'KSA', 'Emirates', 'Emirati', 'Saudia', 'Qatari', 'Kuwaiti'
    ];
    
    $textToSearch = mb_strtolower($title . ' ' . $description . ' ' . $content . ' ' . $source, 'UTF-8');
    
    foreach ($middleEastKeywords as $keyword) {
        if (mb_strpos($textToSearch, mb_strtolower($keyword, 'UTF-8')) !== false) {
            return true;
        }
    }
    
    return false;
}

/**
 * Save article to database
 */
function saveArticle($item, $source, $category, $pdo, $region = 'international') {
    global $lang;
    
    // Extract data from RSS item
    $titleEn = (string)$item->title;
    $descriptionEn = isset($item->description) ? (string)$item->description : '';
    $contentEn = isset($item->{'content:encoded'}) ? (string)$item->{'content:encoded'} : $descriptionEn;
    $link = isset($item->link) ? (string)$item->link : '';
    $pubDate = isset($item->pubDate) ? strtotime((string)$item->pubDate) : time();
    
    // Check if article is from Middle East
    $isMiddleEast = ($region === 'middle_east') || isMiddleEastArticle($titleEn, $descriptionEn, $contentEn, $source);
    
    // Check if article is relevant to our services
    if (!isArticleRelevant($titleEn, $descriptionEn, $contentEn)) {
        logMessage("Article filtered out (not relevant): {$titleEn}", 'FILTER');
        return false;
    }
    
    // Generate slug
    $slug = generateSlug($titleEn);
    
    // Check if already exists
    if (articleExists($slug, $pdo)) {
        logMessage("Article already exists: {$slug}", 'SKIP');
        return false;
    }
    
    // If slug is empty or too short, use a hash
    if (empty($slug) || mb_strlen($slug) < 5) {
        $slug = 'article-' . md5($titleEn . $link . time());
    }
    
    // Make slug unique
    $originalSlug = $slug;
    $counter = 1;
    while (articleExists($slug, $pdo)) {
        $slug = $originalSlug . '-' . $counter;
        $counter++;
    }
    
    // Generate excerpt
    $excerptEn = generateExcerpt($contentEn, 200);
    if (empty($excerptEn) && !empty($descriptionEn)) {
        $excerptEn = generateExcerpt($descriptionEn, 200);
    }
    
    // For Arabic, we'll use the same content for now (can be translated later)
    $titleAr = $titleEn; // Will be translated manually or via API
    $excerptAr = $excerptEn;
    $contentAr = $contentEn;
    
    // Extract image from RSS feed (multiple methods)
    $featuredImage = null;
    
    // Method 1: Check media:thumbnail (common in Google News, BBC, etc.)
    $namespaces = $item->getNamespaces(true);
    if (isset($namespaces['media'])) {
        $media = $item->children($namespaces['media']);
        if (isset($media->thumbnail)) {
            $attrs = $media->thumbnail->attributes();
            if (isset($attrs['url'])) {
                $featuredImage = (string)$attrs['url'];
            }
        }
        // Check media:content for images
        if (empty($featuredImage) && isset($media->content)) {
            foreach ($media->content as $content) {
                $attrs = $content->attributes();
                if (isset($attrs['type']) && strpos($attrs['type'], 'image/') === 0 && isset($attrs['url'])) {
                    $featuredImage = (string)$attrs['url'];
                    break;
                }
            }
        }
    }
    
    // Method 2: Check enclosure (direct image attachment)
    if (empty($featuredImage) && isset($item->enclosure)) {
        $enclosure = $item->enclosure;
        $type = isset($enclosure['type']) ? (string)$enclosure['type'] : '';
        if (strpos($type, 'image/') === 0) {
            $featuredImage = (string)$enclosure['url'];
        }
    }
    
    // Method 3: Extract from description/content HTML
    if (empty($featuredImage)) {
        $htmlContent = $descriptionEn . ' ' . $contentEn;
        // Try multiple regex patterns for images
        if (preg_match('/<img[^>]+src=["\']([^"\']+)["\']/i', $htmlContent, $matches)) {
            $featuredImage = html_entity_decode($matches[1], ENT_QUOTES | ENT_HTML5, 'UTF-8');
        } elseif (preg_match('/src=["\']([^"\']+\.(?:jpg|jpeg|png|gif|webp)[^"\']*)["\']/i', $htmlContent, $matches)) {
            $featuredImage = html_entity_decode($matches[1], ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }
    }
    
    // Method 4: Check for Google News image in link or other sources
    if (empty($featuredImage) && strpos($link, 'news.google.com') !== false) {
        // Google News articles sometimes have images embedded differently
        // Try fetching the actual article page (lightweight check)
        // For now, we'll skip this to avoid delays
    }
    
    // Convert relative image URLs to absolute
    if (!empty($featuredImage) && !preg_match('/^https?:\/\//i', $featuredImage)) {
        // Try to build absolute URL from article link
        $parsedLink = parse_url($link);
        if ($parsedLink && isset($parsedLink['scheme']) && isset($parsedLink['host'])) {
            $baseUrl = $parsedLink['scheme'] . '://' . $parsedLink['host'];
            if (strpos($featuredImage, '//') === 0) {
                // Protocol-relative URL (//example.com/image.jpg)
                $featuredImage = $parsedLink['scheme'] . ':' . $featuredImage;
            } elseif (strpos($featuredImage, '/') === 0) {
                // Absolute path from domain root
                $featuredImage = $baseUrl . $featuredImage;
            } else {
                // Relative path
                $path = isset($parsedLink['path']) ? dirname($parsedLink['path']) : '';
                $featuredImage = $baseUrl . rtrim($path, '/') . '/' . ltrim($featuredImage, '/');
            }
        }
    }
    
    // Clean up image URL (remove query parameters that might cause issues)
    if (!empty($featuredImage) && preg_match('/^https?:\/\//i', $featuredImage)) {
        // Keep the image URL as-is if it's absolute
        // Optionally clean up: $featuredImage = preg_replace('/[?&](w|width|h|height|size)=[^&]*/i', '', $featuredImage);
    }
    
    // Log if no image found
    if (empty($featuredImage)) {
        logMessage("No image found for article: {$titleEn}", 'WARNING');
        $featuredImage = 'assets/images/niche-society-homepage-1-scaled.jpg'; // Default image
    } else {
        logMessage("Found image for article: {$titleEn} - {$featuredImage}", 'INFO');
    }
    
    // Get author ID (use 1 if exists, otherwise NULL)
    $authorId = null;
    try {
        $authorCheck = $pdo->query("SELECT id FROM users WHERE id = 1 LIMIT 1");
        if ($authorCheck && $authorCheck->fetch()) {
            $authorId = 1;
        }
    } catch (PDOException $e) {
        // Users table might not exist
    }
    
    // Store original article link and region in tags field
    $tags = 'source_url:' . $link;
    if ($isMiddleEast) {
        $tags .= ' region:middle_east priority:high';
    } else {
        $tags .= ' region:international priority:normal';
    }
    
    try {
        $stmt = $pdo->prepare("
            INSERT INTO blog_posts 
            (author_id, slug, title_en, title_ar, excerpt_en, excerpt_ar, content_en, content_ar, 
             featured_image, category, tags, status, published_at, views, created_at, updated_at)
            VALUES 
            (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'published', FROM_UNIXTIME(?), 0, NOW(), NOW())
        ");
        
        $stmt->execute([
            $authorId,
            $slug,
            $titleEn,
            $titleAr,
            $excerptEn,
            $excerptAr,
            $contentEn,
            $contentAr,
            $featuredImage,
            $category,
            $tags,
            $pubDate
        ]);
        
        $articleId = $pdo->lastInsertId();
        logMessage("Saved article: {$titleEn} (ID: {$articleId}, Slug: {$slug})", 'SUCCESS');
        return $articleId;
        
    } catch (PDOException $e) {
        logMessage("Failed to save article: {$titleEn} - " . $e->getMessage(), 'ERROR');
        return false;
    }
}

/**
 * Process RSS feed
 */
function processFeed($url, $source, $category, $pdo, $limit = 10, $region = 'international', $skipOnError = false) {
    logMessage("Processing feed: {$source} ({$category}) [Region: {$region}]");
    
    $feed = fetchRSSFeed($url);
    if (!$feed) {
        if ($skipOnError) {
            logMessage("Skipping feed due to error (skip_on_error flag set): {$source}", 'SKIP');
        }
        return 0;
    }
    
    $count = 0;
    $filtered = 0;
    $items = $feed->channel->item;
    $processed = 0;
    $checked = 0;
    
    foreach ($items as $item) {
        $checked++;
        if ($processed >= $limit) {
            break;
        }
        
        $result = saveArticle($item, $source, $category, $pdo, $region);
        if ($result === false) {
            // Article was filtered out or already exists
            $filtered++;
        } else {
            $count++;
            $processed++;
        }
    }
    
    logMessage("Checked {$checked} items from {$source}, filtered out {$filtered}, saved {$count} relevant articles");
    return $count;
}

// ============================================
// MAIN EXECUTION
// ============================================

logMessage("=== RSS Feed Aggregator Started ===");

// RSS Feed Sources Configuration - Middle East Priority
// Note: Google News RSS feeds may return 400 errors due to anti-bot measures
// Alternative reliable sources are used when available

$feeds = [
    // MIDDLE EAST PRIORITY FEEDS - Using Al Jazeera and alternative sources
    // Al Jazeera English (Middle East focus) - Reliable RSS feed
    [
        'url' => 'https://www.aljazeera.com/xml/rss/all.xml',
        'source' => 'Al Jazeera - Middle East',
        'category' => 'News',
        'region' => 'middle_east',
        'priority' => 1
    ],
    
    // Alternative: Use RSS Bridge or RSS aggregator services if Google News blocks
    // For now, we'll try simplified Google News queries with better error handling
    
    // Saudi Arabia - Simplified single keyword searches
    [
        'url' => 'https://news.google.com/rss/search?q=property+management+Saudi+Arabia&hl=en-US&gl=SA&ceid=SA:en',
        'source' => 'Google News - Saudi Arabia',
        'category' => 'Estate Management',
        'region' => 'middle_east',
        'priority' => 1,
        'skip_on_error' => true // Skip this feed if it returns 400
    ],
    [
        'url' => 'https://news.google.com/rss/search?q=concierge+Saudi+Arabia&hl=en-US&gl=SA&ceid=SA:en',
        'source' => 'Google News - Saudi Arabia',
        'category' => 'Logistics',
        'region' => 'middle_east',
        'priority' => 1,
        'skip_on_error' => true
    ],
    
    // UAE
    [
        'url' => 'https://news.google.com/rss/search?q=property+Dubai&hl=en-US&gl=AE&ceid=AE:en',
        'source' => 'Google News - UAE',
        'category' => 'Estate Management',
        'region' => 'middle_east',
        'priority' => 1,
        'skip_on_error' => true
    ],
    
    // Qatar
    [
        'url' => 'https://news.google.com/rss/search?q=property+Qatar&hl=en-US&gl=QA&ceid=QA:en',
        'source' => 'Google News - Qatar',
        'category' => 'Estate Management',
        'region' => 'middle_east',
        'priority' => 1,
        'skip_on_error' => true
    ],
    
    // INTERNATIONAL FEEDS (Lower Priority)
    [
        'url' => 'https://news.google.com/rss/search?q=property+management&hl=en-US&gl=US&ceid=US:en',
        'source' => 'Google News - International',
        'category' => 'Estate Management',
        'region' => 'international',
        'priority' => 2,
        'skip_on_error' => true
    ],
    [
        'url' => 'https://news.google.com/rss/search?q=concierge&hl=en-US&gl=US&ceid=US:en',
        'source' => 'Google News - International',
        'category' => 'Logistics',
        'region' => 'international',
        'priority' => 2,
        'skip_on_error' => true
    ],
];

// Clean up old articles (keep only last 48 hours)
logMessage("Cleaning up old articles (keeping only articles from last 48 hours)...");
$deleteStmt = $pdo->prepare("DELETE FROM blog_posts WHERE tags LIKE 'source_url:%' AND created_at < DATE_SUB(NOW(), INTERVAL 48 HOUR)");
$deleteStmt->execute();
$deletedCount = $deleteStmt->rowCount();
logMessage("Deleted {$deletedCount} old articles (older than 48 hours)");

// Sort feeds by priority (Middle East first)
usort($feeds, function($a, $b) {
    $priorityA = $a['priority'] ?? 2;
    $priorityB = $b['priority'] ?? 2;
    return $priorityA <=> $priorityB;
});

// Process each feed (Middle East feeds first)
$totalSaved = 0;
$feedsProcessed = 0;
$middleEastSaved = 0;
$internationalSaved = 0;

foreach ($feeds as $feedConfig) {
    try {
        $region = $feedConfig['region'] ?? 'international';
        $limit = ($region === 'middle_east') ? 10 : 5; // Get more from Middle East
        $skipOnError = $feedConfig['skip_on_error'] ?? false;
        
        $saved = processFeed($feedConfig['url'], $feedConfig['source'], $feedConfig['category'], $pdo, $limit, $region, $skipOnError);
        $totalSaved += $saved;
        $feedsProcessed++;
        
        if ($region === 'middle_east') {
            $middleEastSaved += $saved;
        } else {
            $internationalSaved += $saved;
        }
        
        // Small delay between feeds to avoid overwhelming servers
        sleep(2);
        
    } catch (Exception $e) {
        logMessage("Error processing feed {$feedConfig['source']}: " . $e->getMessage(), 'ERROR');
        // Continue with next feed even if one fails
        continue;
    }
}

logMessage("=== RSS Feed Aggregator Completed ===");
logMessage("Total feeds processed: {$feedsProcessed}");
logMessage("Middle East articles saved: {$middleEastSaved}");
logMessage("International articles saved: {$internationalSaved}");
logMessage("Total new articles saved: {$totalSaved}");
logMessage("Old articles deleted: {$deletedCount}");
logMessage("Note: Articles are filtered to only include relevant topics related to administrative/organizational solutions, property management, etiquette/protocol, logistics, and high-end services.");
logMessage("Note: Middle East articles are prioritized and displayed first. Articles older than 48 hours are automatically deleted.");

// Output summary
echo PHP_EOL . "=== RSS Feed Aggregator Summary ===" . PHP_EOL;
echo "Feeds processed: {$feedsProcessed}" . PHP_EOL;
echo "New articles saved: {$totalSaved}" . PHP_EOL;
echo "Log file: {$logFile}" . PHP_EOL;
echo PHP_EOL;

?>
