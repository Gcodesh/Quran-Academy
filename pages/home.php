<?php 
require_once '../includes/config/database.php';
require_once '../includes/classes/Database.php';
$db = (new Database())->getConnection();

// Fetch Featured Courses (e.g., top 3 by rating or random)
$stmt = $db->prepare("SELECT c.*, u.full_name as teacher_name FROM courses c LEFT JOIN users u ON c.teacher_id = u.id WHERE c.status = 'published' LIMIT 3");
$stmt->execute();
$featured_courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

include '../includes/components/header.php'; 
?>

<!-- 1. Hero Modern Section -->
<section class="hero-premium">
    <div class="hero-animated-bg"></div>
    <div class="container hero-layout">
        <div class="hero-main-content">
            <div class="hero-badge-premium" data-aos="fade-down">
                <span class="pulse-dot"></span>
                <span>منصة "بِنَاءُ الْمُسْلِمِ" التعليمية</span>
            </div>
            <h1 class="hero-title-premium" data-aos="fade-up" data-aos-delay="100">
                بِنَاءُ
                <span class="highlight-premium">الْمُسْلِمِ</span>
                <div class="underline-animated"></div>
            </h1>
            <p class="hero-subtitle-premium" data-aos="fade-up" data-aos-delay="200">
                عِلْمٌ يَبْنِي مُسْلِم
            </p>
            <div class="hero-cta-group" data-aos="fade-up" data-aos-delay="300">
                <a href="/pages/courses.php" class="btn-premium-glow">
                    <i class="fas fa-book-open"></i>
                    تصفح الدورات
                </a>
                <a href="/pages/register.php" class="btn-premium-outline">
                    <i class="fas fa-user-plus"></i>
                    انضم إلينا الآن
                </a>
            </div>
            
            <div class="hero-mini-stats" data-aos="fade-up" data-aos-delay="400">
                <div class="mini-stat">
                    <span class="count" data-target="5000">0</span>
                    <span class="label">طالب طموح</span>
                </div>
                <div class="mini-stat">
                    <span class="count" data-target="50">0</span>
                    <span class="label">دورة متخصصة</span>
                </div>
                <div class="mini-stat">
                    <span class="count" data-target="100">0</span>
                    <span class="label">معلم متميز</span>
                </div>
            </div>
        </div>

        <div class="hero-visual-side" data-aos="zoom-in" data-aos-delay="300">
            <div class="glass-orb-container">
                <div class="glass-orb orb-1"></div>
                <div class="glass-orb orb-2"></div>
                <!-- Main Floating Card -->
                <div class="featured-card-premium glass-effect floating">
                    <div class="card-inner">
                        <img src="../assets/images/logo_new.png" alt="Logo" class="card-logo-overlay">
                        <div class="card-text">
                            <h4>طريقك نحو العلم الشرعي</h4>
                            <p>نخبة من العلماء في خدمتك</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 2. Stats Section (Premium Hub) -->
<section class="stats-premium-hub">
    <div class="container">
        <div class="stats-premium-grid">
            <div class="stat-premium-card glass-effect" data-aos="fade-up">
                <div class="stat-icon-wrapper circle-emerald">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <div class="stat-info">
                    <h3 class="stat-counter" data-target="5000">0</h3>
                    <p>خريج متميز</p>
                </div>
            </div>
            <div class="stat-premium-card glass-effect" data-aos="fade-up" data-aos-delay="100">
                <div class="stat-icon-wrapper circle-gold">
                    <i class="fas fa-award"></i>
                </div>
                <div class="stat-info">
                    <h3 class="stat-counter" data-target="120">0</h3>
                    <p>شهادة معتمدة</p>
                </div>
            </div>
            <div class="stat-premium-card glass-effect" data-aos="fade-up" data-aos-delay="200">
                <div class="stat-icon-wrapper circle-teal">
                    <i class="fas fa-video"></i>
                </div>
                <div class="stat-info">
                    <h3 class="stat-counter" data-target="1200">0</h3>
                    <p>فيديو تعليمي</p>
                </div>
            </div>
            <div class="stat-premium-card glass-effect" data-aos="fade-up" data-aos-delay="300">
                <div class="stat-icon-wrapper circle-purple">
                    <i class="fas fa-star"></i>
                </div>
                <div class="stat-info">
                    <h3 class="stat-counter" data-target="4.9">0</h3>
                    <p>تقييم الطلاب</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 3. Featured Courses Section (Netflix Vibe) -->
<section class="featured-courses-netflix">
    <div class="container container-wide">
        <div class="section-header-premium">
            <div class="header-left">
                <span class="badge-accent">محتوى حصري</span>
                <h2>الدورات الأكثر تميزاً</h2>
            </div>
            <a href="/pages/courses.php" class="view-all-link">مشاهدة الكل <i class="fas fa-arrow-left"></i></a>
        </div>

        <div class="netflix-scroll-container">
            <?php foreach ($featured_courses as $index => $course): 
                $thumbnail = $course['thumbnail'] ?? '';
                // Check if file exists relative to document root if it starts with /
                // or relative to current file if it's a relative path
                $test_path = (strpos($thumbnail, '/') === 0) ? $_SERVER['DOCUMENT_ROOT'] . $thumbnail : __DIR__ . '/../' . $thumbnail;
                
                if (!$thumbnail || !file_exists($test_path)) {
                    $thumbnail = '../assets/images/placeholder.jpg';
                }
            ?>
            <div class="course-card-netflix" data-aos="fade-left" data-aos-delay="<?= $index * 100 ?>">
                <div class="card-media">
                    <img src="<?= htmlspecialchars($thumbnail) ?>" alt="<?= htmlspecialchars($course['title']) ?>">
                    <div class="card-overlay">
                        <div class="overlay-content">
                            <span class="overlay-category">دورة مميزة</span>
                            <h3><?= htmlspecialchars($course['title']) ?></h3>
                            <div class="overlay-meta">
                                <span><i class="fas fa-star"></i> 4.9</span>
                                <span><i class="fas fa-play"></i> <?= rand(15, 40) ?> درس</span>
                            </div>
                            <a href="course-details.php?id=<?= $course['id'] ?>" class="btn-play-sm"><i class="fas fa-play"></i> ابدأ الآن</a>
                        </div>
                    </div>
                </div>
                <div class="card-info-bottom">
                    <h4><?= htmlspecialchars($course['title']) ?></h4>
                    <p><?= htmlspecialchars($course['teacher_name']) ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- 4. Why Choose Us (Dark Glass Mode) -->
<section class="why-us-premium">
    <div class="container">
        <div class="section-header-center" data-aos="fade-up">
            <h2>لماذا تختار منصتنا؟</h2>
            <p>نجمع بين أصالة العلم الشرعي وأدوات التعلم الحديثة</p>
        </div>
        
        <div class="features-premium-grid">
            <div class="feature-glass-card" data-aos="fade-up">
                <div class="icon-box-premium">
                    <i class="fas fa-infinity"></i>
                </div>
                <h3>وصول غير محدود</h3>
                <p>تعلم في أي وقت ومن أي مكان عبر كافة الأجهزة الذكية</p>
            </div>
            <div class="feature-glass-card" data-aos="fade-up" data-aos-delay="100">
                <div class="icon-box-premium">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h3>محتوى موثوق</h3>
                <p>مناهجنا مراجعة ومدققة من قبل نخبة من علماء الأمة الأجلاء</p>
            </div>
            <div class="feature-glass-card" data-aos="fade-up" data-aos-delay="200">
                <div class="icon-box-premium">
                    <i class="fas fa-certificate"></i>
                </div>
                <h3>شهادات معتمدة</h3>
                <p>احصل على شهادة إتمام معتمدة تعزز مسيرتك العلمية والعملية</p>
            </div>
        </div>
    </div>
</section>

<!-- 5. Teachers Section (Trust Grid) -->
<section class="teachers-premium">
    <div class="container">
        <div class="section-header-premium">
            <div class="header-left">
                <span class="badge-accent">نخبة العلماء</span>
                <h2>المعلمون والمحاضرون</h2>
            </div>
        </div>

        <div class="teachers-premium-grid">
            <!-- Dynamic loop placeholder or manual luxury items -->
            <div class="teacher-premium-card" data-aos="fade-up">
                <div class="teacher-avatar-wrapper">
                    <img src="https://images.unsplash.com/photo-1573497019940-1c28c88b4f3e?auto=format&fit=crop&w=300" alt="Teacher">
                    <div class="teacher-rating-badge"><i class="fas fa-star"></i> 5.0</div>
                </div>
                <div class="teacher-content">
                    <h3>الأستاذة سارة أحمد</h3>
                    <p>متخصصة في القرآن الكريم والقراءات</p>
                    <div class="teacher-stats-mini">
                        <span><b>12</b> دورة</span>
                        <span><b>+500</b> طالب</span>
                    </div>
                </div>
            </div>
            <!-- More teachers following the same luxury pattern -->
            <div class="teacher-premium-card" data-aos="fade-up" data-aos-delay="100">
                <div class="teacher-avatar-wrapper">
                    <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=300" alt="Teacher">
                    <div class="teacher-rating-badge"><i class="fas fa-star"></i> 4.9</div>
                </div>
                <div class="teacher-content">
                    <h3>الأستاذ محمد علي</h3>
                    <p>دكتوراه في الفقه المقارن والعقيدة</p>
                    <div class="teacher-stats-mini">
                        <span><b>8</b> دورات</span>
                        <span><b>+350</b> طالب</span>
                    </div>
                </div>
            </div>
            <div class="teacher-premium-card" data-aos="fade-up" data-aos-delay="200">
                <div class="teacher-avatar-wrapper">
                    <img src="https://images.unsplash.com/photo-1580489944761-15a19d654956?auto=format&fit=crop&w=300" alt="Teacher">
                    <div class="teacher-rating-badge"><i class="fas fa-star"></i> 5.0</div>
                </div>
                <div class="teacher-content">
                    <h3>الأستاذة نور الهدى</h3>
                    <p>متخصصة في علوم اللغة العربية والبلاغة</p>
                    <div class="teacher-stats-mini">
                        <span><b>6</b> دورات</span>
                        <span><b>+280</b> طالب</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 6. Testimonials (Premium Quote Slider) -->
<section class="testimonials-premium">
    <div class="container">
        <div class="quote-icon-large"><i class="fas fa-quote-right"></i></div>
        <div class="testimonials-dynamic-grid">
            <div class="testimonial-premium-card" data-aos="fade-right">
                <p class="testimonial-text">"منصة بِنَاءُ الْمُسْلِمِ غيرت نظرتي للتعلم عن بعد، العلم ميسر والمحتوى في قمة الرقي."</p>
                <div class="testimonial-user">
                    <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=100" alt="User">
                    <div class="user-info">
                        <h4>أم محمد</h4>
                        <span>طالبة دراسات عليا</span>
                    </div>
                </div>
            </div>
            <div class="testimonial-premium-card" data-aos="fade-left" data-aos-delay="200">
                <p class="testimonial-text">"أفضل تجربة تعليمية شرعية خضتها، النظام سهل والمعلمون متمكنون من أدواتهم."</p>
                <div class="testimonial-user">
                    <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=100" alt="User">
                    <div class="user-info">
                        <h4>أحمد يوسف</h4>
                        <span>طالب علم</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 7. CTA Section (Final WOW) -->
<section class="cta-premium-final">
    <div class="container">
        <div class="cta-glass-box" data-aos="zoom-in">
            <h2 class="cta-title">هل أنت مستعد لبناء مستقبلك العلمي؟</h2>
            <p class="cta-subtitle">انضم إلينا الآن وكن جزءاً من مجتمعنا التعليمي المتميز</p>
            <div class="cta-buttons">
                <?php if (!isset($_SESSION['user_id'])): ?>
                    <a href="/pages/register.php" class="btn-cta-primary pulse-animation">ابدأ رحلتك الآن <i class="fas fa-arrow-left"></i></a>
                <?php else: ?>
                    <a href="/pages/courses.php" class="btn-cta-primary pulse-animation">تصفح الدورات <i class="fas fa-arrow-left"></i></a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php include '../includes/components/footer.php'; ?>
