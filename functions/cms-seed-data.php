<?php
/**
 * Hardcoded site content — source of truth for CMS import
 */

function cmsGetHardcodedServices(): array
{
    return [
        [
            'slug' => 'household-management',
            'category' => 'household',
            'order' => 1,
            'icon' => 'bi-house-door',
            'image' => 'assets/images/service.png',
            'title_ar' => 'إدارة المنزل الذكية',
            'title_en' => 'Smart Household Management',
            'desc_ar' => 'نحول كل عقار إلى نظام متكامل يدار عبر أنظمة ذكية ويتم تنسيقه من خلال تواصل فعال.',
            'desc_en' => 'We transform every property into an integrated system managed through smart technologies and coordinated through effective communication.',
            'features_ar' => "إنشاء نظام داخلي شامل للعمليات\nإدارة وتقييم العمليات اليومية\nتقييم شامل للعقار والموظفين\nلوحات تحكم رقمية وسجلات خدمية\nتنسيق الاستجابة الفورية للطوارئ\nمدير علاقات مخصص على مدار الساعة",
            'features_en' => "Comprehensive internal system establishment\nDaily operations management and evaluation\nComprehensive property and staff assessment\nDigital control panels and service records\nRapid emergency response coordination\nDedicated relationship manager 24/7",
            'detail' => [
                'subtitle_ar' => 'الحل المتكامل لإدارة منزلك بكفاءة واحترافية عالية',
                'subtitle_en' => 'Complete solution for managing your home with high efficiency and professionalism',
                'overview_title_ar' => 'نظرة عامة على الخدمة',
                'overview_title_en' => 'Service Overview',
                'overview_p1_ar' => 'نوفر حلولاً متكاملة لإدارة المنازل والفلل والقصور بمعايير عالمية، نجمع بين الخبرة الطويلة والتقنيات الحديثة لضمان راحتك التامة.',
                'overview_p1_en' => 'We provide comprehensive solutions for managing homes, villas, and palaces with international standards, combining extensive experience with modern technology to ensure your complete comfort.',
                'overview_p2_ar' => 'فريقنا المتخصص يتولى جميع جوانب إدارة منزلك من الإشراف على الموظفين إلى إدارة الجداول والميزانيات، مع الحفاظ على أعلى معايير الجودة والسرية.',
                'overview_p2_en' => 'Our specialized team handles all aspects of your home management from staff supervision to schedule and budget management, while maintaining the highest standards of quality and confidentiality.',
            ],
        ],
        [
            'slug' => 'property-management',
            'category' => 'properties',
            'order' => 2,
            'icon' => 'bi-building',
            'image' => 'assets/images/service-3.jpg',
            'title_ar' => 'إدارة الممتلكات',
            'title_en' => 'Property Management',
            'desc_ar' => 'إدارة شاملة لممتلكاتكم العقارية مع دمج الأنظمة الذكية والتقنيات الحديثة.',
            'desc_en' => 'Comprehensive management of your properties with integration of smart systems and modern technologies.',
            'features_ar' => "تكامل الأنظمة الذكية للمباني\nجدولة وإدارة الصيانة الوقائية\nإدارة الموظفين والمقاولين\nتقارير وتحليلات أداء العقار\nإدارة الأمن والسلامة\nتحسين كفاءة الطاقة",
            'features_en' => "Smart building systems integration\nPreventive maintenance scheduling\nStaff and contractor management\nProperty performance reports and analytics\nSecurity and safety management\nEnergy efficiency optimization",
            'detail' => [
                'subtitle_ar' => 'إدارة شاملة لممتلكاتكم العقارية بمعايير عالمية',
                'subtitle_en' => 'Comprehensive property management with international standards',
                'overview_title_ar' => 'نظرة عامة على الخدمة',
                'overview_title_en' => 'Service Overview',
                'overview_p1_ar' => 'إدارة شاملة لممتلكاتكم العقارية مع دمج الأنظمة الذكية والتقنيات الحديثة لضمان الأداء الأمثل.',
                'overview_p1_en' => 'Comprehensive management of your real estate assets with smart systems and modern technology for optimal performance.',
                'overview_p2_ar' => 'نقدم تقارير دورية، إدارة الصيانة، والتنسيق الكامل مع المقاولين والموظفين.',
                'overview_p2_en' => 'We provide regular reporting, maintenance management, and full coordination with contractors and staff.',
            ],
        ],
        [
            'slug' => 'event-management',
            'category' => 'events',
            'order' => 3,
            'icon' => 'bi-calendar-event',
            'image' => 'assets/images/service-5.jpg',
            'title_ar' => 'تنظيم الفعاليات',
            'title_en' => 'Event Management',
            'desc_ar' => 'تخطيط وتنفيذ فعاليات استثنائية من الألف إلى الياء بأعلى معايير الاحترافية.',
            'desc_en' => 'Planning and executing exceptional events from A to Z with the highest standards of professionalism.',
            'features_ar' => "التخطيط الاستراتيجي والتصميم الإبداعي\nإدارة الموردين والتنسيق الكامل\nالتنفيذ المثالي يوم الفعالية\nإدارة الضيوف والبروتوكول\nتقنيات الصوت والصورة المتقدمة\nتقييم ما بعد الفعالية",
            'features_en' => "Strategic planning and creative design\nVendor management and full coordination\nFlawless execution on event day\nGuest management and protocol\nAdvanced audio-visual technology\nPost-event analysis and reporting",
            'detail' => [
                'subtitle_ar' => 'فعاليات استثنائية من التخطيط إلى التنفيذ',
                'subtitle_en' => 'Exceptional events from planning to execution',
                'overview_title_ar' => 'نظرة عامة على الخدمة',
                'overview_title_en' => 'Service Overview',
                'overview_p1_ar' => 'تخطيط وتنفيذ فعاليات استثنائية من الألف إلى الياء بأعلى معايير الاحترافية والدقة.',
                'overview_p1_en' => 'Planning and executing exceptional events from start to finish with the highest standards of professionalism and precision.',
                'overview_p2_ar' => 'ندير كل التفاصيل: الموردين، الضيوف، البروتوكول، والتقنيات لضمان نجاح فعاليتكم.',
                'overview_p2_en' => 'We manage every detail: vendors, guests, protocol, and technology to ensure your event succeeds.',
            ],
        ],
        [
            'slug' => 'protocol-etiquette',
            'category' => 'protocol',
            'order' => 4,
            'icon' => 'bi-award',
            'image' => 'assets/images/service-6.jpg',
            'title_ar' => 'البروتوكول والإتيكيت',
            'title_en' => 'Protocol & Etiquette Training',
            'desc_ar' => 'برامج تدريبية مخصصة لتعزيز التواصل والسلوك في المواقف الرسمية واليومية.',
            'desc_en' => 'Tailored training programs to enhance communication and behavior in formal and everyday settings.',
            'features_ar' => "ورش البروتوكول الملكي والرسمي\nبرامج إتيكيت الضيافة المتقدمة\nاستراتيجيات التواصل الشخصية\nتدريب على الحضور والتعبير\nبرامج الإتيكيت للمدارس\nالتدريب الشخصي والاستشارات",
            'features_en' => "Royal and official protocol workshops\nAdvanced hospitality etiquette programs\nPersonalized communication strategies\nPresence and articulation training\nFoundational etiquette for schools\nPrivate coaching and consultations",
            'detail' => [
                'subtitle_ar' => 'برامج تدريبية راقية للبروتوكول والإتيكيت الرسمي',
                'subtitle_en' => 'Refined training programs in protocol and formal etiquette',
                'overview_title_ar' => 'نظرة عامة على الخدمة',
                'overview_title_en' => 'Service Overview',
                'overview_p1_ar' => 'برامج تدريبية مخصصة لتعزيز التواصل والسلوك في المواقف الرسمية واليومية بمعايير ملكية.',
                'overview_p1_en' => 'Tailored training programs to enhance communication and behavior in formal and everyday settings to royal standards.',
                'overview_p2_ar' => 'ورش عمل، تدريب شخصي، وبرامج للمدارس والمؤسسات.',
                'overview_p2_en' => 'Workshops, private coaching, and programs for schools and organizations.',
            ],
        ],
        [
            'slug' => 'vip-concierge',
            'category' => 'vip',
            'order' => 5,
            'icon' => 'bi-star',
            'image' => 'assets/images/service-2-914x1024.png',
            'title_ar' => 'خدمة الكونسيرج VIP',
            'title_en' => 'VIP Logistics & Consulting Service',
            'desc_ar' => 'مساعدة شخصية حصرية على مدار الساعة لتلبية جميع احتياجاتكم ورغباتكم.',
            'desc_en' => 'Exclusive 24/7 personal assistance to meet all your needs and desires.',
            'features_ar' => "مساعد شخصي متاح على مدار الساعة\nتنسيق السفر والإقامة الفاخرة\nحجوزات المطاعم والفعاليات الحصرية\nإدارة نمط الحياة الشخصي\nخدمات التسوق والهدايا الفاخرة\nطلبات خاصة ومتطلبات فريدة",
            'features_en' => "24/7 available personal assistant\nLuxury travel and accommodation coordination\nExclusive restaurant and event reservations\nPersonal lifestyle management\nLuxury shopping and gift services\nSpecial requests and unique requirements",
            'detail' => [
                'subtitle_ar' => 'مساعدة شخصية حصرية على مدار الساعة',
                'subtitle_en' => 'Exclusive 24/7 personal assistance',
                'overview_title_ar' => 'نظرة عامة على الخدمة',
                'overview_title_en' => 'Service Overview',
                'overview_p1_ar' => 'مساعدة شخصية حصرية على مدار الساعة لتلبية جميع احتياجاتكم ورغباتكم بسرية تامة.',
                'overview_p1_en' => 'Exclusive 24/7 personal assistance to meet all your needs and desires with complete discretion.',
                'overview_p2_ar' => 'من السفر والحجوزات إلى التسوق والطلبات الخاصة — نحن هنا من أجلكم.',
                'overview_p2_en' => 'From travel and reservations to shopping and special requests — we are here for you.',
            ],
        ],
        [
            'slug' => 'staff-recruitment',
            'category' => 'consulting',
            'order' => 6,
            'icon' => 'bi-people',
            'image' => 'assets/images/service-4.jpg',
            'title_ar' => 'تدريب الموظفين',
            'title_en' => 'Staff Training',
            'desc_ar' => 'تطوير وتدريب الموظفين بأعلى معايير الاحترافية.',
            'desc_en' => 'Developing and training staff with the highest standards of professionalism.',
            'features_ar' => "عملية فحص دقيقة ومتعددة المراحل\nتقييم المهارات والكفاءات\nبرامج تدريب مخصصة ومستمرة\nتدريب على البروتوكول والإتيكيت\nتقييم الأداء المستمر\nبرامج التطوير المهني",
            'features_en' => "Rigorous multi-stage vetting process\nSkills and competency assessment\nCustomized ongoing training programs\nProtocol and etiquette training\nContinuous performance evaluation\nProfessional development programs",
            'detail' => [
                'subtitle_ar' => 'تطوير وتدريب الكوادر بأعلى المعايير',
                'subtitle_en' => 'Developing and training staff to the highest standards',
                'overview_title_ar' => 'نظرة عامة على الخدمة',
                'overview_title_en' => 'Service Overview',
                'overview_p1_ar' => 'تطوير وتدريب الموظفين بأعلى معايير الاحترافية من الاختيار حتى التقييم المستمر.',
                'overview_p1_en' => 'Developing and training staff with the highest standards of professionalism from selection through ongoing evaluation.',
                'overview_p2_ar' => 'فحص دقيق، برامج تدريب مخصصة، وتطوير مهني مستمر.',
                'overview_p2_en' => 'Rigorous vetting, customized training programs, and continuous professional development.',
            ],
        ],
    ];
}

function cmsGetHardcodedAboutPage(): array
{
    return [
        'hero' => ['title_ar' => 'من نحن', 'title_en' => 'About Us'],
        'overview' => [
            'lead_ar' => 'نيش سوسيتي شركة متخصصة في تقديم حلول إدارية وتنظيمية بمعايير تعيد تعريف معنى التميز، تشمل إدارة الممتلكات الخاصة، العقارات، البروتوكول والإتيكيت الرسمي، اللوجستيات، العلاقات العامة، والخدمات التشغيلية الفاخرة.',
            'lead_en' => 'Niche Society is a company specialized in providing administrative and organizational solutions with standards that redefine the meaning of excellence, including private property management, real estate, official etiquette and protocols, logistics, public relations, and high-end operational services.',
            'text_ar' => 'مع أكثر من 25 عاماً من الخبرة في خدمة الشخصيات البارزة والعملاء الدوليين، ندير العمليات وننسق التفاصيل بأسلوب يجمع بين الدقة والخصوصية والأناقة.',
            'text_en' => 'With over 25 years of experience serving high-profile individuals and international clients, we manage operations and coordinate details in a style that combines precision, privacy, and sophistication.',
        ],
        'mission' => [
            'title_ar' => 'الرسالة',
            'title_en' => 'Our Mission',
            'text_ar' => 'تقديم حلول إدارية وتنظيمية استثنائية تجمع بين الكفاءة التشغيلية والأناقة، مع الحفاظ على أعلى معايير الخصوصية والسرية للعملاء المميزين.',
            'text_en' => 'To deliver exceptional administrative and organizational solutions that combine operational efficiency with elegance, while maintaining the highest standards of privacy and confidentiality for distinguished clients.',
        ],
        'vision' => [
            'title_ar' => 'الرؤية',
            'title_en' => 'Our Vision',
            'text_ar' => 'أن نكون المرجع الأول في مجال الخدمات الإدارية الفاخرة، معترف بنا عالمياً لتميزنا في خدمة الشخصيات الرفيعة.',
            'text_en' => 'To be the leading reference in luxury administrative services, globally recognized for our excellence in serving distinguished personalities.',
        ],
        'values' => [
            'title_ar' => 'قيمنا',
            'title_en' => 'Our Values',
            'text_ar' => 'التميز، الخصوصية، الإتقان، والأناقة الهادئة. نؤمن أن الرقي الحقيقي يُشعر به قبل أن يُرى.',
            'text_en' => 'Excellence, Privacy, Mastery, and Quiet Elegance. We believe that true sophistication is felt before it is seen.',
        ],
        'story' => [
            'title_ar' => '25 عاماً من التميز',
            'title_en' => '25 Years of Excellence',
            'lead_ar' => 'بدأت نيش سوسيتي من شغف عميق بالتحدي ورغبة لا تتزعزع في خلق حلول حديثة تترجم أعلى معايير الجودة والدقة.',
            'lead_en' => 'Niche Society was born from a deep passion for challenge and an unwavering desire to create modern solutions that translate the highest standards of quality and accuracy.',
            'text_ar' => 'بناء الأنظمة، متابعة التفاصيل، وتحقيق نتائج ملموسة كان دائماً ما يلهمنا ويدفعنا للمضي قدماً. المستحيل لم يكن موجوداً في قاموسنا، بل كان دافعاً لمزيد من الإصرار على تطوير المهارات، مواكبة أحدث التقنيات، وتقديم خدمات تلبي تطلعات العملاء وتجسد طموحنا.',
            'text_en' => 'Building systems, following details, and achieving tangible results has always been what inspires and motivates us to keep going. The impossible had no place in our dictionary, but it was a motivation to further insist on developing skills, keeping up with the latest technologies, and providing services that meet customer aspirations and embody our ambition.',
            'text2_ar' => 'نيش سوسيتي ليست مجرد مشروع، بل نتيجة لسنوات من الخبرة، التنوع الثقافي، والتحديات التي واجهناها خلال مسيرتنا المهنية. تأسست لتقديم خدمات تعزز الخصوصية، تعزز الإنتاجية، وتُنفذ بأعلى مستويات الحرفية، بهدوء وسلاسة، وتعزز الأناقة التي لا تُخطئها العين ولا يغفلها الحس.',
            'text2_en' => 'Niche Society is not just a project, but the result of years of experience, cultural diversity, and the challenges we faced during our professional career. It was founded to provide services that enhance privacy, enhance productivity, and are executed with the highest levels of craftsmanship, with calmness and smoothness, and enhance elegance that is unmistakable to the eye and not overlooked by the senses.',
        ],
    ];
}

function cmsGetHardcodedServicesPage(): array
{
    return [
        'hero' => [
            'title_ar' => 'خدماتنا',
            'title_en' => 'Our Services',
            'subtitle_ar' => 'حلول إدارية متكاملة ومعتمدة بشهادة ISO 9001:2015 لتلبية جميع احتياجاتكم',
            'subtitle_en' => 'Comprehensive management solutions certified with ISO 9001:2015 to meet all your needs',
        ],
        'intro' => [
            'badge_ar' => 'خدمات معتمدة',
            'badge_en' => 'Certified Services',
            'title_ar' => 'خدمات متخصصة للعملاء المميزين',
            'title_en' => 'Specialized Services for Distinguished Clients',
            'lead_ar' => 'نقدم مجموعة شاملة من خدمات الإدارة الفاخرة المصممة خصيصاً لتلبية احتياجات الشخصيات البارزة. كل خدمة نقدمها تجمع بين الخبرة العميقة، التقنيات المبتكرة، والاهتمام الدقيق بالتفاصيل لضمان تجربة استثنائية لعملائنا.',
            'lead_en' => 'We offer a comprehensive range of luxury management services specifically designed to meet the needs of distinguished personalities. Each service we provide combines deep expertise, innovative technologies, and meticulous attention to detail to ensure an exceptional experience for our clients.',
        ],
    ];
}
