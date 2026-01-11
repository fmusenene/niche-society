<?php
$pdo = new PDO(
    'mysql:unix_socket=/Applications/MAMP/tmp/mysql/mysql.sock;dbname=niche_society;charset=utf8mb4',
    'root',
    'root',
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

$posts = [
    [
        'slug' => 'art-of-understated-luxury-in-modern-estates',
        'title_en' => 'The Art of Understated Luxury in Modern Estates',
        'title_ar' => 'فن الترف الهادئ في العقارات الحديثة',
        'excerpt_en' => 'True luxury whispers rather than shouts. Discover how the world\'s most discerning families are embracing quiet elegance in their private estates.',
        'excerpt_ar' => 'الترف الحقيقي يهمس ولا يصرخ. اكتشف كيف تتبنى العائلات الأكثر تميزاً في العالم الأناقة الهادئة في عقاراتهم الخاصة.',
        'content_en' => '<p>In the world of ultra-high-net-worth estate management, there has been a remarkable shift. The ostentatious displays of wealth that once dominated luxury living have given way to something far more sophisticated: quiet luxury.</p>

<p>After 25 years of serving distinguished clients, we have witnessed this evolution firsthand. Today\'s elite are not seeking to impress others—they are curating environments that bring genuine peace and joy.</p>

<h2>What Defines Quiet Luxury?</h2>

<p>Quiet luxury is not about minimalism for its own sake. It is about intention. Every element in a well-managed estate serves a purpose, tells a story, or brings comfort without demanding attention.</p>

<p>Consider the difference: A visible security system shouts protection. An invisible one, seamlessly integrated into the architecture, provides the same safety with elegance. Both fulfill the function, but only one respects the aesthetic.</p>

<h2>The Five Pillars of Understated Excellence</h2>

<p><strong>1. Impeccable Materials</strong><br>
Quality speaks volumes. Hand-finished woodwork, naturally aged stone, and fabrics that improve with time create an environment that feels timeless rather than trendy.</p>

<p><strong>2. Invisible Technology</strong><br>
Smart systems should anticipate needs without intrusion. Lighting that adjusts to circadian rhythms, climate control that responds to occupancy, entertainment that emerges when wanted and disappears when not needed.</p>

<p><strong>3. Thoughtful Service</strong><br>
Staff who understand the rhythm of the household, anticipating needs before they are spoken. This level of service is not just about efficiency—it is about creating an atmosphere of effortless comfort.</p>

<p><strong>4. Curated Experiences</strong><br>
From the morning coffee ritual to evening gatherings, every experience is designed to feel natural, never forced. The art collection that evolves with the seasons, the table settings that honor guests without overwhelming them.</p>

<p><strong>5. Sustainable Elegance</strong><br>
Modern luxury embraces responsibility. Solar integration that is architecturally invisible, organic gardens that supply the kitchen, water systems that respect resources—all while maintaining the highest aesthetic standards.</p>

<h2>The Peace of Mind Factor</h2>

<p>What our clients value most is not the visible luxury—it is the invisible perfection. Knowing that every system works flawlessly, every detail is managed, every contingency planned for. This is the ultimate luxury: complete peace of mind.</p>

<p>When a home runs so smoothly that its management becomes invisible, when technology serves without intruding, when staff anticipate without hovering—that is when true luxury is achieved.</p>

<p>In an increasingly complex world, the greatest luxury is not what you can show others. It is what you can feel when you are home: completely, effortlessly at peace.</p>',
        'content_ar' => '<p>في عالم إدارة العقارات لأصحاب الثروات الضخمة، حدث تحول ملحوظ. لقد فسح المجال أمام إظهار الثروة المتفاخر الذي كان يهيمن على الحياة الفاخرة لشيء أكثر تطوراً: الترف الهادئ.</p>

<p>بعد 25 عاماً من خدمة العملاء المتميزين، شهدنا هذا التطور بشكل مباشر. نخبة اليوم لا يسعون لإبهار الآخرين - بل يصممون بيئات تجلب السلام والفرح الحقيقيين.</p>

<h2>ما الذي يحدد الترف الهادئ؟</h2>

<p>الترف الهادئ ليس عن البساطة من أجل البساطة. إنه عن النية. كل عنصر في عقار مُدار جيداً يخدم غرضاً، يروي قصة، أو يجلب الراحة دون المطالبة بالاهتمام.</p>

<h2>الأركان الخمسة للتميز الهادئ</h2>

<p><strong>1. مواد لا تشوبها شائبة</strong><br>
الجودة تتحدث عن نفسها. الأعمال الخشبية المصنوعة يدوياً، والحجر المعتق طبيعياً، والأقمشة التي تتحسن مع الوقت تخلق بيئة تبدو خالدة وليست عصرية.</p>

<p><strong>2. التكنولوجيا غير المرئية</strong><br>
يجب أن تتوقع الأنظمة الذكية الاحتياجات دون تطفل. إضاءة تتكيف مع الإيقاعات اليومية، تحكم في المناخ يستجيب للإشغال، ترفيه يظهر عند الحاجة ويختفي عندما لا يكون مطلوباً.</p>

<h2>عامل راحة البال</h2>

<p>ما يقدره عملاؤنا أكثر ليس الترف المرئي - إنه الكمال غير المرئي. معرفة أن كل نظام يعمل بشكل لا تشوبه شائبة، كل تفصيل مُدار، كل طارئ مُخطط له. هذا هو الترف النهائي: راحة البال الكاملة.</p>',
        'category' => 'Estate Management',
        'featured_image' => 'assets/images/niche-society-homepage-1-scaled.jpg'
    ],
    [
        'slug' => 'etiquette-excellence-training-royal-households',
        'title_en' => 'Etiquette Excellence: Training the Next Generation',
        'title_ar' => 'التميز في الإتيكيت: تدريب الجيل القادم',
        'excerpt_en' => 'Protocol and etiquette are not outdated traditions—they are essential tools for graceful leadership in a modern world.',
        'excerpt_ar' => 'البروتوكول والإتيكيت ليست تقاليد عفا عليها الزمن - بل هي أدوات أساسية للقيادة الراقية في عالم حديث.',
        'content_en' => '<p>There is a common misconception that etiquette and protocol are relics of a bygone era, stuffy rules meant for antiquated courts. The truth could not be more different.</p>

<p>In our quarter-century of working with distinguished institutions, we have seen how proper protocol serves as the foundation for meaningful interaction, diplomatic success, and personal confidence.</p>

<h2>Why Protocol Matters More Than Ever</h2>

<p>In an age of instant communication and global interconnection, the rules of engagement have become more complex, not less. A single gesture, misunderstood across cultures, can derail years of diplomatic work.</p>

<h2>The Modern Approach to Traditional Excellence</h2>

<p><strong>Cultural Intelligence</strong><br>
Today\'s protocol training goes far beyond which fork to use. It is about understanding the why behind every gesture, reading the room across cultures, and adapting formal traditions to contemporary contexts without losing their essence.</p>

<p><strong>Authentic Presence</strong><br>
The best etiquette does not make people stiff—it makes them confident. When you know the rules, you can focus on genuine connection rather than worrying about the next move.</p>

<p><strong>Leadership Through Grace</strong><br>
We train the next generation to see protocol not as constraint but as tool for leadership. How you greet someone, how you navigate a formal dinner, how you handle unexpected situations—these are not mere formalities. They are expressions of respect and competence.</p>

<h2>The Quiet Power of Excellence</h2>

<p>What we love most about teaching protocol is watching the transformation. Students arrive nervous, uncertain of the rules. They leave confident, capable of moving gracefully through any situation.</p>

<p>Because true etiquette is not about following rules—it is about making others feel valued, creating harmony in diverse settings, and leading with grace.</p>',
        'content_ar' => '<p>هناك مفهوم خاطئ شائع بأن الإتيكيت والبروتوكول هي بقايا حقبة ماضية. الحقيقة لا يمكن أن تكون أكثر اختلافاً.</p>

<p>في ربع القرن الذي قضيناه في العمل مع المؤسسات المتميزة، رأينا كيف يعمل البروتوكول الصحيح كأساس للتفاعل الهادف والنجاح الدبلوماسي.</p>

<h2>لماذا البروتوكول أكثر أهمية من أي وقت مضى</h2>

<p>في عصر الاتصال الفوري والترابط العالمي، أصبحت قواعد المشاركة أكثر تعقيداً وليس أقل. إيماءة واحدة، يساء فهمها عبر الثقافات، يمكن أن تعطل سنوات من العمل الدبلوماسي.</p>

<h2>القوة الهادئة للتميز</h2>

<p>ما نحبه أكثر حول تعليم البروتوكول هو مشاهدة التحول. يصل الطلاب عصبيين، غير متأكدين من القواعد. يغادرون واثقين، قادرين على التحرك بسلاسة في أي موقف.</p>',
        'category' => 'Protocol & Etiquette',
        'featured_image' => 'assets/images/service-2-914x1024.png'
    ],
    [
        'slug' => 'smart-home-integration-invisible-technology',
        'title_en' => 'Smart Home Integration: When Technology Becomes Invisible',
        'title_ar' => 'تكامل المنزل الذكي: عندما تصبح التكنولوجيا غير مرئية',
        'excerpt_en' => 'The best smart home systems are the ones you never think about—they simply work, anticipating your needs before you voice them.',
        'excerpt_ar' => 'أفضل أنظمة المنزل الذكي هي تلك التي لا تفكر فيها أبداً - فهي تعمل ببساطة، متوقعة احتياجاتك قبل أن تعبر عنها.',
        'content_en' => '<p>Walk into a truly smart estate, and you might not notice anything unusual. The lights are perfect, the temperature ideal, your favorite music playing softly. Everything feels natural, effortless.</p>

<p>That is the hallmark of expert smart home integration: technology so well-implemented that it becomes invisible.</p>

<h2>The Niche Society Approach</h2>

<p><strong>Anticipation Over Activation</strong><br>
Instead of requiring you to tell your home what to do, properly integrated systems learn your patterns and preferences. The lights adjust as the sun sets. Climate control responds to occupancy and weather.</p>

<p><strong>Single Point of Control</strong><br>
Forget juggling multiple apps. Our integrated systems work together seamlessly, controlled through intuitive interfaces or—better yet—no interface at all when the automation is sophisticated enough.</p>

<p><strong>Aesthetic Integration</strong><br>
Technology should enhance architecture, not compete with it. Speakers that disappear into ceilings, screens that emerge from furniture when needed, sensors invisible to the eye but sensitive to your needs.</p>

<h2>The Ultimate Goal</h2>

<p>When we have done our job right, you should not think about the technology at all. Your home should simply feel perfect—comfortable, secure, welcoming, and effortlessly in tune with your needs.</p>

<p>That is the true luxury of invisible technology: the complete absence of friction between intention and reality.</p>',
        'content_ar' => '<p>عند الدخول إلى عقار ذكي حقاً، قد لا تلاحظ أي شيء غير عادي. الإضاءة مثالية، درجة الحرارة مثالية، موسيقاك المفضلة تعزف بهدوء.</p>

<p>هذه هي السمة المميزة لتكامل المنزل الذكي الخبير: التكنولوجيا المنفذة بشكل جيد بحيث تصبح غير مرئية.</p>

<h2>نهج نيتش سوسيتي</h2>

<p><strong>التوقع بدلاً من التنشيط</strong><br>
بدلاً من مطالبتك بإخبار منزلك بما يجب فعله، تتعلم الأنظمة المتكاملة بشكل صحيح أنماطك وتفضيلاتك.</p>

<h2>الهدف النهائي</h2>

<p>عندما نقوم بعملنا بشكل صحيح، لا يجب أن تفكر في التكنولوجيا على الإطلاق. يجب أن يبدو منزلك ببساطة مثالياً.</p>',
        'category' => 'Smart Home',
        'featured_image' => 'assets/images/niche-society-homepage-1-scaled.jpg'
    ],
    [
        'slug' => 'staff-excellence-building-world-class-teams',
        'title_en' => 'Staff Excellence: Building World-Class Household Teams',
        'title_ar' => 'التميز في الموظفين: بناء فرق منزلية عالمية المستوى',
        'excerpt_en' => 'Behind every perfectly managed estate is an exceptional team. Here is what separates good household staff from truly extraordinary ones.',
        'excerpt_ar' => 'خلف كل عقار مُدار بشكل مثالي يوجد فريق استثنائي. إليك ما يفصل بين موظفي المنازل الجيدين والاستثنائيين حقاً.',
        'content_en' => '<p>In 25 years of household management, we have learned this: systems and technology matter, but people make the difference.</p>

<p>The finest estates in the world share a common thread—not just beautiful architecture or smart systems, but exceptional teams who transform houses into homes.</p>

<h2>What Defines Excellence?</h2>

<p><strong>Anticipation</strong><br>
They read the household. Not just memorizing preferences, but understanding the why behind them. Recognizing patterns before they are articulated.</p>

<p><strong>Discretion</strong><br>
True professionals understand that their role includes protecting privacy absolutely. They see everything, say nothing. Trust is earned through consistent, unwavering discretion.</p>

<p><strong>Pride in Craft</strong><br>
Whether they are managing a wine cellar or maintaining gardens, the best household professionals take genuine pride in their work. Excellence is not a requirement imposed from above—it is a personal standard.</p>

<h2>The Reward</h2>

<p>When we have built the right team, something remarkable happens. The household develops its own rhythm, its own culture of excellence. Staff do not just work there—they take ownership of creating perfection.</p>

<p>Because at the end of the day, luxury is not about things. It is about people—the right people, doing exceptional work, with genuine care.</p>',
        'content_ar' => '<p>في 25 عاماً من إدارة المنازل، تعلمنا هذا: الأنظمة والتكنولوجيا مهمة، لكن الناس يصنعون الفرق.</p>

<p>أرقى العقارات في العالم تشترك في خيط مشترك - ليس فقط هندسة معمارية جميلة أو أنظمة ذكية، بل فرق استثنائية تحول المنازل إلى بيوت.</p>

<h2>ما الذي يحدد التميز؟</h2>

<p><strong>التوقع</strong><br>
يقرؤون المنزل. ليس فقط حفظ التفضيلات، بل فهم السبب وراءها.</p>

<h2>المكافأة</h2>

<p>عندما نبني الفريق المناسب، يحدث شيء رائع. يطور المنزل إيقاعه الخاص، ثقافته الخاصة من التميز.</p>',
        'category' => 'Staff Management',
        'featured_image' => 'assets/images/TEAM-scaled.jpg'
    ],
    [
        'slug' => 'sustainable-luxury-environmental-responsibility',
        'title_en' => 'Sustainable Luxury: Environmental Responsibility in Estates',
        'title_ar' => 'الترف المستدام: المسؤولية البيئية في العقارات',
        'excerpt_en' => 'Modern luxury estates are proving that environmental responsibility and uncompromising elegance are not just compatible—they are inseparable.',
        'excerpt_ar' => 'تثبت العقارات الفاخرة الحديثة أن المسؤولية البيئية والأناقة غير المساومة ليست متوافقة فقط - بل لا تنفصم.',
        'content_en' => '<p>There is a revolution happening in luxury estate management, and it is changing everything we thought we knew about high-end living.</p>

<p>The old assumption—that luxury meant excess—is giving way to a more sophisticated understanding: true luxury includes responsibility.</p>

<h2>The New Standard</h2>

<p>Our most discerning clients no longer ask whether sustainability is possible in a luxury setting. They demand it. Not as a compromise, but as an enhancement.</p>

<h2>Invisible Sustainability</h2>

<p><strong>Solar Integration</strong><br>
Modern solar systems bear no resemblance to the clunky panels of decades past. Today installations integrate seamlessly with architecture—solar roof tiles indistinguishable from traditional materials.</p>

<p><strong>Water Management</strong><br>
Sophisticated rainwater harvesting systems. Greywater recycling for irrigation. Smart sensors that water gardens only when needed, saving resources while maintaining perfection.</p>

<h2>The Future of Luxury</h2>

<p>We are watching sustainability shift from optional add-on to fundamental expectation. The estates we design today anticipate not just current environmental standards but future requirements.</p>

<p>Because true luxury has always been about doing things properly. And in the 21st century, that means embracing responsibility as elegantly as we embrace beauty.</p>',
        'content_ar' => '<p>هناك ثورة تحدث في إدارة العقارات الفاخرة، وهي تغير كل ما اعتقدنا أننا نعرفه عن الحياة الراقية.</p>

<p>الافتراض القديم - أن الترف يعني الزيادة - يفسح المجال لفهم أكثر تطوراً: الترف الحقيقي يشمل المسؤولية.</p>

<h2>المعيار الجديد</h2>

<p>عملاؤنا الأكثر تمييزاً لم يعودوا يسألون عما إذا كانت الاستدامة ممكنة في بيئة فاخرة. إنهم يطالبون بها.</p>

<h2>مستقبل الترف</h2>

<p>نحن نراقب تحول الاستدامة من إضافة اختيارية إلى توقع أساسي. العقارات التي نصممها اليوم تتوقع ليس فقط المعايير البيئية الحالية بل المتطلبات المستقبلية.</p>',
        'category' => 'Sustainability',
        'featured_image' => 'assets/images/sunlit-library-escape-701x1024.jpg'
    ]
];

foreach ($posts as $post) {
    $stmt = $pdo->prepare("
        INSERT INTO blog_posts 
        (author_id, slug, title_en, title_ar, excerpt_en, excerpt_ar, content_en, content_ar, 
         featured_image, category, status, published_at, created_at, updated_at)
        VALUES 
        (1, :slug, :title_en, :title_ar, :excerpt_en, :excerpt_ar, :content_en, :content_ar,
         :featured_image, :category, 'published', NOW(), NOW(), NOW())
    ");
    
    $stmt->execute([
        'slug' => $post['slug'],
        'title_en' => $post['title_en'],
        'title_ar' => $post['title_ar'],
        'excerpt_en' => $post['excerpt_en'],
        'excerpt_ar' => $post['excerpt_ar'],
        'content_en' => $post['content_en'],
        'content_ar' => $post['content_ar'],
        'featured_image' => $post['featured_image'],
        'category' => $post['category']
    ]);
    
    echo "✅ Added: " . $post['title_en'] . "\n";
}

echo "\n🎉 Successfully added " . count($posts) . " blog posts!\n";
