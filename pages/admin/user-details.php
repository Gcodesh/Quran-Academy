<?php
require_once __DIR__ . '/admin_middleware.php';
require_once '../../pages/dashboard/layout.php';
require_once '../../includes/config/database.php';
require_once '../../includes/classes/Database.php';

$page_title = 'تفاصيل المستخدم';

render_dashboard_layout(function() {
    $db = new Database();
    $conn = $db->getConnection();
    
    $user_id = $_GET['id'] ?? null;
    if (!$user_id) {
        header('Location: users.php');
        exit;
    }

    // Fetch User Data
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo '<div class="glass-card" style="padding:40px; text-align:center;"><h2>المستخدم غير موجود</h2><a href="users.php">العودة للقائمة</a></div>';
        return;
    }

    // Fetch Enrollments (if student)
    $enrollments = [];
    if ($user['role'] === 'student') {
        $stmt = $conn->prepare("SELECT e.*, c.title as course_title, c.image as course_image FROM enrollments e JOIN courses c ON e.course_id = c.id WHERE e.user_id = ?");
        $stmt->execute([$user_id]);
        $enrollments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Fetch Courses (if teacher)
    $teacher_courses = [];
    if ($user['role'] === 'teacher') {
        $stmt = $conn->prepare("SELECT * FROM courses WHERE teacher_id = ?");
        $stmt->execute([$user_id]);
        $teacher_courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
?>

<div class="admin-user-details premium-experience">
    <!-- Header -->
    <div class="dashboard-header-flex">
        <div>
            <h1 style="font-size: 2.2rem; font-weight: 900; color: var(--p-slate-900); margin: 0;">الملف التعريفي للمستخدم 🏛️</h1>
            <p style="color: var(--p-slate-500); margin-top: 10px;">عرض شامل للبيانات، الإحصائيات، والأنشطة</p>
        </div>
        <div style="display:flex; gap:15px;">
            <a href="users.php" class="p-btn-secondary" style="text-decoration:none; padding:12px 25px; border-radius:15px; background:white; border:1px solid var(--p-slate-100); color:var(--p-slate-600); font-weight:700; display:flex; align-items:center; gap:8px;">
                <i class="fas fa-arrow-right"></i> القائمة
            </a>
            <a href="edit-user.php?id=<?= $user['id'] ?>" style="text-decoration:none; padding:12px 30px; border-radius:15px; background: var(--p-slate-900); color:white; border:none; font-weight:800; display:flex; align-items:center; gap:10px;">
                <i class="fas fa-user-edit"></i> تعديل المستخدم
            </a>
        </div>
    </div>

    <div class="p-details-layout" style="display:grid; grid-template-columns: 400px 1fr; gap:30px; margin-top:40px;">
        <!-- Left: Summary Card -->
        <div class="p-sidebar">
            <div class="glass-card" style="padding:40px; text-align:center;">
                <div class="p-avatar-xl" style="width:140px; height:140px; border-radius:50px; background:linear-gradient(135deg, var(--p-emerald-500), var(--p-emerald-700)); color:white; display:flex; align-items:center; justify-content:center; font-size:4.5rem; margin:0 auto 30px; box-shadow:var(--p-shadow-lg);">
                    <?= mb_substr($user['name'], 0, 1, 'UTF-8') ?>
                </div>
                <h2 style="color:var(--p-slate-900); margin:0; font-size:1.8rem; font-weight:900;"><?= htmlspecialchars($user['name']) ?></h2>
                <div style="margin:15px auto; display:flex; justify-content:center; gap:10px;">
                    <span class="p-badge" style="background:rgba(20,184,166,0.1); color:var(--p-emerald-600);"><?= strtoupper($user['role']) ?></span>
                    <span class="p-badge <?= $user['status'] === 'active' ? 'p-badge-success' : 'p-badge-danger' ?>"><?= $user['status'] ?></span>
                </div>
                
                <div class="p-user-stats-compact" style="display:grid; grid-template-columns:1fr 1fr; gap:15px; margin-top:35px;">
                    <div style="background:var(--p-slate-50); padding:20px; border-radius:24px;">
                        <div style="color:var(--p-slate-400); font-size:0.8rem;">انضم منذ</div>
                        <strong style="color:var(--p-slate-800); font-size:1.1rem;"><?= date('M Y', strtotime($user['created_at'])) ?></strong>
                    </div>
                    <div style="background:var(--p-slate-50); padding:20px; border-radius:24px;">
                        <div style="color:var(--p-slate-400); font-size:0.8rem;"><?= $user['role'] === 'student' ? 'الدورات' : 'تدريس' ?></div>
                        <strong style="color:var(--p-slate-800); font-size:1.1rem;"><?= $user['role'] === 'student' ? count($enrollments) : count($teacher_courses) ?></strong>
                    </div>
                </div>

                <div style="text-align:right; margin-top:40px; border-top:1px solid var(--p-slate-50); padding-top:30px;">
                    <h5 style="color:var(--p-slate-400); margin-bottom:15px;">معلومات التواصل</h5>
                    <div style="margin-bottom:15px; line-height:1.6;">
                        <i class="fas fa-envelope" style="color:var(--p-emerald-500); width:25px;"></i>
                        <span style="color:var(--p-slate-700);"><?= htmlspecialchars($user['email']) ?></span>
                    </div>
                    <?php if(!empty($user['phone'])): ?>
                    <div style="margin-bottom:15px; line-height:1.6;">
                        <i class="fas fa-phone" style="color:var(--p-emerald-500); width:25px;"></i>
                        <span style="color:var(--p-slate-700);"><?= htmlspecialchars($user['phone']) ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if(!empty($user['country'])): ?>
                    <div style="line-height:1.6;">
                        <i class="fas fa-map-marker-alt" style="color:var(--p-emerald-500); width:25px;"></i>
                        <span style="color:var(--p-slate-700);"><?= htmlspecialchars($user['country']) . ' - ' . htmlspecialchars($user['city']) ?></span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Right: Activity & Detailed Info -->
        <div class="p-content">
            <!-- Activity Tabs -->
            <div class="glass-card" style="padding:40px; min-height:600px;">
                <h3 style="margin-bottom:30px; font-size:1.4rem; color:var(--p-slate-900); font-weight:800;">
                    <?php 
                        if ($user['role'] === 'student') echo '<i class="fas fa-book-reader"></i> الدورات الملتحق بها';
                        elseif ($user['role'] === 'teacher') echo '<i class="fas fa-chalkboard"></i> الدورات التي يقدمها';
                        else echo '<i class="fas fa-history"></i> آخر الأنشطة';
                    ?>
                </h3>

                <?php if ($user['role'] === 'student'): ?>
                    <div class="p-activity-list" style="display:flex; flex-direction:column; gap:20px;">
                        <?php if(empty($enrollments)): ?>
                            <p style="color:var(--p-slate-400); text-align:center; padding:50px;">لا يوجد اشتراكات نشطة حالياً.</p>
                        <?php else: ?>
                            <?php foreach($enrollments as $enroll): ?>
                                <div class="p-course-item" style="display:flex; gap:20px; padding:20px; background:var(--p-slate-50); border-radius:24px; align-items:center;">
                                    <img src="<?= $enroll['course_image'] ?: 'https://placehold.co/100x70?text=Course' ?>" style="width:100px; height:70px; border-radius:12px; object-fit:cover;">
                                    <div style="flex:1;">
                                        <h4 style="margin:0; color:var(--p-slate-900);"><?= htmlspecialchars($enroll['course_title']) ?></h4>
                                        <div style="font-size:0.85rem; color:var(--p-slate-400); margin-top:5px;">تاريخ الاشتراك: <?= date('Y/m/d', strtotime($enroll['enrolled_at'])) ?></div>
                                    </div>
                                    <div class="p-badge p-badge-success" style="padding:10px 20px;">نـشـط</div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                <?php elseif ($user['role'] === 'teacher'): ?>
                    <div class="p-activity-list" style="display:flex; flex-direction:column; gap:20px;">
                        <?php if(empty($teacher_courses)): ?>
                            <p style="color:var(--p-slate-400); text-align:center; padding:50px;">لم يقم المعلم بإنشاء أي دورات بعد.</p>
                        <?php else: ?>
                            <?php foreach($teacher_courses as $course): ?>
                                <div class="p-course-item" style="display:flex; gap:20px; padding:20px; background:white; border:1px solid var(--p-slate-50); border-radius:24px; align-items:center;">
                                    <img src="<?= $course['image'] ?: 'https://placehold.co/100x70?text=Course' ?>" style="width:100px; height:70px; border-radius:12px; object-fit:cover;">
                                    <div style="flex:1;">
                                        <h4 style="margin:0; color:var(--p-slate-900);"><?= htmlspecialchars($course['title']) ?></h4>
                                        <div style="font-size:0.85rem; color:var(--p-slate-400); margin-top:5px;">الحالة: <?= $course['status'] ?></div>
                                    </div>
                                    <a href="course-review.php?id=<?= $course['id'] ?>" style="padding:10px 20px; background:var(--p-slate-50); border-radius:15px; text-decoration:none; color:var(--p-slate-700); font-size:0.9rem; font-weight:700;">إدارة الدورة</a>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <!-- Admin Placeholder -->
                    <div style="text-align:center; padding:100px 20px;">
                        <div style="font-size:4rem; color:var(--p-slate-100); margin-bottom:20px;"><i class="fas fa-shield-alt"></i></div>
                        <h4 style="color:var(--p-slate-400);">سجل مدراء النظام يظهر فقط في قسم (Audit Logs)</h4>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php
});
?>
