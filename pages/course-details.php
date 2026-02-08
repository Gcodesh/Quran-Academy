<?php
require_once '../includes/auth_middleware.php';
require_once '../includes/config/database.php';
require_once '../includes/classes/Database.php';

$course_id = $_GET['id'] ?? 0;
if (!$course_id) {
    header('Location: courses.php');
    exit;
}

$db = (new Database())->getConnection();

// Fetch course details
$stmt = $db->prepare("SELECT c.*, u.full_name as teacher_name, u.avatar, u.bio, u.job_title, cat.name as category_name 
                      FROM courses c 
                      JOIN users u ON c.teacher_id = u.id 
                      LEFT JOIN categories cat ON c.category_id = cat.id
                      WHERE c.id = ?");
$stmt->execute([$course_id]);
$course = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$course) {
    include '../includes/components/header.php';
    echo "<div class='container' style='margin-top:100px; text-align:center;'><h2>الدورة غير موجودة</h2></div>";
    include '../includes/components/footer.php';
    exit;
}

// Check Enrollment
$is_enrolled = false;
$user_id = $_SESSION['user_id'] ?? null;
if ($user_id) {
    $stmt = $db->prepare("SELECT id FROM enrollments WHERE user_id = ? AND course_id = ?");
    $stmt->execute([$user_id, $course_id]);
    $is_enrolled = $stmt->rowCount() > 0;
}

// Fetch lessons
$stmt = $db->prepare("SELECT * FROM lessons WHERE course_id = ?");
$stmt->execute([$course_id]);
$lessons = $stmt->fetchAll(PDO::FETCH_ASSOC);

include '../includes/components/header.php';
?>


<!-- Modern Hero Section -->
<div class="course-header-modern">
    <div class="container">
        <div class="row header-content">
            <div class="col-lg-8">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb-custom">
                        <li><a href="home.php">الرئيسية</a></li>
                        <li><a href="courses.php">الدورات</a></li>
                        <li class="active"><?= htmlspecialchars($course['category_name']) ?></li>
                    </ol>
                </nav>
                <h1 class="modern-title"><?= htmlspecialchars($course['title']) ?></h1>
                <p class="modern-subtitle">تعلم بإتقان مع نخبة من أفضل المعلمين في العالم الإسلامي</p>
                
                <div class="meta-wrapper">
                    <div class="teacher-badge">
                        <img src="<?= htmlspecialchars($course['avatar']) ?: 'https://ui-avatars.com/api/?name=' . urlencode($course['teacher_name']) . '&background=random' ?>" alt="Teacher">
                        <span>انشأت بواسطة <a href="#instructor"><?= htmlspecialchars($course['teacher_name']) ?></a></span>
                    </div>
                    <div class="update-info">
                        <i class="fas fa-exclamation-circle"></i> <span>آخر تحديث: <?= date('F Y', strtotime($course['updated_at'] ?? 'now')) ?></span>
                    </div>
                    <div class="globe-info">
                        <i class="fas fa-globe"></i> <span>العربية</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php 
        // Fix relative path for thumbnail
        $thumbnail_url = $course['thumbnail'];
        if ($thumbnail_url && strpos($thumbnail_url, 'http') !== 0 && strpos($thumbnail_url, '../') !== 0) {
            $thumbnail_url = '../' . $thumbnail_url;
        }
        $thumbnail_url = $thumbnail_url ?: 'https://images.unsplash.com/photo-1609599006353-e629aaabfeae?auto=format&fit=crop&w=1200';
    ?>
    <div class="hero-overlay-bg" style="background-image: url('<?= htmlspecialchars($thumbnail_url) ?>');"></div>
</div>

<!-- Main Content Layout -->
<div class="course-body-modern">
    <div class="container">
        <div class="layout-grid">
            
            <!-- Right Content (Details) -->
            <div class="course-main-content">
                
                <!-- What you'll learn -->
                <?php if (!empty($course['learning_outcomes'])): ?>
                <div class="content-box what-you-learn">
                    <h3>ماذا ستتعلم في هذه الدورة؟</h3>
                    <div class="learn-grid">
                        <?php 
                        $outcomes = explode("\n", $course['learning_outcomes']);
                        foreach($outcomes as $outcome): 
                            if(trim($outcome) == '') continue;
                        ?>
                        <div class="learn-item"><i class="fas fa-check"></i> <?= htmlspecialchars($outcome) ?></div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Description -->
                <div class="content-box description">
                    <h3>وصف الدورة</h3>
                    <div class="text-content">
                        <?= nl2br(htmlspecialchars($course['description'])) ?>
                    </div>
                </div>

                <!-- Curriculum -->
                <div class="content-box curriculum">
                    <h3>محتوى الدورة</h3>
                    <div class="curriculum-header">
                        <span><?= count($lessons) ?> درس</span>
                    </div>
                    
                    <div class="curriculum-list">
                        <?php if(empty($lessons)): ?>
                            <div class="empty-notice">لا توجد دروس حالياً</div>
                        <?php else: ?>
                            <?php foreach($lessons as $index => $lesson): ?>
                            <div class="curriculum-item <?= $is_enrolled ? 'unlocked' : 'locked' ?>">
                                <div class="item-info">
                                    <i class="fas fa-play-circle icon-type"></i>
                                    <span class="item-title"><?= htmlspecialchars($lesson['title']) ?></span>
                                </div>
                                <div class="item-meta">
                                    <?php if($is_enrolled): ?>
                                        <a href="course-lessons.php?id=<?= $course_id ?>&lesson=<?= $lesson['id'] ?>" class="preview-btn">مشاهدة</a>
                                    <?php else: ?>
                                        <i class="fas fa-lock"></i>
                                    <?php endif; ?>
                                    <span class="duration"><?= $lesson['duration'] ?? '10:00' ?></span>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Instructor -->
                <div class="content-box instructor" id="instructor">
                    <h3>عن المعلم</h3>
                    <div class="instructor-profile">
                        <img src="<?= htmlspecialchars($course['avatar']) ?: 'https://ui-avatars.com/api/?name=' . urlencode($course['teacher_name']) . '&size=100' ?>" alt="Instructor">
                        <div class="profile-info">
                            <h4><a href="#"><?= htmlspecialchars($course['teacher_name']) ?></a></h4>
                            <p class="tagline"><?= htmlspecialchars($course['job_title'] ?? 'معلم بالمنصة') ?></p>
                            <p class="bio"><?= htmlspecialchars($course['bio'] ?? 'لا توجد نبذة مختصرة عن المعلم حالياً.') ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Left Sidebar (Sticky Card) -->
            <div class="course-sidebar-container">
                <div class="enrollment-card">
                    <!-- Video Preview Image -->
                    <div class="preview-media" onclick="openVideoModal('<?= htmlspecialchars($course['preview_video'] ?? '') ?>')">
                        <img src="<?= htmlspecialchars($thumbnail_url) ?>" alt="Preview">
                        <div class="play-overlay"><i class="fas fa-play"></i></div>
                        <div class="preview-text">معاينة الدورة</div>
                    </div>
                    
                    <div class="card-body">
                        <div class="price-section">
                            <span class="current-price"><?= $course['price'] == 0 ? 'مجاناً' : $course['price'] . ' ر.س' ?></span>
                        </div>

                        <?php if($is_enrolled): ?>
                            <a href="course-lessons.php?id=<?= $course_id ?>" class="btn-main-action active">اذهب إلى الدورة</a>
                        <?php else: ?>
                            <form action="course-enroll.php" method="POST">
                                <input type="hidden" name="course_id" value="<?= $course['id'] ?>">
                                <button type="submit" class="btn-main-action">اشترك الآن</button>
                            </form>
                            <p class="guarantee-text">وصول كامل مدى الحياة</p>
                        <?php endif; ?>

                        <div class="includes-list">
                            <h6>تتضمن هذه الدورة:</h6>
                            <ul>
                                <li><i class="fas fa-video"></i> <?= count($lessons) * 10 ?> دقيقة فيديو حسب الطلب</li>
                                <li><i class="fas fa-mobile-alt"></i> الوصول عبر الهاتف والتلفاز</li>
                                <li><i class="fas fa-certificate"></i> شهادة إتمام</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Video Modal -->
<div id="videoModal" class="video-modal" onclick="closeVideoModal()">
    <div class="modal-content" onclick="event.stopPropagation()">
        <span class="close-modal" onclick="closeVideoModal()">&times;</span>
        <div class="video-wrapper">
            <iframe id="previewFrame" src="" frameborder="0" allowfullscreen></iframe>
        </div>
    </div>
</div>

<script>
function openVideoModal(url) {
    if (!url) {
        alert('لا يوجد فيديو معاينة لهذه الدورة حالياً');
        return;
    }
    // Convert YouTube URL to Embed if needed (simple check)
    if (url.includes('watch?v=')) {
        url = url.replace('watch?v=', 'embed/');
    }
    
    document.getElementById('previewFrame').src = url + "?autoplay=1";
    document.getElementById('videoModal').style.display = 'flex';
}

function closeVideoModal() {
    document.getElementById('videoModal').style.display = 'none';
    document.getElementById('previewFrame').src = ""; // Stop video
}
</script>

<style>
/* Video Modal Styles */
.video-modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.8);
    z-index: 1000;
    justify-content: center;
    align-items: center;
}
.modal-content {
    width: 90%;
    max-width: 800px;
    background: #000;
    position: relative;
    border-radius: 8px;
    overflow: hidden;
}
.close-modal {
    position: absolute;
    top: -40px;
    right: 0;
    color: #fff;
    font-size: 30px;
    cursor: pointer;
}
.video-wrapper {
    position: relative;
    padding-bottom: 56.25%; /* 16:9 Aspect Ratio */
    height: 0;
}
.video-wrapper iframe {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
}
.preview-text {
    position: absolute;
    bottom: 10px;
    width: 100%;
    text-align: center;
    color: #fff;
    font-weight: bold;
    text-shadow: 0 2px 4px rgba(0,0,0,0.5);
}
</style>

<style>
/* Modern Course Details CSS */
:root {
    --hero-bg: #1c1d1f;
    --text-hero: #ffffff;
    --border-color: #d1d7dc;
}

body {
    background-color: #ffffff;
    font-family: 'Cairo', sans-serif; /* Utilizing Cairo as requested implicitly */
}

/* Header */
.course-header-modern {
    background-color: var(--hero-bg);
    color: var(--text-hero);
    padding: 60px 0;
    position: relative;
    overflow: hidden;
}

.hero-overlay-bg {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-size: cover;
    background-position: center;
    opacity: 0.15;
    filter: blur(2px);
    z-index: 0;
}

.header-content {
    position: relative;
    z-index: 1;
}

.breadcrumb-custom {
    list-style: none;
    padding: 0;
    display: flex;
    gap: 10px;
    font-size: 0.9rem;
    color: #a3a7ae;
}

.breadcrumb-custom a {
    color: #cec0fc;
    text-decoration: none;
    font-weight: 600;
}

.breadcrumb-custom li::after {
    content: "›";
    margin-right: 10px;
    color: #fff;
}

.breadcrumb-custom li:last-child::after {
    content: "";
}

.modern-title {
    font-size: 2.4rem;
    font-weight: 800;
    margin: 15px 0;
    line-height: 1.3;
}

.modern-subtitle {
    font-size: 1.1rem;
    margin-bottom: 25px;
    max-width: 800px;
}

.meta-wrapper {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    font-size: 0.9rem;
    align-items: center;
}

.teacher-badge img {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    margin-left: 8px;
    vertical-align: middle;
}

.teacher-badge a {
    color: #cec0fc;
    text-decoration: underline;
}

/* Layout */
.course-body-modern {
    padding: 40px 0;
}

.layout-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 50px;
}

.content-box {
    margin-bottom: 40px;
}

.content-box h3 {
    font-size: 1.5rem;
    font-weight: 700;
    margin-bottom: 20px;
    color: #2d2f31;
}

/* What you'll learn */
.what-you-learn {
    border: 1px solid var(--border-color);
    padding: 24px;
    border-radius: 8px;
    background-color: #fcfcfc;
}

.learn-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
}

.learn-item {
    font-size: 0.95rem;
    color: #2d2f31;
    display: flex;
    align-items: flex-start;
    gap: 10px;
}

.learn-item i {
    color: #10b981; /* Green checkmark */
    margin-top: 5px;
}

/* Curriculum */
.curriculum-header {
    display: flex;
    gap: 10px;
    font-size: 0.9rem;
    margin-bottom: 15px;
    color: #6a6f73;
}

.curriculum-item {
    display: flex;
    justify-content: space-between;
    padding: 15px;
    border: 1px solid var(--border-color);
    background: #fff;
    margin-bottom: -1px; /* Collapse borders */
    align-items: center;
}

.curriculum-item:first-child { border-top-left-radius: 8px; border-top-right-radius: 8px; }
.curriculum-item:last-child { border-bottom-left-radius: 8px; border-bottom-right-radius: 8px; }

.item-info {
    display: flex;
    align-items: center;
    gap: 10px;
}

.icon-type { color: #6a6f73; }

.item-meta {
    font-size: 0.85rem;
    color: #6a6f73;
    display: flex;
    align-items: center;
    gap: 15px;
}

.preview-btn {
    color: #5624d0;
    font-weight: 700;
    text-decoration: none;
    cursor: pointer;
}

/* Instructor */
.instructor-profile {
    display: flex;
    gap: 20px;
}

.instructor-profile img {
    border-radius: 50%;
    width: 100px;
    height: 100px;
}

.instructor-profile h4 a {
    color: #5624d0;
    text-decoration: underline;
    font-size: 1.1rem;
}

.tagline {
    color: #6a6f73;
    margin: 5px 0 10px;
}

/* Sidebar Enrollment Card */
.course-sidebar-container {
    position: relative;
}

.enrollment-card {
    position: sticky;
    top: 20px;
    background: #fff;
    border: 1px solid #fff;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    border-radius: 8px;
    overflow: hidden;
    z-index: 10;
}

.preview-media {
    position: relative;
    height: 200px;
    overflow: hidden;
    cursor: pointer;
}

.preview-media img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.play-overlay {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 60px;
    height: 60px;
    background: rgba(255,255,255,0.9);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #1c1d1f;
    font-size: 1.5rem;
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
}

.card-body {
    padding: 24px;
}

.current-price {
    font-size: 2rem;
    font-weight: 800;
    color: #1c1d1f;
}

.original-price {
    text-decoration: line-through;
    color: #6a6f73;
    margin-right: 10px;
}

.discount-badge {
    margin-right: 10px;
    color: #a435f0; /* Udemy discount color approx */
}

.btn-main-action {
    display: block;
    width: 100%;
    padding: 16px;
    background-color: #a435f0;
    color: #fff;
    text-align: center;
    font-weight: 700;
    font-size: 1.1rem;
    border: none;
    border-radius: 4px;
    text-decoration: none;
    margin: 20px 0 10px;
    transition: background 0.2s;
}

.btn-main-action:hover {
    background-color: #8710d8;
    color: #fff;
}

.btn-main-action.active {
    background-color: #1c1d1f;
}

.guarantee-text {
    font-size: 0.8rem;
    text-align: center;
    color: #6a6f73;
    margin-bottom: 20px;
}

.includes-list {
    font-size: 0.9rem;
}

.includes-list h6 {
    font-weight: 700;
    margin-bottom: 10px;
}

.includes-list ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.includes-list li {
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    gap: 10px;
}

/* Responsive */
@media (max-width: 991px) {
    .layout-grid {
        grid-template-columns: 1fr;
    }
    
    .course-sidebar-container {
        order: -1; /* Sidebar on top for mobile? Or maybe keeping normal flow is better */
    }
    
    .enrollment-card {
        position: static;
        margin-bottom: 30px;
    }
}
</style>

<?php include '../includes/components/footer.php'; ?>