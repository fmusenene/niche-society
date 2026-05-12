<?php
/**
 * Seed Success Stories - Niche Society
 * Adds professional success stories to the success_stories table
 * Run this file once: http://localhost/niche-society-main/seed-success-stories.php
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';

echo "<h1>Seed Success Stories</h1>";
echo "<style>
    body { font-family: Arial, sans-serif; padding: 20px; max-width: 1200px; margin: 0 auto; }
    .success { color: green; font-weight: bold; }
    .error { color: red; font-weight: bold; }
</style>";

// Professional success stories
$stories = [
    [
        'slug' => 'royal-family-estate-management-riyadh',
        'title_en' => 'Comprehensive Estate Management for Distinguished Royal Family in Riyadh',
        'title_ar' => 'إدارة شاملة لعقار عائلة ملكية متميزة في الرياض',
        'description_en' => 'Managing a 15,000-square-meter estate with multiple residences, gardens, and staff quarters, ensuring seamless operations and absolute privacy for a royal family.',
        'description_ar' => 'إدارة عقار بمساحة 15,000 متر مربع يتضمن مساكن متعددة وحدائق ومساكن للموظفين، مع ضمان عمليات سلسة وخصوصية مطلقة لعائلة ملكية.',
        'content_en' => '<p>Over the past five years, Niche Society has managed one of Riyadh\'s most distinguished royal estates, overseeing every aspect of operations with precision and discretion.</p>

<h2>The Challenge</h2>
<p>The estate spans 15,000 square meters, includes three main residences, extensive gardens, staff quarters, and recreational facilities. The family required seamless management that preserved their privacy while maintaining the highest standards of service.</p>

<h2>Our Solution</h2>
<p>We implemented a comprehensive management system covering:</p>
<ul>
<li><strong>Property Maintenance:</strong> Preventive maintenance schedules ensuring all systems operate flawlessly</li>
<li><strong>Staff Management:</strong> Recruiting, training, and managing a team of 25 household professionals</li>
<li><strong>Event Coordination:</strong> Organizing private gatherings and official receptions with strict protocol standards</li>
<li><strong>Technology Integration:</strong> Smart home systems for security, climate control, and entertainment</li>
</ul>

<h2>Results</h2>
<p>The estate now operates with complete autonomy. Systems function without interruption, staff anticipate needs before they arise, and the family enjoys their home without management concerns.</p>

<p><em>"Niche Society transformed our home into a sanctuary. We never think about management—everything simply works."</em> - Client</p>',
        'content_ar' => '<p>خلال السنوات الخمس الماضية، أدارت نيش سوسيتي أحد أرقى العقارات الملكية في الرياض، مشرفة على كل جانب من جوانب العمليات بدقة وسرية.</p>

<h2>التحدي</h2>
<p>يمتد العقار على مساحة 15,000 متر مربع، ويشمل ثلاث مساكن رئيسية، حدائق واسعة، مساكن للموظفين، ومرافق ترفيهية. كانت العائلة تحتاج إدارة سلسة تحافظ على خصوصيتها مع الحفاظ على أعلى معايير الخدمة.</p>

<h2>حلولنا</h2>
<p>نفذنا نظام إدارة شامل يغطي:</p>
<ul>
<li><strong>صيانة الممتلكات:</strong> جداول الصيانة الوقائية لضمان عمل جميع الأنظمة بشكل لا تشوبه شائبة</li>
<li><strong>إدارة الموظفين:</strong> توظيف وتدريب وإدارة فريق من 25 متخصصاً في المنزل</li>
<li><strong>تنسيق الفعاليات:</strong> تنظيم التجمعات الخاصة والاستقبالات الرسمية بمعايير بروتوكول صارمة</li>
</ul>

<h2>النتائج</h2>
<p>يعمل العقار الآن باستقلالية كاملة. الأنظمة تعمل دون انقطاع، الموظفون يتوقعون الاحتياجات قبل ظهورها، والعائلة تستمتع بمنزلها دون مخاوف إدارية.</p>',
        'client_name_en' => 'Distinguished Royal Family',
        'client_name_ar' => 'عائلة ملكية متميزة',
        'client_type' => 'royal',
        'service_category' => 'Estate Management',
        'image' => 'assets/images/niche-society-homepage-1-scaled.jpg',
        'project_date' => date('Y-m-d', strtotime('-2 years')),
        'featured' => true,
        'display_order' => 1
    ],
    [
        'slug' => 'luxury-property-dubai-uae',
        'title_en' => 'Luxury Property Management for High-Profile Corporate Leader in Dubai',
        'title_ar' => 'إدارة عقار فاخر لرائد أعمال بارز في دبي',
        'description_en' => 'Managing multiple luxury properties in Dubai, including smart home integration, concierge services, and event coordination for a leading corporate executive.',
        'description_ar' => 'إدارة عقارات فاخرة متعددة في دبي، بما في ذلك تكامل المنزل الذكي وخدمات الكونسيرج وتنسيق الفعاليات لرائد أعمال بارز.',
        'content_en' => '<p>A leading corporate executive with properties across Dubai required comprehensive management services that allowed him to focus on business while his homes were perfectly maintained.</p>

<h2>Scope of Services</h2>
<p>Our management covers three luxury properties in Dubai, each with unique requirements:</p>
<ul>
<li><strong>Main Residence (Jumeirah):</strong> 8,000 sqm villa with smart home systems</li>
<li><strong>Business Residence (Dubai Marina):</strong> Luxury apartment for business stays</li>
<li><strong>Vacation Property (Palm Jumeirah):</strong> Beachfront villa for family gatherings</li>
</ul>

<h2>Key Achievements</h2>
<p>We integrated smart home technology across all properties, enabling remote monitoring and control. Our concierge team handles everything from restaurant reservations to travel arrangements, ensuring the client\'s needs are met before he arrives.</p>

<p>The properties are maintained at ready-to-use status year-round, allowing for spontaneous visits without any preparation needed.</p>',
        'content_ar' => '<p>رائد أعمال بارز يمتلك عقارات في جميع أنحاء دبي يحتاج خدمات إدارة شاملة تسمح له بالتركيز على العمل بينما يتم الحفاظ على منازله بشكل مثالي.</p>

<h2>نطاق الخدمات</h2>
<p>تغطي إدارتنا ثلاث عقارات فاخرة في دبي، لكل منها متطلبات فريدة:</p>
<ul>
<li><strong>المسكن الرئيسي (جميرا):</strong> فيلا بمساحة 8,000 متر مربع مع أنظمة منزل ذكية</li>
<li><strong>مسكن العمل (دبي مارينا):</strong> شقة فاخرة للإقامات التجارية</li>
<li><strong>عقار الإجازة (نخلة جميرا):</strong> فيلا على الشاطئ للتجمعات العائلية</li>
</ul>',
        'client_name_en' => 'Corporate Executive',
        'client_name_ar' => 'رائد أعمال',
        'client_type' => 'corporate',
        'service_category' => 'Property Management',
        'image' => 'assets/images/service-3.jpg',
        'project_date' => date('Y-m-d', strtotime('-18 months')),
        'featured' => true,
        'display_order' => 2
    ],
    [
        'slug' => 'protocol-training-government-officials',
        'title_en' => 'Protocol and Etiquette Training for Government Officials',
        'title_ar' => 'تدريب البروتوكول والإتيكيت للمسؤولين الحكوميين',
        'description_en' => 'Comprehensive protocol training program for senior government officials, covering diplomatic etiquette, official ceremonies, and international protocol standards.',
        'description_ar' => 'برنامج تدريبي شامل للبروتوكول للمسؤولين الحكوميين رفيعي المستوى، يغطي إتيكيت الدبلوماسية والاحتفالات الرسمية ومعايير البروتوكول الدولية.',
        'content_en' => '<p>Niche Society delivered a comprehensive protocol training program for a group of senior government officials preparing for international diplomatic assignments.</p>

<h2>Training Program</h2>
<p>The program covered:</p>
<ul>
<li>International diplomatic protocol</li>
<li>Official ceremony procedures</li>
<li>Cultural sensitivity and adaptation</li>
<li>Formal dining etiquette</li>
<li>Communication protocols for official functions</li>
</ul>

<h2>Impact</h2>
<p>All participants reported increased confidence in formal settings. The training has been credited with improving diplomatic relations and official interactions at the highest levels.</p>

<p><em>"The training was exceptional. Our team now approaches international assignments with complete confidence."</em> - Government Official</p>',
        'content_ar' => '<p>قدمت نيش سوسيتي برنامجاً تدريبياً شاملاً للبروتوكول لمجموعة من المسؤولين الحكوميين رفيعي المستوى الذين يستعدون للمهام الدبلوماسية الدولية.</p>

<h2>برنامج التدريب</h2>
<p>غطى البرنامج:</p>
<ul>
<li>البروتوكول الدبلوماسي الدولي</li>
<li>إجراءات الاحتفالات الرسمية</li>
<li>الحساسية الثقافية والتكيف</li>
<li>إتيكيت الطعام الرسمي</li>
</ul>',
        'client_name_en' => 'Government Institution',
        'client_name_ar' => 'مؤسسة حكومية',
        'client_type' => 'government',
        'service_category' => 'Protocol & Etiquette',
        'image' => 'assets/images/service-6.jpg',
        'project_date' => date('Y-m-d', strtotime('-1 year')),
        'featured' => true,
        'display_order' => 3
    ],
    [
        'slug' => 'exclusive-event-management-kuwait',
        'title_en' => 'Exclusive Event Management for Distinguished Family Celebration in Kuwait',
        'title_ar' => 'إدارة فعالية حصرية لاحتفال عائلي متميز في الكويت',
        'description_en' => 'Organized a milestone celebration for 300 distinguished guests, featuring custom catering, entertainment, and flawless protocol execution over three days.',
        'description_ar' => 'تنظيم احتفال هام لـ 300 ضيف متميز، يضم تقديم طعام مخصص وترفيه وتنفيذ بروتوكول لا تشوبه شائبة على مدى ثلاثة أيام.',
        'content_en' => '<p>A distinguished Kuwaiti family celebrated a significant milestone with a three-day event that required meticulous planning and flawless execution.</p>

<h2>Event Overview</h2>
<p>The celebration included:</p>
<ul>
<li>Welcome reception for 300 guests</li>
<li>Formal dinner with custom menu designed by international chefs</li>
<li>Cultural entertainment program</li>
<li>Private family gathering</li>
</ul>

<h2>Execution Excellence</h2>
<p>Every detail was coordinated perfectly—from guest arrival protocols to dietary accommodations, from entertainment timing to cultural sensitivity. The event ran seamlessly, allowing the family to enjoy every moment.</p>

<p>Our team managed all logistics, staff coordination, and protocol requirements, ensuring the event reflected the family\'s distinguished status while maintaining warmth and hospitality.</p>',
        'content_ar' => '<p>احتفلت عائلة كويتية متميزة بمعلم هام بفعالية استمرت ثلاثة أيام تطلبت تخطيطاً دقيقاً وتنفيذاً لا تشوبه شائبة.</p>

<h2>نظرة عامة على الفعالية</h2>
<p>شمل الاحتفال:</p>
<ul>
<li>استقبال ترحيبي لـ 300 ضيف</li>
<li>عشاء رسمي بقائمة مخصصة صممها طهاة دوليون</li>
<li>برنامج ترفيهي ثقافي</li>
<li>تجمع عائلي خاص</li>
</ul>',
        'client_name_en' => 'Distinguished Kuwaiti Family',
        'client_name_ar' => 'عائلة كويتية متميزة',
        'client_type' => 'individual',
        'service_category' => 'Event Management',
        'image' => 'assets/images/service-5.jpg',
        'project_date' => date('Y-m-d', strtotime('-8 months')),
        'featured' => false,
        'display_order' => 4
    ],
    [
        'slug' => 'vip-concierge-services-qatar',
        'title_en' => 'VIP Concierge Services for High-Profile Individual in Qatar',
        'title_ar' => 'خدمات الكونسيرج VIP لشخصية بارزة في قطر',
        'description_en' => 'Providing comprehensive VIP concierge services, including travel arrangements, exclusive access, and personalized service coordination for a high-profile client.',
        'description_ar' => 'تقديم خدمات الكونسيرج VIP الشاملة، بما في ذلك ترتيبات السفر والوصول الحصري وتنسيق الخدمات المخصصة لعميل بارز.',
        'content_en' => '<p>Our VIP concierge team has been serving a high-profile client in Qatar for three years, handling every aspect of lifestyle management with precision and discretion.</p>

<h2>Services Provided</h2>
<ul>
<li><strong>Travel Coordination:</strong> Private jet arrangements, hotel reservations, ground transportation</li>
<li><strong>Exclusive Access:</strong> VIP access to events, restaurants, and cultural experiences</li>
<li><strong>Personal Shopping:</strong> Curated shopping for luxury items, gifts, and personal needs</li>
<li><strong>Dining Reservations:</strong> Securing tables at exclusive restaurants worldwide</li>
</ul>

<h2>Client Satisfaction</h2>
<p>The client describes our service as "invisible excellence"—everything happens seamlessly without needing to request or follow up. Our team anticipates needs and executes with precision.</p>',
        'content_ar' => '<p>يخدم فريق الكونسيرج VIP لدينا عميلاً بارزاً في قطر لمدة ثلاث سنوات، ويتعامل مع كل جانب من جوانب إدارة نمط الحياة بدقة وسرية.</p>

<h2>الخدمات المقدمة</h2>
<ul>
<li><strong>تنسيق السفر:</strong> ترتيبات الطائرة الخاصة وحجوزات الفنادق والنقل البري</li>
<li><strong>الوصول الحصري:</strong> وصول VIP إلى الفعاليات والمطاعم والتجارب الثقافية</li>
<li><strong>التسوق الشخصي:</strong> تسوق مختار للعناصر الفاخرة والهدايا والاحتياجات الشخصية</li>
</ul>',
        'client_name_en' => 'High-Profile Individual',
        'client_name_ar' => 'شخصية بارزة',
        'client_type' => 'individual',
        'service_category' => 'Logistics',
        'image' => 'assets/images/service.png',
        'project_date' => date('Y-m-d', strtotime('-3 years')),
        'featured' => false,
        'display_order' => 5
    ],
    [
        'slug' => 'household-staff-management-riyadh',
        'title_en' => 'Household Staff Management and Training in Riyadh',
        'title_ar' => 'إدارة وتدريب موظفي المنزل في الرياض',
        'description_en' => 'Recruited, trained, and managed a team of household professionals for a luxury estate, establishing service standards and operational excellence.',
        'description_ar' => 'توظيف وتدريب وإدارة فريق من محترفي المنزل لعقار فاخر، وإنشاء معايير الخدمة والتميز التشغيلي.',
        'content_en' => '<p>A luxury estate in Riyadh required a complete household team, from butlers to gardeners, all trained to the highest standards of service excellence.</p>

<h2>Recruitment and Training</h2>
<p>We recruited 18 professionals across various roles:</p>
<ul>
<li>Senior butler and assistant butlers</li>
<li>Housekeepers and maintenance staff</li>
<li>Chefs and kitchen team</li>
<li>Gardeners and groundskeepers</li>
<li>Security personnel</li>
</ul>

<h2>Training Program</h2>
<p>Each team member underwent comprehensive training in:</p>
<ul>
<li>Service protocols and standards</li>
<li>Privacy and discretion</li>
<li>Estate-specific procedures</li>
<li>Emergency response</li>
</ul>

<p>The team now operates autonomously, maintaining the estate to exceptional standards while the owners enjoy complete privacy and seamless service.</p>',
        'content_ar' => '<p>تطلب عقار فاخر في الرياض فريق منزلي كاملاً، من الخدم إلى البستانيين، جميعهم مدربون على أعلى معايير التميز في الخدمة.</p>

<h2>التوظيف والتدريب</h2>
<p>قمنا بتوظيف 18 محترفاً عبر أدوار متنوعة:</p>
<ul>
<li>خادم رئيسي وخدم مساعدون</li>
<li>مدبرات منزل وموظفو صيانة</li>
<li>طهاة وفريق المطبخ</li>
<li>بستانيون وعمال أرضيات</li>
<li>أفراد أمن</li>
</ul>',
        'client_name_en' => 'Luxury Estate Owner',
        'client_name_ar' => 'مالك عقار فاخر',
        'client_type' => 'individual',
        'service_category' => 'Staff Management',
        'image' => 'assets/images/TEAM-scaled.jpg',
        'project_date' => date('Y-m-d', strtotime('-6 months')),
        'featured' => false,
        'display_order' => 6
    ]
];

try {
    $inserted = 0;
    $failed = 0;
    
    foreach ($stories as $story) {
        try {
            // Check if story already exists
            $checkStmt = $pdo->prepare("SELECT id FROM success_stories WHERE slug = ?");
            $checkStmt->execute([$story['slug']]);
            
            if ($checkStmt->fetch()) {
                echo "<p>⚠️ Story already exists: {$story['title_en']}</p>";
                continue;
            }
            
            // Insert story
            $stmt = $pdo->prepare("
                INSERT INTO success_stories 
                (slug, title_en, title_ar, description_en, description_ar, content_en, content_ar,
                 client_name_en, client_name_ar, client_type, service_category, image, 
                 project_date, featured, display_order, status)
                VALUES 
                (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'active')
            ");
            
            $stmt->execute([
                $story['slug'],
                $story['title_en'],
                $story['title_ar'],
                $story['description_en'],
                $story['description_ar'],
                $story['content_en'],
                $story['content_ar'],
                $story['client_name_en'],
                $story['client_name_ar'],
                $story['client_type'],
                $story['service_category'],
                $story['image'],
                $story['project_date'],
                $story['featured'] ? 1 : 0,
                $story['display_order']
            ]);
            
            $inserted++;
            echo "<p class='success'>✅ Added: {$story['title_en']}</p>";
            
        } catch (PDOException $e) {
            $failed++;
            echo "<p class='error'>❌ Failed: {$story['title_en']} - " . $e->getMessage() . "</p>";
        }
    }
    
    echo "<hr>";
    echo "<h2>Summary</h2>";
    echo "<p class='success'>Successfully added: <strong>{$inserted}</strong> success stories</p>";
    if ($failed > 0) {
        echo "<p class='error'>Failed: <strong>{$failed}</strong> stories</p>";
    }
    echo "<p><a href='success-stories.php' style='display: inline-block; margin-top: 20px; padding: 10px 20px; background: #602234; color: #fffaf3; text-decoration: none;'>View Success Stories →</a></p>";
    
} catch (Exception $e) {
    echo "<p class='error'><strong>Error:</strong> " . $e->getMessage() . "</p>";
}
?>
