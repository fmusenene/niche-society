<?php
/**
 * Fix Blog Posts - Direct Insert Script
 * This will add articles directly and show detailed output
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';

echo "<h1>Fix Blog Posts</h1>";
echo "<style>
    body { font-family: Arial, sans-serif; padding: 20px; max-width: 1200px; margin: 0 auto; }
    .success { color: green; font-weight: bold; }
    .error { color: red; font-weight: bold; }
    .info { color: blue; }
    table { border-collapse: collapse; width: 100%; margin: 20px 0; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background-color: #602234; color: white; }
</style>";

try {
    // Check database connection
    echo "<h2>Step 1: Database Connection</h2>";
    $test = $pdo->query("SELECT 1");
    echo "<p class='success'>✅ Database connection successful!</p>";
    
    // Check if table exists
    echo "<h2>Step 2: Check Table</h2>";
    $tableCheck = $pdo->query("SHOW TABLES LIKE 'blog_posts'");
    if ($tableCheck->rowCount() === 0) {
        echo "<p class='error'>❌ blog_posts table does not exist! Please run the database schema first.</p>";
        exit;
    }
    echo "<p class='success'>✅ blog_posts table exists</p>";
    
    // Check current posts
    echo "<h2>Step 3: Current Posts</h2>";
    $currentStmt = $pdo->query("SELECT COUNT(*) as total FROM blog_posts");
    $currentTotal = $currentStmt->fetch(PDO::FETCH_ASSOC)['total'];
    echo "<p class='info'>Current posts in database: <strong>{$currentTotal}</strong></p>";
    
    $publishedStmt = $pdo->query("SELECT COUNT(*) as total FROM blog_posts WHERE status = 'published' AND published_at <= NOW()");
    $publishedTotal = $publishedStmt->fetch(PDO::FETCH_ASSOC)['total'];
    echo "<p class='info'>Published posts (visible on blog): <strong>{$publishedTotal}</strong></p>";
    
    // Show all posts with status
    $allPostsStmt = $pdo->query("SELECT id, slug, title_en, status, published_at FROM blog_posts ORDER BY id DESC LIMIT 10");
    $allPosts = $allPostsStmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (!empty($allPosts)) {
        echo "<h3>Existing Posts:</h3>";
        echo "<table>";
        echo "<tr><th>ID</th><th>Slug</th><th>Title</th><th>Status</th><th>Published At</th></tr>";
        foreach ($allPosts as $post) {
            $statusColor = $post['status'] === 'published' ? 'green' : 'orange';
            echo "<tr>";
            echo "<td>{$post['id']}</td>";
            echo "<td>{$post['slug']}</td>";
            echo "<td>" . htmlspecialchars(substr($post['title_en'], 0, 50)) . "...</td>";
            echo "<td style='color: {$statusColor};'>{$post['status']}</td>";
            echo "<td>{$post['published_at']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    // If no published posts, add them
    if ($publishedTotal == 0) {
        echo "<h2>Step 4: Adding Articles</h2>";
        
        // Simple article data
        $articles = [
            [
                'slug' => 'art-of-understated-luxury-in-modern-estates',
                'title_en' => 'The Art of Understated Luxury in Modern Estates',
                'title_ar' => 'فن الترف الهادئ في العقارات الحديثة',
                'excerpt_en' => 'True luxury whispers rather than shouts. Discover how the world\'s most discerning families are embracing quiet elegance in their private estates.',
                'excerpt_ar' => 'الترف الحقيقي يهمس ولا يصرخ. اكتشف كيف تتبنى العائلات الأكثر تميزاً في العالم الأناقة الهادئة في عقاراتهم الخاصة.',
                'content_en' => '<p>In the world of ultra-high-net-worth estate management, there has been a remarkable shift. The ostentatious displays of wealth that once dominated luxury living have given way to something far more sophisticated: quiet luxury.</p><p>After 25 years of serving distinguished clients, we have witnessed this evolution firsthand. Today\'s elite are not seeking to impress others—they are curating environments that bring genuine peace and joy.</p>',
                'content_ar' => '<p>في عالم إدارة العقارات لأصحاب الثروات الضخمة، حدث تحول ملحوظ. لقد فسح المجال أمام إظهار الثروة المتفاخر الذي كان يهيمن على الحياة الفاخرة لشيء أكثر تطوراً: الترف الهادئ.</p>',
                'category' => 'News',
                'featured_image' => 'assets/images/niche-society-homepage-1-scaled.jpg',
                'published_at' => date('Y-m-d H:i:s', strtotime('-30 days'))
            ],
            [
                'slug' => 'protocol-excellence-royal-etiquette-training',
                'title_en' => 'Protocol Excellence: Training the Next Generation of Leaders',
                'title_ar' => 'التميز في البروتوكول: تدريب الجيل القادم من القادة',
                'excerpt_en' => 'Protocol and etiquette are not outdated traditions—they are essential tools for graceful leadership in a modern world.',
                'excerpt_ar' => 'البروتوكول والإتيكيت ليست تقاليد عفا عليها الزمن - بل هي أدوات أساسية للقيادة الراقية في عالم حديث.',
                'content_en' => '<p>There is a common misconception that etiquette and protocol are relics of a bygone era. The truth could not be more different.</p><p>In our quarter-century of working with distinguished institutions, we have seen how proper protocol serves as the foundation for meaningful interaction and diplomatic success.</p>',
                'content_ar' => '<p>هناك مفهوم خاطئ شائع بأن الإتيكيت والبروتوكول هي بقايا حقبة ماضية. الحقيقة لا يمكن أن تكون أكثر اختلافاً.</p>',
                'category' => 'Protocol & Etiquette',
                'featured_image' => 'assets/images/service-6.jpg',
                'published_at' => date('Y-m-d H:i:s', strtotime('-25 days'))
            ],
            [
                'slug' => 'smart-home-integration-invisible-technology',
                'title_en' => 'Smart Home Integration: When Technology Becomes Invisible',
                'title_ar' => 'تكامل المنزل الذكي: عندما تصبح التكنولوجيا غير مرئية',
                'excerpt_en' => 'The best smart home systems are the ones you never think about—they simply work, anticipating your needs before you voice them.',
                'excerpt_ar' => 'أفضل أنظمة المنزل الذكي هي تلك التي لا تفكر فيها أبداً - فهي تعمل ببساطة، متوقعة احتياجاتك قبل أن تعبر عنها.',
                'content_en' => '<p>Walk into a truly smart estate, and you might not notice anything unusual. The lights are perfect, the temperature ideal, your favorite music playing softly.</p><p>That is the hallmark of expert smart home integration: technology so well-implemented that it becomes invisible.</p>',
                'content_ar' => '<p>عند الدخول إلى عقار ذكي حقاً، قد لا تلاحظ أي شيء غير عادي. الإضاءة مثالية، درجة الحرارة مثالية.</p>',
                'category' => 'Smart Home',
                'featured_image' => 'assets/images/service.png',
                'published_at' => date('Y-m-d H:i:s', strtotime('-20 days'))
            ],
            [
                'slug' => 'event-management-creating-unforgettable-experiences',
                'title_en' => 'Event Management: Creating Unforgettable Experiences',
                'title_ar' => 'إدارة الفعاليات: خلق تجارب لا تُنسى',
                'excerpt_en' => 'From intimate gatherings to grand celebrations, discover how meticulous planning and seamless execution create events that become cherished memories.',
                'excerpt_ar' => 'من التجمعات الحميمة إلى الاحتفالات الكبرى، اكتشف كيف يخلق التخطيط الدقيق والتنفيذ السلس فعاليات تصبح ذكريات عزيزة.',
                'content_en' => '<p>Every event tells a story. The question is: what story will yours tell?</p><p>At Niche Society, we believe that exceptional events are not about extravagance—they are about intention.</p>',
                'content_ar' => '<p>كل فعالية تحكي قصة. السؤال هو: ما القصة التي ستخبرها فعاليتك؟</p>',
                'category' => 'Event Management',
                'featured_image' => 'assets/images/service-5.jpg',
                'published_at' => date('Y-m-d H:i:s', strtotime('-15 days'))
            ],
            [
                'slug' => 'twenty-five-years-serving-distinguished-clients',
                'title_en' => 'Twenty-Five Years of Serving Distinguished Clients',
                'title_ar' => 'خمسة وعشرون عاماً من خدمة العملاء المميزين',
                'excerpt_en' => 'Reflecting on a quarter-century of excellence, we share insights from our journey serving royal families and distinguished institutions.',
                'excerpt_ar' => 'بالتأمل في ربع قرن من التميز، نشارك رؤى من رحلتنا في خدمة العائلات الملكية والمؤسسات المتميزة.',
                'content_en' => '<p>Twenty-five years. It sounds like a milestone, and it is—but more importantly, it represents thousands of days of learning, growing, and perfecting our craft.</p>',
                'content_ar' => '<p>خمسة وعشرون عاماً. يبدو وكأنه معلم، وهو كذلك - ولكن الأهم من ذلك، أنه يمثل آلاف الأيام من التعلم والنمو.</p>',
                'category' => 'News',
                'featured_image' => 'assets/images/niche-society-homepage-1-scaled.jpg',
                'published_at' => date('Y-m-d H:i:s', strtotime('-10 days'))
            ]
        ];
        
        $inserted = 0;
        $failed = 0;
        
        // Check if author_id exists, use NULL if not
        $authorId = null;
        try {
            $authorCheck = $pdo->query("SELECT id FROM users WHERE id = 1 LIMIT 1");
            if ($authorCheck && $authorCheck->fetch()) {
                $authorId = 1;
            }
        } catch (PDOException $e) {
            // Users table might not exist, use NULL
            $authorId = null;
        }
        
        foreach ($articles as $article) {
            try {
                // Check if exists
                $checkStmt = $pdo->prepare("SELECT id FROM blog_posts WHERE slug = ?");
                $checkStmt->execute([$article['slug']]);
                
                if ($checkStmt->fetch()) {
                    // Update existing to published
                    $updateStmt = $pdo->prepare("UPDATE blog_posts SET status = 'published', published_at = ? WHERE slug = ?");
                    $updateStmt->execute([$article['published_at'], $article['slug']]);
                    echo "<p class='success'>✅ Updated: {$article['title_en']}</p>";
                    $inserted++;
                } else {
                    // Insert new
                    $stmt = $pdo->prepare("
                        INSERT INTO blog_posts 
                        (author_id, slug, title_en, title_ar, excerpt_en, excerpt_ar, content_en, content_ar, 
                         featured_image, category, status, published_at, views)
                        VALUES 
                        (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'published', ?, 0)
                    ");
                    
                    $stmt->execute([
                        $authorId,
                        $article['slug'],
                        $article['title_en'],
                        $article['title_ar'],
                        $article['excerpt_en'],
                        $article['excerpt_ar'],
                        $article['content_en'],
                        $article['content_ar'],
                        $article['featured_image'],
                        $article['category'],
                        $article['published_at']
                    ]);
                    
                    echo "<p class='success'>✅ Added: {$article['title_en']}</p>";
                    $inserted++;
                }
            } catch (PDOException $e) {
                $failed++;
                echo "<p class='error'>❌ Failed: {$article['title_en']} - " . $e->getMessage() . "</p>";
            }
        }
        
        echo "<hr>";
        echo "<h2>Summary</h2>";
        echo "<p class='success'>Successfully processed: <strong>{$inserted}</strong> articles</p>";
        if ($failed > 0) {
            echo "<p class='error'>Failed: <strong>{$failed}</strong> articles</p>";
        }
        
        // Verify again
        $verifyStmt = $pdo->query("SELECT COUNT(*) as total FROM blog_posts WHERE status = 'published' AND published_at <= NOW()");
        $verifyTotal = $verifyStmt->fetch(PDO::FETCH_ASSOC)['total'];
        echo "<p class='info'>Published posts now: <strong>{$verifyTotal}</strong></p>";
        
        echo "<hr>";
        echo "<p><a href='blog.php' style='display: inline-block; padding: 10px 20px; background: #602234; color: white; text-decoration: none; margin-top: 20px;'>View Blog →</a></p>";
        
    } else {
        echo "<p class='success'>✅ You already have {$publishedTotal} published articles!</p>";
        echo "<p><a href='blog.php' style='display: inline-block; padding: 10px 20px; background: #602234; color: white; text-decoration: none; margin-top: 20px;'>View Blog →</a></p>";
    }
    
} catch (PDOException $e) {
    echo "<p class='error'>❌ Database Error: " . $e->getMessage() . "</p>";
    echo "<p>Please check your database configuration in config/database.php</p>";
} catch (Exception $e) {
    echo "<p class='error'>❌ Error: " . $e->getMessage() . "</p>";
}
?>
