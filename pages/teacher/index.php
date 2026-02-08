<?php
require_once __DIR__ . '/../../pages/dashboard/layout.php';
require_once __DIR__ . '/../../includes/config/database.php';
require_once __DIR__ . '/../../includes/classes/Database.php';

$page_title = 'لوحة التميز للمعلم';

render_dashboard_layout(function() {
    $db = new Database();
    $conn = $db->getConnection();
    $teacher_id = $_SESSION['user_id'] ?? 0;

    // Check Status
    $statusStmt = $conn->prepare("SELECT status FROM users WHERE id = ?");
    $statusStmt->execute([$teacher_id]);
    $userStatus = $statusStmt->fetchColumn();

    if ($userStatus === 'pending') {
        echo "<script>window.location.href = '../teacher-pending.php';</script>";
        return;
    }

    // Stats Queries
    $total_courses = $conn->query("SELECT COUNT(*) FROM courses WHERE teacher_id = $teacher_id")->fetchColumn();
    $total_students = $conn->query("SELECT COUNT(DISTINCT e.user_id) FROM enrollments e JOIN courses c ON e.course_id = c.id WHERE c.teacher_id = $teacher_id")->fetchColumn();
    $pending_courses = $conn->query("SELECT COUNT(*) FROM courses WHERE teacher_id = $teacher_id AND status = 'pending'")->fetchColumn();
    $rating = 4.9; // Mock

    // Recent Courses
    $recent_courses = $conn->query("SELECT * FROM courses WHERE teacher_id = $teacher_id ORDER BY created_at DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="teacher-dashboard premium-experience">
    <!-- Premium Header -->
    <div class="dashboard-header-flex" style="margin-bottom: 40px;">
        <div>
            <h1 style="font-size: 2rem; font-weight: 800; color: var(--p-slate-900); margin: 0;">مرحباً بك، معلمنا القدير 👋</h1>
            <p style="color: var(--p-slate-500); margin-top: 8px; font-size: 1.1rem;">إليك ملخص أداء دوراتك التعليمية اليوم</p>
        </div>
        <div class="glass-card" style="padding: 12px 25px; display: flex; align-items: center; gap: 12px; color: var(--p-emerald-700); font-weight: 700; border-radius: 20px;">
            <i class="far fa-calendar-check" style="font-size: 1.2rem;"></i>
            <?= date('d M Y') ?>
        </div>
    </div>

    <!-- Vibrant Stats Grid -->
    <div class="stats-grid">
        <div class="glass-card stat-card" style="border-right: 5px solid var(--p-emerald-500);">
            <div class="stat-label">إجمالي الطلاب</div>
            <div class="stat-value"><?= number_format($total_students) ?></div>
            <div style="position: absolute; left: 25px; bottom: 20px; width: 45px; height: 45px; background: var(--p-emerald-50); border-radius: 14px; display: flex; align-items: center; justify-content: center; color: var(--p-emerald-600);">
                <i class="fas fa-users"></i>
            </div>
        </div>

        <div class="glass-card stat-card" style="border-right: 5px solid #3b82f6;">
            <div class="stat-label">الدورات النشطة</div>
            <div class="stat-value" style="color: #2563eb;"><?= number_format($total_courses) ?></div>
            <div style="position: absolute; left: 25px; bottom: 20px; width: 45px; height: 45px; background: #eff6ff; border-radius: 14px; display: flex; align-items: center; justify-content: center; color: #3b82f6;">
                <i class="fas fa-graduation-cap"></i>
            </div>
        </div>

        <div class="glass-card stat-card" style="border-right: 5px solid var(--p-gold-500);">
            <div class="stat-label">قيد المراجعة</div>
            <div class="stat-value" style="color: var(--p-gold-600);"><?= number_format($pending_courses) ?></div>
            <div style="position: absolute; left: 25px; bottom: 20px; width: 45px; height: 45px; background: var(--p-gold-50); border-radius: 14px; display: flex; align-items: center; justify-content: center; color: var(--p-gold-600);">
                <i class="fas fa-hourglass-half"></i>
            </div>
        </div>

        <div class="glass-card stat-card" style="border-right: 5px solid #ec4899;">
            <div class="stat-label">متوسط التقييم</div>
            <div class="stat-value" style="color: #db2777;"><?= $rating ?></div>
            <div style="position: absolute; left: 25px; bottom: 20px; width: 45px; height: 45px; background: #fdf2f8; border-radius: 14px; display: flex; align-items: center; justify-content: center; color: #ec4899;">
                <i class="fas fa-star"></i>
            </div>
        </div>
    </div>

    <!-- Recent Courses Premium Table -->
    <div class="glass-card" style="margin-top: 50px; padding: 40px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 35px;">
            <h3 style="margin: 0; font-size: 1.5rem; font-weight: 800; color: var(--p-slate-800);">الدورات المضافة حديثاً</h3>
            <a href="my-courses.php" style="color: var(--p-emerald-600); text-decoration: none; font-weight: 700; font-size: 0.95rem; display: flex; align-items: center; gap: 8px;">
                عرض كافة الدورات <i class="fas fa-arrow-left"></i>
            </a>
        </div>
        
        <div style="overflow-x: auto;">
            <table class="modern-table">
                <thead>
                    <tr style="text-align: right; color: var(--p-slate-400); font-weight: 700; font-size: 0.9rem;">
                        <th style="padding: 0 25px 15px;">عنوان الدورة</th>
                        <th style="padding: 0 25px 15px;">الحالة</th>
                        <th style="padding: 0 25px 15px;">تاريخ الإضافة</th>
                        <th style="padding: 0 25px 15px; text-align: left;">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($recent_courses)): ?>
                        <tr>
                            <td colspan="4" style="text-align: center; padding: 60px; color: var(--p-slate-400); background: white; border-radius: 20px; border: 1px dashed var(--p-slate-200);">
                                <i class="fas fa-inbox" style="font-size: 3rem; margin-bottom: 20px; display: block; opacity: 0.3;"></i>
                                <span style="font-size: 1.1rem;">لا توجد دورات مسجلة حالياً، ابدأ بإضافة دورتك الأولى!</span>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($recent_courses as $course): ?>
                        <tr class="p-table-row">
                            <td style="font-weight: 700; color: var(--p-slate-800);">
                                <div style="display: flex; align-items: center; gap: 15px;">
                                    <div style="width: 45px; height: 45px; border-radius: 12px; background: var(--p-emerald-50); color: var(--p-emerald-600); display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
                                        <i class="fas fa-play-circle"></i>
                                    </div>
                                    <?= htmlspecialchars($course['title']) ?>
                                </div>
                            </td>
                            <td>
                                <?php
                                $st = $course['status'];
                                $badge_class = 'p-badge-warning';
                                $badge_text = 'قيد المراجعة';
                                if ($st === 'published') { $badge_class = 'p-badge-success'; $badge_text = 'منشور'; }
                                elseif ($st === 'rejected') { $badge_class = 'p-badge-danger'; $badge_text = 'مرفوض'; }
                                elseif ($st === 'draft') { $badge_class = ''; $badge_text = 'مسودة'; }
                                ?>
                                <span class="p-badge <?= $badge_class ?>" style="background: <?= $st === 'draft' ? '#f1f5f9' : '' ?>; color: <?= $st === 'draft' ? '#64748b' : '' ?>;">
                                    <?= $badge_text ?>
                                </span>
                            </td>
                            <td style="color: var(--p-slate-500); font-weight: 600;">
                                <i class="far fa-clock" style="margin-left:5px; opacity:0.5;"></i>
                                <?= date('Y/m/d', strtotime($course['created_at'])) ?>
                            </td>
                            <td style="text-align: left;">
                                <div style="display: flex; gap: 10px; justify-content: flex-end;">
                                    <a href="../../course-details.php?id=<?= $course['id'] ?>" target="_blank" class="p-action-btn" title="معاينة">
                                        <i class="fas fa-external-link-alt"></i>
                                    </a>
                                    <a href="edit-course.php?id=<?= $course['id'] ?>" class="p-action-btn" title="تعديل" style="color: #6366f1; background: #eef2ff;">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
.p-action-btn {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--p-slate-50);
    color: var(--p-slate-500);
    border-radius: 12px;
    transition: all 0.3s;
    text-decoration: none;
}

.p-action-btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.05);
}

.p-table-row:hover td {
    background: #fdfdfd !important;
}
</style>

<?php
});
?>
