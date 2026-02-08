<?php
require_once '../includes/auth_middleware.php';
require_once '../includes/config/database.php';
require_once '../includes/classes/Database.php';

// Authenticate
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$course_id = $_GET['id'] ?? 0;
$user_id = $_SESSION['user_id'];

if (!$course_id) {
    header("Location: courses.php");
    exit();
}

$db = (new Database())->getConnection();

// Check Enrollment
$stmt = $db->prepare("SELECT * FROM enrollments WHERE user_id = ? AND course_id = ? AND status = 'active'");
$stmt->execute([$user_id, $course_id]);
if ($stmt->rowCount() == 0) {
    header("Location: course-details.php?id=" . $course_id);
    exit();
}

// Fetch Course Info
$stmt = $db->prepare("SELECT title FROM courses WHERE id = ?");
$stmt->execute([$course_id]);
$course = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch Sections
$stmt = $db->prepare("SELECT * FROM course_sections WHERE course_id = ? ORDER BY order_number ASC, id ASC");
$stmt->execute([$course_id]);
$sections = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch Lessons organized by sections (Only Published for students)
$stmt = $db->prepare("SELECT * FROM lessons WHERE course_id = ? AND status = 'published' ORDER BY section_id ASC, order_number ASC, id ASC");
$stmt->execute([$course_id]);
$all_lessons = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Group lessons
$lessons_by_section = [];
foreach ($all_lessons as $lesson) {
    $sid = $lesson['section_id'] ?: 0;
    $lessons_by_section[$sid][] = $lesson;
}

// Get Progress
$stmt = $db->prepare("SELECT lesson_id, status FROM lesson_progress_detailed WHERE user_id = ?");
$stmt->execute([$user_id]);
$progress_data = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

// Current Lesson
$current_lesson_id = $_GET['lesson'] ?? ($all_lessons[0]['id'] ?? 0);
$current_lesson = null;
foreach ($all_lessons as $l) {
    if ($l['id'] == $current_lesson_id) {
        $current_lesson = $l;
        break;
    }
}

// Media Service Helper
require_once '../src/Services/MediaService.php';
use App\Services\MediaService;

include '../includes/components/header.php';
?>

<div class="course-viewer-modern">
    <!-- Sidebar / Playlist -->
    <aside class="viewer-sidebar">
        <div class="sidebar-header">
            <h5 class="course-subtitle">أنت تشاهد</h5>
            <h3 class="course-title"><?= htmlspecialchars($course['title']) ?></h3>
            <div class="progress-container">
                <div class="progress-bar" style="width: <?= count($lessons) > 0 ? (array_search($current_lesson_id, array_column($lessons, 'id')) + 1) / count($lessons) * 100 : 0 ?>%"></div>
            </div>
            <span class="progress-text"><?= count($lessons) > 0 ? array_search($current_lesson_id, array_column($lessons, 'id')) + 1 : 0 ?> / <?= count($lessons) ?> درس</span>
        </div>
        
        <div class="lessons-list custom-scrollbar">
            <?php if(empty($sections) && empty($lessons_by_section[0])): ?>
                <div class="empty-state">
                    <i class="far fa-folder-open"></i>
                    <p>لا توجد دروس حالياً</p>
                </div>
            <?php else: ?>
                <!-- Uncategorized -->
                <?php if(!empty($lessons_by_section[0])): ?>
                    <div class="section-divider">دروس عامة</div>
                    <?php foreach($lessons_by_section[0] as $lesson): ?>
                        <?= renderViewerLessonItem($lesson, $course_id, $current_lesson_id, $progress_data) ?>
                    <?php endforeach; ?>
                <?php endif; ?>

                <!-- Sections -->
                <?php foreach($sections as $section): ?>
                    <div class="section-divider"><?= htmlspecialchars($section['title']) ?></div>
                    <div class="section-lessons">
                        <?php if(isset($lessons_by_section[$section['id']])): ?>
                            <?php foreach($lessons_by_section[$section['id']] as $lesson): ?>
                                <?= renderViewerLessonItem($lesson, $course_id, $current_lesson_id, $progress_data) ?>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div style="padding:15px; font-size:0.8rem; color:#94a3b8;">لا توجد دروس حالياً</div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </aside>

    <?php
    /** Helper for Sidebar Item **/
    function renderViewerLessonItem($lesson, $course_id, $current_id, $progress) {
        $isActive = $lesson['id'] == $current_id;
        $status = $progress[$lesson['id']] ?? 'not_started';
        $iconClass = 'far fa-circle';
        if ($status === 'completed') $iconClass = 'fas fa-check-circle';
        elseif ($status === 'in_progress') $iconClass = 'fas fa-play-circle';
        
        $mediaIcons = ['video'=>'fa-video', 'audio'=>'fa-volume-up', 'pdf'=>'fa-file-pdf', 'text'=>'fa-align-left'];
        $mIcon = $mediaIcons[$lesson['media_type'] ?? 'text'] ?? 'fa-book';

        ob_start(); ?>
        <a href="?id=<?= $course_id ?>&lesson=<?= $lesson['id'] ?>" 
           class="lesson-item <?= $isActive ? 'active' : '' ?> <?= $status ?>">
            <div style="margin-left: 12px; font-size: 0.9rem; opacity: 0.6;">
                <i class="fas <?= $mIcon ?>"></i>
            </div>
            <div class="lesson-info">
                <h4 class="lesson-title"><?= htmlspecialchars($lesson['title']) ?></h4>
                <span class="lesson-meta">
                    <i class="far fa-clock"></i> <?= $lesson['duration'] ? gmdate("i:s", $lesson['duration']) : '---' ?>
                </span>
            </div>
            <i class="<?= $iconClass ?> status-icon"></i>
        </a>
        <?php return ob_get_clean();
    }
    ?>

    <!-- Main Content Area -->
    <main class="viewer-main">
        <div class="container-fluid">
            <?php if($current_lesson): ?>
                <div class="video-wrapper glass-panel">
                    <div class="video-container">
                        <?= MediaService::renderPlayer(
                            $current_lesson['media_provider'] ?? 'local', 
                            $current_lesson['media_url'] ?? '', 
                            $current_lesson['media_type'] ?? 'video'
                        ) ?>
                    </div>
                </div>

                <div class="lesson-content-panel glass-panel">
                    <div class="panel-header">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 20px;">
                            <h1 class="current-lesson-title"><?= htmlspecialchars($current_lesson['title']) ?></h1>
                            <button id="markCompleteBtn" class="btn-primary-glow" style="padding: 10px 20px;">
                                <i class="fas fa-check"></i> تحديد كمكتمل
                            </button>
                        </div>
                        <div class="lesson-actions">
                            <button class="btn-action active"><i class="fas fa-align-right"></i> الوصف</button>
                            <button class="btn-action"><i class="fas fa-paperclip"></i> المرفقات</button>
                            <button class="btn-action"><i class="fas fa-comment-alt"></i> المناقشة</button>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="description-text">
                            <?= nl2br(htmlspecialchars($current_lesson['content'] ?? 'لا يوجد وصف لهذا الدرس.')) ?>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="select-lesson-prompt">
                    <img src="../assets/images/select-lesson.svg" alt="Select Lesson" onerror="this.style.display='none'">
                    <h3>اختر درساً للبدء</h3>
                    <p>استعرض قائمة الدروس من القائمة الجانبية وابدأ رحلة التعلم.</p>
                </div>
            <?php endif; ?>
        </div>
    </main>
</div>

<style>
/* Modern Viewer Styles */
:root {
    --sidebar-width: 380px;
    --header-height: 80px;
    --accent-glow: rgba(16, 185, 129, 0.4);
}

body {
    overflow: hidden; /* Prevent double scrollbars */
}

.course-viewer-modern {
    display: flex;
    height: calc(100vh - var(--header-height));
    background-color: #f3f4f6;
    overflow: hidden;
}

/* Sidebar Styling */
.viewer-sidebar {
    width: var(--sidebar-width);
    background: #fff;
    border-left: 1px solid #e5e7eb;
    display: flex;
    flex-direction: column;
    z-index: 10;
    box-shadow: -4px 0 20px rgba(0,0,0,0.05);
}

.sidebar-header {
    padding: 25px;
    background: linear-gradient(135deg, #0f172a, #1e293b);
    color: #fff;
}

.course-subtitle {
    font-size: 0.85rem;
    opacity: 0.7;
    margin-bottom: 5px;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.course-title {
    margin: 0 0 15px 0;
    font-size: 1.25rem;
    line-height: 1.4;
}

.progress-container {
    height: 6px;
    background: rgba(255,255,255,0.1);
    border-radius: 3px;
    margin-bottom: 8px;
    overflow: hidden;
}

.progress-bar {
    height: 100%;
    background: #10b981;
    border-radius: 3px;
    transition: width 0.3s ease;
}

.progress-text {
    font-size: 0.8rem;
    opacity: 0.8;
    display: block;
    text-align: left;
}

.section-divider {
    padding: 12px 25px;
    background: #f1f5f9;
    font-size: 0.75rem;
    font-weight: 700;
    color: #475569;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-bottom: 1px solid #e2e8f0;
}

.lesson-item.completed .status-icon {
    color: #10b981;
}

.lesson-item.in_progress .status-icon {
    color: #3b82f6;
}


.lessons-list {
    flex: 1;
    overflow-y: auto;
    padding: 10px 0;
}

.lesson-item {
    display: flex;
    align-items: center;
    padding: 18px 25px;
    text-decoration: none;
    color: #334155;
    border-right: 3px solid transparent;
    transition: all 0.2s ease;
}

.lesson-item:hover {
    background: #f8fafc;
    color: #0f172a;
}

.lesson-item.active {
    background: #f0fdf4;
    border-right-color: #10b981;
    color: #10b981;
}

.lesson-number {
    font-family: 'Tajawal', sans-serif;
    font-weight: 700;
    font-size: 0.9rem;
    color: #94a3b8;
    margin-left: 15px;
    min-width: 25px;
}

.lesson-info {
    flex: 1;
}

.lesson-title {
    margin: 0 0 4px 0;
    font-size: 0.95rem;
    font-weight: 600;
}

.lesson-meta {
    font-size: 0.75rem;
    color: #94a3b8;
    display: flex;
    align-items: center;
    gap: 5px;
}

.status-icon {
    margin-right: 10px;
    font-size: 1rem;
    opacity: 0.5;
}

.lesson-item.active .status-icon {
    opacity: 1;
    color: #10b981;
}

/* Main Content Styling */
.viewer-main {
    flex: 1;
    padding: 30px;
    overflow-y: auto;
    position: relative;
    background-image: radial-gradient(#e5e7eb 1px, transparent 1px);
    background-size: 24px 24px;
}

.glass-panel {
    background: rgba(255, 255, 255, 0.9);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.5);
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
    overflow: hidden;
    margin-bottom: 25px;
}

.video-wrapper {
    padding: 0;
}

.video-container {
    padding-bottom: 56.25%;
    position: relative;
    height: 0;
    background: #000;
}

.video-container iframe {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
}

.lesson-content-panel {
    padding: 30px;
}

.panel-header {
    border-bottom: 1px solid #e5e7eb;
    padding-bottom: 20px;
    margin-bottom: 20px;
}

.current-lesson-title {
    font-size: 1.8rem;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 20px;
}

.lesson-actions {
    display: flex;
    gap: 15px;
}

.btn-action {
    background: none;
    border: none;
    padding: 8px 16px;
    border-radius: 8px;
    color: #64748b;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    gap: 8px;
}

.btn-action:hover {
    background: #f1f5f9;
    color: #334155;
}

.btn-action.active {
    background: #e0e7ff;
    color: #4f46e5;
}

.description-text {
    line-height: 1.8;
    color: #475569;
    font-size: 1.05rem;
}

/* Scrollbar */
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: #f1f1f1;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 3px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

/* Responsive */
@media (max-width: 1024px) {
    .course-viewer-modern {
        flex-direction: column-reverse;
        height: auto;
        overflow-y: auto;
    }
    
    .viewer-sidebar {
        width: 100%;
        height: 500px;
        border-left: none;
        border-top: 1px solid #e5e7eb;
    }
    
    .viewer-main {
        padding: 20px;
    }
}
</style>

<script>
document.getElementById('markCompleteBtn')?.addEventListener('click', function() {
    const lessonId = <?= $current_lesson_id ?>;
    const courseId = <?= $course_id ?>;
    
    this.disabled = true;
    this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري الحفظ...';

    fetch('../api/update_progress.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `lesson_id=${lessonId}&course_id=${courseId}&status=completed`
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('حدث خطأ أثناء حفظ التقدم');
            this.disabled = false;
            this.innerHTML = '<i class="fas fa-check"></i> تحديد كمكتمل';
        }
    });
});

// Optional: Simple Heartbeat to mark "in_progress"
setTimeout(() => {
    fetch('../api/update_progress.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `lesson_id=<?= $current_lesson_id ?>&course_id=<?= $course_id ?>&status=in_progress`
    });
}, 5000); // After 5 seconds marks as in progress
</script>

<?php include '../includes/components/footer.php'; ?>
