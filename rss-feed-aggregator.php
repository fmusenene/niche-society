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
require_once __DIR__ . '/functions/helpers.php';
require_once __DIR__ . '/functions/rss-scheduler.php';

// Prevent duplicate runs when cron and auto-scheduler overlap
if (!rssMarkAggregatorStarted()) {
    $msg = 'RSS aggregator already running. Exiting.';
    if (php_sapi_name() === 'cli') {
        echo $msg . PHP_EOL;
    }
    exit(0);
}

register_shutdown_function(static function (): void {
    $paths = rssSchedulerPaths();
    if (is_file($paths['lock'])) {
        @unlink($paths['lock']);
    }
});

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
    
    // Fetch with context (User-Agent etc.), then parse — simplexml_load_file() does not accept context in PHP 8+
    $xml = @file_get_contents($url, false, $context);
    if ($xml !== false && $xml !== '') {
        $feed = @simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
        if ($feed !== false) {
            $itemCount = isset($feed->channel->item) ? count($feed->channel->item) : 0;
            logMessage("Successfully fetched RSS feed: {$url} ({$itemCount} items)");
            libxml_clear_errors();
            return $feed;
        }
    }
    
    // If that fails, fall back to cURL (for feeds requiring custom headers)
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
 * Translate text using an external API (optional).
 *
 * IMPORTANT:
 * - To enable real translation, define TRANSLATE_API_KEY in config/config.php
 *   (e.g. Google Cloud Translation API key).
 * - If no key is defined or the API call fails, this safely falls back to the original text.
 *
 * NOTE:
 * - This runs on the server when aggregating articles, so translated Arabic text is stored
 *   once in the database (no per-request API calls on page load).
 */
function translateContent($text, $targetLang) {
    $text = trim((string)$text);
    $targetLang = trim((string)$targetLang);

    // Nothing to translate
    if ($text === '' || $targetLang === '') {
        return $text;
    }

    // No API key configured → keep original text
    if (!defined('TRANSLATE_API_KEY') || !TRANSLATE_API_KEY) {
        return $text;
    }

    // Avoid very short strings where translation is not critical (e.g. 1–2 letters)
    if (mb_strlen($text, 'UTF-8') < 3) {
        return $text;
    }

    try {
        $apiKey = TRANSLATE_API_KEY;

        // Google Cloud Translation API v2 endpoint (example)
        $endpoint = 'https://translation.googleapis.com/language/translate/v2';

        $payload = http_build_query([
            'q'      => $text,
            'target' => $targetLang,
            'format' => 'text',
            // 'source' => 'en', // optional; API can auto-detect
            'key'    => $apiKey,
        ], '', '&', PHP_QUERY_RFC3986);

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => $endpoint,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $payload,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT        => 20,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/x-www-form-urlencoded; charset=utf-8',
            ],
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($response === false || $httpCode !== 200) {
            logMessage("Translation API error ({$httpCode}): {$curlError} – falling back to original text", 'ERROR');
            return $text;
        }

        $data = json_decode($response, true);
        if (!isset($data['data']['translations'][0]['translatedText'])) {
            logMessage("Translation API response missing translatedText – falling back to original text", 'ERROR');
            return $text;
        }

        $translated = trim((string)$data['data']['translations'][0]['translatedText']);
        if ($translated === '') {
            return $text;
        }

        return $translated;
    } catch (Exception $e) {
        // On any failure, do NOT break aggregation; just log and return original
        logMessage("Translation exception: " . $e->getMessage(), 'ERROR');
        return $text;
    }
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
 * Download image from news source and save locally so it displays reliably (no hotlink blocking).
 * Returns relative path e.g. "uploads/news/abc123.jpg" or null on failure.
 */
function downloadArticleImage($imageUrl, $slug) {
    if (!preg_match('/^https?:\/\//i', $imageUrl)) return null;
    $uploadDir = __DIR__ . '/uploads/news';
    if (!is_dir($uploadDir)) {
        if (!@mkdir($uploadDir, 0755, true)) return null;
    }
    $ext = 'jpg';
    if (preg_match('/\.(jpe?g|png|gif|webp)(?:\?|$)/i', $imageUrl, $m)) $ext = strtolower($m[1]);
    if ($ext === 'jpeg') $ext = 'jpg';
    $safeSlug = preg_replace('/[^a-z0-9\-]/i', '-', substr($slug, 0, 40));
    $filename = $safeSlug . '-' . substr(md5($imageUrl), 0, 8) . '.' . $ext;
    $filepath = $uploadDir . '/' . $filename;
    $ch = curl_init($imageUrl);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS => 3,
        CURLOPT_TIMEOUT => 15,
        CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_REFERER => '',
        CURLOPT_ENCODING => '',
    ]);
    $data = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
    curl_close($ch);
    if ($data === false || $httpCode !== 200 || strlen($data) < 500) return null;
    if (preg_match('/image\/(jpeg|jpg|png|gif|webp)/i', $contentType, $m)) {
        $ext = strtolower($m[1]);
        if ($ext === 'jpeg') $ext = 'jpg';
        $filename = $safeSlug . '-' . substr(md5($imageUrl), 0, 8) . '.' . $ext;
        $filepath = $uploadDir . '/' . $filename;
    }
    if (@file_put_contents($filepath, $data) === false) return null;
    return 'uploads/news/' . $filename;
}

/**
 * Check if article matches Niche Society profile only:
 * Luxury estate/property management, event management, protocol/etiquette,
 * VIP/concierge, high-end hospitality. Middle East focus when combined with these.
 */
function isArticleRelevant($title, $description, $content) {
    $textToSearch = mb_strtolower($title . ' ' . $description . ' ' . $content, 'UTF-8');
    
    // EXCLUSION - not relevant to Niche Society
    $exclusionKeywords = [
        'electric vehicle', 'EV', 'electric car', 'automotive', 'car', 'vehicle',
        'cryptocurrency', 'bitcoin', 'crypto', 'blockchain',
        'stock market', 'trading', 'investment', 'financial market', 'forex',
        'election', 'political', 'politics', 'vote', 'campaign',
        'medical', 'healthcare', 'hospital', 'doctor', 'patient', 'vaccine',
        'sports', 'game', 'match', 'player', 'football', 'soccer', 'league',
        'entertainment', 'movie', 'music', 'celebrity', 'film', 'album',
        'weather', 'climate', 'temperature', 'flood', 'earthquake',
        'Canada', 'US ', 'USA ', 'United States ', 'Chinese ', 'China ', 'EVs ', 'EV market'
    ];
    foreach ($exclusionKeywords as $exclusion) {
        if (mb_strpos($textToSearch, mb_strtolower($exclusion, 'UTF-8')) !== false) {
            return false;
        }
    }
    
    // Niche Society profile phrases only - must match at least one
    $profilePhrases = [
        'estate management', 'property management', 'household management',
        'real estate management', 'luxury property', 'private property management',
        'concierge service', 'VIP concierge', 'luxury concierge', 'concierge',
        'event management', 'event logistics', 'corporate events', 'royal event',
        'etiquette', 'protocol', 'royal protocol', 'diplomatic protocol', 'business etiquette',
        'luxury service', 'high-end service', 'premium service', 'exclusive service',
        'hospitality management', 'guest service', 'white glove service',
        'butler service', 'household staff', 'staff management',
        'luxury real estate', 'ultra luxury', 'high-net-worth', 'high net worth',
        'private estate', 'luxury estate', 'residential management',
        'operational excellence', 'management solution', 'organizational service',
        'protocol training', 'etiquette training', 'professional training',
        'smart property', 'property technology', 'luxury hospitality'
    ];
    foreach ($profilePhrases as $phrase) {
        if (mb_strpos($textToSearch, mb_strtolower($phrase, 'UTF-8')) !== false) {
            return true;
        }
    }
    
    // Middle East + any related term: "Dubai property", "Saudi hospitality", "Jordan real estate"
    $middleEastTerms = ['saudi', 'uae', 'dubai', 'abu dhabi', 'qatar', 'kuwait', 'bahrain', 'oman', 'jordan', 'amman', 'gcc', 'middle east', 'riyadh', 'jeddah', 'doha', 'muscat', 'manama'];
    $relatedTerms = ['property', 'estate', 'luxury', 'hospitality', 'concierge', 'management', 'event', 'protocol', 'service', 'real estate', 'business', 'sector', 'market'];
    foreach ($middleEastTerms as $me) {
        if (mb_strpos($textToSearch, $me) === false) continue;
        foreach ($relatedTerms as $term) {
            if (mb_strpos($textToSearch, $term) !== false) return true;
        }
    }
    
    // Any two of these (broader so more news passes filter)
    $anyTwo = ['property', 'real estate', 'luxury', 'hospitality', 'management', 'event', 'business', 'estate'];
    $hits = 0;
    foreach ($anyTwo as $term) {
        if (mb_strpos($textToSearch, $term) !== false) $hits++;
        if ($hits >= 2) return true;
    }
    
    return false;
}

/**
 * Check if article is from Middle East
 */
function isMiddleEastArticle($title, $description, $content, $source) {
    $middleEastKeywords = [
        'Saudi Arabia', 'Saudi', 'UAE', 'United Arab Emirates', 'Dubai', 'Abu Dhabi',
        'Qatar', 'Kuwait', 'Bahrain', 'Oman', 'Jordan', 'Amman', 'Middle East', 'GCC',
        'Riyadh', 'Jeddah', 'Doha', 'Muscat', 'Manama',
        'KSA', 'Emirates', 'Emirati', 'Saudia', 'Qatari', 'Kuwaiti', 'Jordanian'
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
    
    // Arabic versions: translate once at aggregation time (if TRANSLATE_API_KEY is configured)
    $titleAr   = translateContent($titleEn, 'ar');
    $excerptAr = translateContent($excerptEn, 'ar');
    $contentAr = translateContent($contentEn, 'ar');
    
    // Extract real article image from RSS feed (multiple methods, prefer feed image over default)
    $featuredImage = null;
    $parsedLink = parse_url($link);
    $baseUrl = ($parsedLink && isset($parsedLink['scheme']) && isset($parsedLink['host']))
        ? $parsedLink['scheme'] . '://' . $parsedLink['host'] : '';

    // Method 1: media:thumbnail and media:content (Google News, BBC, Yahoo MRSS)
    $namespaces = $item->getNamespaces(true);
    $mediaNs = $namespaces['media'] ?? 'http://search.yahoo.com/mrss/';
    $media = @$item->children($mediaNs);
    if ($media && isset($media->thumbnail)) {
        $attrs = $media->thumbnail->attributes();
        if (isset($attrs['url']) && (string)$attrs['url'] !== '') {
            $featuredImage = (string)$attrs['url'];
        }
    }
    if (empty($featuredImage) && $media && isset($media->content)) {
        foreach ($media->content as $content) {
            $attrs = $content->attributes();
            $type = isset($attrs['type']) ? (string)$attrs['type'] : '';
            if (strpos($type, 'image/') === 0 && isset($attrs['url'])) {
                $featuredImage = (string)$attrs['url'];
                break;
            }
        }
    }

    // Method 2: enclosure (RSS 2.0 – single or multiple)
    if (empty($featuredImage) && isset($item->enclosure)) {
        $enclosures = is_array($item->enclosure) ? $item->enclosure : [$item->enclosure];
        foreach ($enclosures as $enc) {
            $type = isset($enc['type']) ? (string)$enc['type'] : '';
            if (strpos($type, 'image/') === 0 && isset($enc['url'])) {
                $featuredImage = (string)$enc['url'];
                break;
            }
        }
    }

    // Method 3: Atom link rel="enclosure"
    if (empty($featuredImage) && isset($item->link)) {
        $links = is_array($item->link) ? $item->link : [$item->link];
        foreach ($links as $l) {
            $rel = isset($l['rel']) ? (string)$l['rel'] : '';
            $type = isset($l['type']) ? (string)$l['type'] : '';
            $href = isset($l['href']) ? (string)$l['href'] : (string)$l;
            if ((strtolower($rel) === 'enclosure' || strtolower($rel) === '') && strpos($type, 'image/') === 0 && $href !== '') {
                $featuredImage = $href;
                break;
            }
        }
    }

    // Method 4: First image in description or content (BBC, Al Jazeera often put img here)
    if (empty($featuredImage)) {
        $htmlContent = $descriptionEn . ' ' . $contentEn;
        // img src="..."
        if (preg_match('/<img[^>]+src=["\']([^"\']+)["\']/i', $htmlContent, $m)) {
            $featuredImage = html_entity_decode(trim($m[1]), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }
        // data-src (lazy-loaded)
        if (empty($featuredImage) && preg_match('/<img[^>]+data-src=["\']([^"\']+)["\']/i', $htmlContent, $m)) {
            $featuredImage = html_entity_decode(trim($m[1]), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }
        // srcset first URL (e.g. "url 1x, url2 2x")
        if (empty($featuredImage) && preg_match('/<img[^>]+srcset=["\']([^"\']+)["\']/i', $htmlContent, $m)) {
            $srcset = preg_split('/\s*,\s*/', trim($m[1]), 2);
            $first = trim(preg_replace('/\s+\d+x$/', '', $srcset[0]));
            if ($first !== '') $featuredImage = html_entity_decode($first, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }
        // Fallback: any URL ending with image extension
        if (empty($featuredImage) && preg_match('/src=["\']([^"\']+\.(?:jpg|jpeg|png|gif|webp)(?:\?[^"\']*)?)["\']/i', $htmlContent, $m)) {
            $featuredImage = html_entity_decode(trim($m[1]), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }
    }

    // Convert relative image URLs to absolute
    if (!empty($featuredImage) && !preg_match('/^https?:\/\//i', $featuredImage) && $baseUrl !== '') {
        $featuredImage = trim($featuredImage);
        if (strpos($featuredImage, '//') === 0) {
            $featuredImage = ($parsedLink['scheme'] ?? 'https') . ':' . $featuredImage;
        } elseif (strpos($featuredImage, '/') === 0) {
            $featuredImage = $baseUrl . $featuredImage;
        } else {
            $path = isset($parsedLink['path']) ? dirname($parsedLink['path']) : '';
            $featuredImage = $baseUrl . rtrim(str_replace('\\', '/', $path), '/') . '/' . ltrim($featuredImage, '/');
        }
    }

    // Ensure we have a valid absolute URL; otherwise keep for relative resolution
    $featuredImage = trim($featuredImage);
    if ($featuredImage === '' || !preg_match('/^https?:\/\//i', $featuredImage)) {
        if ($featuredImage !== '' && $baseUrl !== '') {
            $featuredImage = $baseUrl . (strpos($featuredImage, '/') === 0 ? '' : '/') . ltrim($featuredImage, '/');
        }
    }
    if ($featuredImage === '' || !preg_match('/^https?:\/\//i', $featuredImage)) {
        logMessage("No image found for article: {$titleEn}", 'WARNING');
        $featuredImage = 'assets/images/niche-society-homepage-1-scaled.jpg';
    } else {
        logMessage("Found image for article: {$titleEn} - {$featuredImage}", 'INFO');
        // Download image locally so it always displays (avoids hotlink blocking)
        $localPath = downloadArticleImage($featuredImage, $slug);
        if ($localPath !== null) {
            $featuredImage = $localPath;
            logMessage("Saved article image locally: {$localPath}", 'INFO');
        }
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
    // MIDDLE EAST PRIORITY – reliable direct RSS (filtered by Niche Society profile)
    [
        'url' => 'https://feeds.bbci.co.uk/news/world/middle_east/rss.xml',
        'source' => 'BBC News - Middle East',
        'category' => 'Middle East',
        'region' => 'middle_east',
        'priority' => 1
    ],
    [
        'url' => 'https://www.aljazeera.com/xml/rss/all.xml',
        'source' => 'Al Jazeera - Middle East',
        'category' => 'Middle East',
        'region' => 'middle_east',
        'priority' => 1
    ],
    // Google News – topic-specific (Middle East first; may return 400)
    [
        'url' => 'https://news.google.com/rss/search?q=property+management+Saudi+Arabia&hl=en-US&gl=SA&ceid=SA:en',
        'source' => 'Google News - Saudi Arabia',
        'category' => 'Estate Management',
        'region' => 'middle_east',
        'priority' => 1,
        'skip_on_error' => true
    ],
    [
        'url' => 'https://news.google.com/rss/search?q=luxury+property+Dubai&hl=en-US&gl=AE&ceid=AE:en',
        'source' => 'Google News - UAE',
        'category' => 'Estate Management',
        'region' => 'middle_east',
        'priority' => 1,
        'skip_on_error' => true
    ],
    [
        'url' => 'https://news.google.com/rss/search?q=event+management+Dubai&hl=en-US&gl=AE&ceid=AE:en',
        'source' => 'Google News - Events UAE',
        'category' => 'Event Management',
        'region' => 'middle_east',
        'priority' => 1,
        'skip_on_error' => true
    ],
    [
        'url' => 'https://news.google.com/rss/search?q=concierge+service+UAE&hl=en-US&gl=AE&ceid=AE:en',
        'source' => 'Google News - Concierge UAE',
        'category' => 'Logistics',
        'region' => 'middle_east',
        'priority' => 1,
        'skip_on_error' => true
    ],
    [
        'url' => 'https://news.google.com/rss/search?q=property+hospitality+Jordan&hl=en-US&gl=JO&ceid=JO:en',
        'source' => 'Google News - Jordan',
        'category' => 'Middle East',
        'region' => 'middle_east',
        'priority' => 1,
        'skip_on_error' => true
    ],
    // INTERNATIONAL (lower priority)
    [
        'url' => 'https://news.google.com/rss/search?q=property+management+luxury&hl=en-US&gl=US&ceid=US:en',
        'source' => 'Google News - Luxury Property',
        'category' => 'Estate Management',
        'region' => 'international',
        'priority' => 2,
        'skip_on_error' => true
    ],
    [
        'url' => 'https://news.google.com/rss/search?q=concierge+service+VIP&hl=en-US&gl=US&ceid=US:en',
        'source' => 'Google News - VIP Concierge',
        'category' => 'Logistics',
        'region' => 'international',
        'priority' => 2,
        'skip_on_error' => true
    ],
];

// Clean up old aggregated articles (keep last 14 days so blog has more content)
$retentionDays = 14;
logMessage("Cleaning up old articles (keeping last {$retentionDays} days)...");
$deleteStmt = $pdo->prepare("DELETE FROM blog_posts WHERE tags LIKE 'source_url:%' AND created_at < DATE_SUB(NOW(), INTERVAL ? DAY)");
$deleteStmt->execute([$retentionDays]);
$deletedCount = $deleteStmt->rowCount();
logMessage("Deleted {$deletedCount} old articles (older than {$retentionDays} days)");

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

// Per-feed limit: max new articles to process per feed each run (more = more blog news; filtered by relevance)
$limitMiddleEast = 25;
$limitInternational = 15;

foreach ($feeds as $feedConfig) {
    try {
        $region = $feedConfig['region'] ?? 'international';
        $limit = ($region === 'middle_east') ? $limitMiddleEast : $limitInternational;
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
logMessage("Note: Articles are filtered to Niche Society profile only: luxury/property/estate management, event management, protocol/etiquette, concierge/VIP, hospitality.");
logMessage("Note: Middle East articles are prioritized. Aggregated articles older than {$retentionDays} days are automatically removed.");

rssMarkAggregatorFinished();

// Output summary
echo PHP_EOL . "=== RSS Feed Aggregator Summary ===" . PHP_EOL;
echo "Feeds processed: {$feedsProcessed}" . PHP_EOL;
echo "New articles saved: {$totalSaved}" . PHP_EOL;
echo "Log file: {$logFile}" . PHP_EOL;
echo PHP_EOL;

?>
