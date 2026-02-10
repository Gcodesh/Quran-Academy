<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../includes/config/root.php';
require_once path('includes/config/database.php');
require_once path('includes/classes/Database.php');

// Redirect if not logged in or not a student
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'student') {
    header("Location: " . url('pages/login.php'));
    exit();
}

require_once __DIR__ . '/layout.php';
require_once path('src/Services/GamificationService.php');

$page_title = 'لوحة التميز';

render_dashboard_layout(function() {
    $user_id = $_SESSION['user_id'];
    $user_name = $_SESSION['user_name'] ?? 'الطالب';
    
    $db = new Database();
    $conn = $db->getConnection();
    
    // Fetch enrollments
    $sql = "SELECT c.*, e.enrollment_date, e.progress_percentage, u.full_name as teacher_name 
            FROM enrollments e 
            JOIN courses c ON e.course_id = c.id 
            LEFT JOIN users u ON c.teacher_id = u.id
            WHERE e.user_id = ?
            ORDER BY e.enrollment_date DESC";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$user_id]);
    $my_courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Gamification Integration
    // Gamification Integration
    $user_mastery = null;
    try {
        require_once __DIR__ . '/../../src/Services/GamificationService.php';
        $gamification = new \App\Services\GamificationService(); // No args
        $user_mastery = $gamification->getUserStats($user_id);
    } catch (\Throwable $e) {
        // Log error silently and use defaults
        error_log("Dashboard Gamification Error: " . $e->getMessage());
    }

    // Safe Defauts
    $rank_label = $user_mastery['rank'] ?? 'طالب مبتدئ'; // Fixed: DB column is 'rank'
    $points = $user_mastery['points'] ?? 0;
    $next_rank_label = $user_mastery['next_rank']['label'] ?? 'متقن';
    $progress = $user_mastery['progress_percent'] ?? 0;

    // Certificates Integration
    $cert_stmt = $conn->prepare("SELECT c.*, crs.title as course_title 
                               FROM certificates c 
                               JOIN courses crs ON c.course_id = crs.id 
                               WHERE c.user_id = ? 
                               ORDER BY c.issue_date DESC LIMIT 3");
    $cert_stmt->execute([$user_id]);
    $my_certificates = $cert_stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get notifications
    $notif_stmt = $conn->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 4");
    $notif_stmt->execute([$user_id]);
    $notifications = $notif_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="student-dashboard premium-experience">
    <!-- Premium Header Section -->
    <div class="glass-card premium-hero" style="padding: 40px; margin-bottom: 30px; background: linear-gradient(135deg, rgba(19, 78, 74, 0.9), rgba(15, 23, 42, 0.95)); color: white; border: none; position: relative; overflow: hidden;">
        <div style="position: absolute; right: -50px; top: -50px; width: 200px; height: 200px; background: var(--p-emerald-500); opacity: 0.1; filter: blur(60px); border-radius: 50%;"></div>
        
        <div style="display: flex; justify-content: space-between; align-items: center; position: relative; z-index: 1;">
            <div style="display: flex; align-items: center; gap: 25px;">
                <div class="rank-shield pulse-premium">
                    <i class="fas fa-crown"></i>
                </div>
                <div>
                    <h1 style="font-size: 2.2rem; font-weight: 800; margin: 0; color: white;">أهلاً بك، <?= htmlspecialchars($user_name) ?> ✨</h1>
                    <p style="color: rgba(255,255,255,0.7); font-size: 1.1rem; margin-top: 8px;">
                        رتبتك الحالية: <strong style="color: var(--p-gold-500);"><?= htmlspecialchars($rank_label) ?></strong> | 
                        النقاط المكتسبة: <strong style="color: var(--p-emerald-500);"><?= number_format($points) ?></strong>
                    </p>
                </div>
            </div>
            
            <div class="next-rank-info" style="text-align: left; background: rgba(255,255,255,0.05); padding: 20px; border-radius: 24px; border: 1px solid rgba(255,255,255,0.1); width: 300px;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 12px; font-size: 0.9rem;">
                    <span>المستوى التالي: <strong><?= htmlspecialchars($next_rank_label) ?></strong></span>
                    <span style="color: var(--p-gold-500);"><?= $progress ?>%</span>
                </div>
                <div class="glow-progress">
                    <div class="glow-progress-fill" style="width: <?= $progress ?>%; background: var(--p-gold-500); box-shadow: 0 0 15px rgba(245, 158, 11, 0.4);"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="stats-grid" style="margin-bottom: 40px;">
        <div class="glass-card stat-card">
            <div class="stat-label">الدورات المسجلة</div>
            <div class="stat-value"><?= count($my_courses) ?></div>
            <i class="fas fa-book-reader" style="position: absolute; left: 25px; bottom: 25px; font-size: 2.5rem; opacity: 0.05;"></i>
        </div>
        <div class="glass-card stat-card">
            <div class="stat-label">نقاط التميز</div>
            <div class="stat-value" style="color: var(--p-emerald-600);"><?= number_format($points) ?></div>
            <i class="fas fa-bolt" style="position: absolute; left: 25px; bottom: 25px; font-size: 2.5rem; opacity: 0.05;"></i>
        </div>
        <div class="glass-card stat-card">
            <div class="stat-label">الشهادات الحالية</div>
            <div class="stat-value" style="color: var(--p-gold-600);"><?= count($my_certificates) ?></div>
            <i class="fas fa-award" style="position: absolute; left: 25px; bottom: 25px; font-size: 2.5rem; opacity: 0.05;"></i>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px;">
        <!-- Courses Panel -->
        <div class="glass-card" style="padding: 35px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
                <h3 style="margin: 0; font-size: 1.4rem; color: var(--p-slate-800); font-weight: 800;">
                    <i class="fas fa-graduation-cap" style="margin-left: 10px; color: var(--p-emerald-500);"></i> 
                    رحلتي التعليمية الحالية
                </h3>
                <a href="../courses.php" class="view-all-premium">استكشاف المزيد <i class="fas fa-plus"></i></a>
            </div>

            <?php if(empty($my_courses)): ?>
                <div style="text-align: center; padding: 60px 20px;">
                    <div style="width: 80px; height: 80px; background: var(--p-slate-50); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; color: var(--p-slate-400);">
                        <i class="fas fa-book-open" style="font-size: 2rem;"></i>
                    </div>
                    <h4 style="color: var(--p-slate-600);">ابدأ رحلتك الأولى اليوم!</h4>
                    <p style="color: var(--p-slate-400); margin-bottom: 25px;">لم تقم بالتسجيل في أي دورة بعد.</p>
                    <a href="../courses.php" style="padding: 12px 30px; background: var(--p-emerald-600); color: white; border-radius: 16px; text-decoration: none; font-weight: 700; box-shadow: var(--p-shadow-md);">تصفح الدورات</a>
                </div>
            <?php else: ?>
                <div class="premium-courses-list" style="display: flex; flex-direction: column; gap: 20px;">
                    <?php foreach($my_courses as $course): ?>
                        <div class="course-row-premium" style="display: flex; align-items: center; gap: 20px; padding: 20px; background: white; border: 1px solid var(--p-slate-100); border-radius: 24px; transition: var(--dash-transition);">
                            <div style="width: 100px; height: 75px; border-radius: 16px; overflow: hidden; flex-shrink: 0;">
                                <img src="<?= htmlspecialchars($course['thumbnail'] ?: '../../assets/images/placeholder.jpg') ?>" style="width:100%; height:100%; object-fit:cover;" onerror="this.src='https://placehold.co/100x75?text=Course'">
                            </div>
                            <div style="flex: 1;">
                                <h4 style="margin: 0 0 5px; color: var(--p-slate-800);"><?= htmlspecialchars($course['title']) ?></h4>
                                <span style="font-size: 0.85rem; color: var(--p-slate-500);"><i class="fas fa-user-tie" style="margin-left:5px;"></i> <?= htmlspecialchars($course['teacher_name']) ?></span>
                                <div style="display: flex; align-items: center; gap: 15px; margin-top: 10px;">
                                    <div class="glow-progress" style="flex: 1; height: 8px;">
                                        <div class="glow-progress-fill" style="width: <?= $course['progress_percentage'] ?>%;"></div>
                                    </div>
                                    <span style="font-size: 0.85rem; font-weight: 800; color: var(--p-emerald-600);"><?= $course['progress_percentage'] ?>%</span>
                                </div>
                            </div>
                            <a href="../course-lessons.php?id=<?= $course['id'] ?>" style="width: 45px; height: 45px; background: var(--p-emerald-50); color: var(--p-emerald-600); border-radius: 14px; display: flex; align-items: center; justify-content: center; transition: 0.3s;" title="متابعة">
                                <i class="fas fa-play"></i>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Sidebar Panel -->
        <div style="display: flex; flex-direction: column; gap: 30px;">
            <!-- Mastery Rank Panel -->
            <div class="glass-card" style="padding: 30px; background: linear-gradient(135deg, white, var(--p-emerald-50)); border: 1px solid var(--p-emerald-100); text-align: center;">
                <h3 style="font-size: 1.2rem; margin-bottom: 20px; color: var(--p-emerald-900); font-weight: 800;">رتبة الإتقان</h3>
                <div class="p-rank-badge pulse-premium">
                    <i class="fas fa-medal"></i>
                </div>
                <h2 style="margin: 15px 0 5px; color: var(--p-emerald-900);"><?= htmlspecialchars($rank_label) ?></h2>
                <div class="p-badge p-badge-success">نشط حالياً</div>
            </div>

            <!-- Notifications Panel -->
            <div class="glass-card" style="padding: 30px;">
                <h3 style="font-size: 1.2rem; margin-bottom: 25px; color: var(--p-slate-800); font-weight: 800;">أحدث الإشعارات</h3>
                <div class="p-notifications-list" style="display: flex; flex-direction: column; gap: 15px;">
                    <?php if(empty($notifications)): ?>
                        <p style="text-align: center; color: var(--p-slate-400);">لا توجد إشعارات جديدة</p>
                    <?php else: ?>
                        <?php foreach($notifications as $notif): ?>
                            <div style="display: flex; gap: 15px; padding-bottom: 15px; border-bottom: 1px solid var(--p-slate-50);">
                                <div style="width: 35px; height: 35px; background: var(--p-slate-50); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: var(--p-emerald-600); flex-shrink: 0;">
                                    <i class="fas fa-bell"></i>
                                </div>
                                <div>
                                    <strong style="display: block; font-size: 0.9rem; color: var(--p-slate-800);"><?= htmlspecialchars($notif['title']) ?></strong>
                                    <span style="font-size: 0.75rem; color: var(--p-slate-500);"><?= date('H:i - Y/m/d', strtotime($notif['created_at'])) ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.rank-shield {
    width: 70px;
    height: 70px;
    background: linear-gradient(135deg, var(--p-gold-500), var(--p-gold-600));
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    color: white;
    box-shadow: 0 10px 25px rgba(217, 119, 6, 0.4);
}

.p-rank-badge {
    width: 90px;
    height: 90px;
    background: white;
    border: 6px solid var(--p-gold-500);
    border-radius: 50%;
    margin: 0 auto;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2.5rem;
    color: var(--p-gold-600);
    box-shadow: var(--p-shadow-lg);
}

.pulse-premium {
    animation: pPulseAnimation 2s infinite;
}

@keyframes pPulseAnimation {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}

.view-all-premium {
    font-size: 0.9rem;
    font-weight: 700;
    color: var(--p-emerald-600);
    text-decoration: none;
    transition: 0.3s;
}

.view-all-premium:hover {
    color: var(--p-emerald-700);
    transform: translateX(-5px);
}

.course-row-premium:hover {
    border-color: var(--p-emerald-500);
    box-shadow: var(--p-shadow-md);
    transform: translateX(-5px);
}

.course-row-premium:hover a {
    background: var(--p-emerald-600);
    color: white;
}
</style>

<?php
});
?>
