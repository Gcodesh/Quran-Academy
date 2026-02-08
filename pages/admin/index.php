<?php
require_once __DIR__ . '/../../pages/dashboard/layout.php';
require_once __DIR__ . '/../../includes/config/database.php';
require_once __DIR__ . '/../../includes/classes/Database.php';

// CRITICAL: Only allow admin access
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../dashboard/index.php');
    exit;
}

$page_title = 'لوحة التميز - الإدارة العليا';

render_dashboard_layout(function() {
    $db = new Database();
    $conn = $db->getConnection();

    // Fetch Analytics Data
    $total_users = $conn->query("SELECT COUNT(*) FROM users")->fetchColumn();
    $total_students = $conn->query("SELECT COUNT(*) FROM users WHERE role = 'student'")->fetchColumn();
    $total_teachers = $conn->query("SELECT COUNT(*) FROM users WHERE role = 'teacher'")->fetchColumn();
    $total_courses = $conn->query("SELECT COUNT(*) FROM courses")->fetchColumn();
    $published_courses = $conn->query("SELECT COUNT(*) FROM courses WHERE status = 'published'")->fetchColumn();
    $pending_courses = $conn->query("SELECT COUNT(*) FROM courses WHERE status = 'pending'")->fetchColumn();
    $total_revenue = $conn->query("SELECT COALESCE(SUM(amount), 0) FROM payments WHERE status = 'completed'")->fetchColumn();
    
    // Recent Activities
    $recent_users = $conn->query("SELECT * FROM users ORDER BY created_at DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
    $pending_list = $conn->query("SELECT c.*, u.full_name as teacher_name FROM courses c JOIN users u ON c.teacher_id = u.id WHERE c.status = 'pending' ORDER BY c.created_at DESC LIMIT 3")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="admin-dashboard premium-experience">
    <!-- Premium Header Area -->
    <div class="dashboard-header-flex">
        <div>
            <h1 style="font-size: 2.2rem; font-weight: 900; color: var(--p-slate-900); margin: 0; letter-spacing: -0.5px;">المركز الرئيسي للإدارة ✨</h1>
            <p style="color: var(--p-slate-500); margin-top: 10px; font-size: 1.1rem;">مرحباً بك في نظام إدارة منصة التميز المطور</p>
        </div>
        <div style="display: flex; gap: 15px;">
            <div class="glass-card" style="padding: 12px 25px; display: flex; align-items: center; gap: 12px; color: var(--p-emerald-700); font-weight: 700; border-radius: 20px;">
                <i class="fas fa-shield-alt"></i> اتصال آمن
            </div>
        </div>
    </div>

    <!-- Analytics Cards -->
    <div class="stats-grid" style="margin-top: 40px;">
        <div class="glass-card stat-card" style="border-top: 4px solid var(--p-emerald-500);">
            <div class="stat-label">إجمالي المستخدمين</div>
            <div class="stat-value"><?= number_format($total_users) ?></div>
            <div style="margin-top: 15px; font-size: 0.85rem; color: var(--p-emerald-600); font-weight: 700;">
                <span style="margin-left: 10px;"><?= $total_students ?> طالب</span>
                <span><?= $total_teachers ?> معلم</span>
            </div>
            <i class="fas fa-users" style="position: absolute; left: 25px; top: 25px; font-size: 1.8rem; color: var(--p-emerald-200); opacity: 0.4;"></i>
        </div>

        <div class="glass-card stat-card" style="border-top: 4px solid var(--p-gold-500);">
            <div class="stat-label">المداخيل الإجمالية</div>
            <div class="stat-value" style="color: var(--p-gold-600);"><?= number_format($total_revenue) ?> <small>ر.س</small></div>
            <div style="margin-top: 15px; font-size: 0.85rem; color: var(--p-gold-600); font-weight: 700;">
                <i class="fas fa-chart-line"></i> نمو مستمر
            </div>
            <i class="fas fa-coins" style="position: absolute; left: 25px; top: 25px; font-size: 1.8rem; color: var(--p-gold-200); opacity: 0.4;"></i>
        </div>

        <div class="glass-card stat-card" style="border-top: 4px solid #3b82f6;">
            <div class="stat-label">إجمالي الدورات</div>
            <div class="stat-value" style="color: #2563eb;"><?= number_format($total_courses) ?></div>
            <div style="margin-top: 15px; font-size: 0.85rem; color: #3b82f6; font-weight: 700;">
                <i class="fas fa-check-double"></i> <?= $published_courses ?> منشورة
            </div>
            <i class="fas fa-graduation-cap" style="position: absolute; left: 25px; top: 25px; font-size: 1.8rem; color: #bfdbfe; opacity: 0.4;"></i>
        </div>

        <div class="glass-card stat-card" style="border-top: 4px solid #f43f5e;">
            <div class="stat-label">طلبات المراجعة</div>
            <div class="stat-value" style="color: #e11d48;"><?= number_format($pending_courses) ?></div>
            <div style="margin-top: 15px; font-size: 0.85rem; color: #f43f5e; font-weight: 700;">
                <i class="fas fa-bell"></i> تتطلب اهتمامك
            </div>
            <i class="fas fa-clock" style="position: absolute; left: 25px; top: 25px; font-size: 1.8rem; color: #fecdd3; opacity: 0.4;"></i>
        </div>
    </div>

    <!-- Main Content Panels -->
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px; margin-top: 40px;">
        
        <!-- Pending Approvals & Activity -->
        <div style="display: flex; flex-direction: column; gap: 30px;">
            <div class="glass-card" style="padding: 35px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
                    <h3 style="margin: 0; font-size: 1.4rem; color: var(--p-slate-800); font-weight: 800;">طلبات بانتظار الموافقة</h3>
                    <a href="courses.php?status=pending" class="view-all-premium">عرض الكل <i class="fas fa-arrow-left"></i></a>
                </div>
                
                <?php if (empty($pending_list)): ?>
                    <div style="text-align: center; padding: 40px; color: var(--p-slate-400);">
                        لا توجد طلبات معلقة حالياً
                    </div>
                <?php else: ?>
                    <div style="display: flex; flex-direction: column; gap: 15px;">
                        <?php foreach($pending_list as $item): ?>
                            <div style="padding: 20px; background: white; border: 1px solid var(--p-slate-100); border-radius: 20px; display: flex; justify-content: space-between; align-items: center; transition: 0.3s;" class="p-hover-row">
                                <div style="display: flex; align-items: center; gap: 15px;">
                                    <div style="width: 45px; height: 45px; background: #fff7ed; color: #f59e0b; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-file-signature"></i>
                                    </div>
                                    <div>
                                        <strong style="display: block; color: var(--p-slate-800);"><?= htmlspecialchars($item['title']) ?></strong>
                                        <span style="font-size: 0.85rem; color: var(--p-slate-400);">بواسطة: <?= htmlspecialchars($item['teacher_name']) ?></span>
                                    </div>
                                </div>
                                <div style="display: flex; gap: 10px;">
                                    <a href="course-review.php?id=<?= $item['id'] ?>" style="padding: 8px 18px; background: var(--p-emerald-500); color: white; border-radius: 10px; text-decoration: none; font-size: 0.85rem; font-weight: 700;">مراجعة</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="glass-card" style="padding: 35px;">
                <h3 style="margin: 0 0 30px; font-size: 1.4rem; color: var(--p-slate-800); font-weight: 800;">أحدث المسجلين</h3>
                <div style="overflow-x: auto;">
                    <table class="modern-table">
                        <tbody>
                            <?php foreach($recent_users as $user): ?>
                            <tr>
                                <td style="width: 60px;">
                                    <div class="p-avatar" style="<?= $user['role'] == 'teacher' ? 'background: linear-gradient(135deg, #6366f1, #4338ca);' : '' ?>">
                                        <?= mb_substr($user['full_name'], 0, 1, "UTF-8") ?>
                                    </div>
                                </td>
                                <td>
                                    <strong style="display: block; color: var(--p-slate-800);"><?= htmlspecialchars($user['full_name']) ?></strong>
                                    <span style="font-size: 0.8rem; color: var(--p-slate-400);"><?= htmlspecialchars($user['email']) ?></span>
                                </td>
                                <td>
                                    <span class="p-badge" style="background: <?= $user['role'] == 'teacher' ? '#eef2ff' : '#f0fdf4' ?>; color: <?= $user['role'] == 'teacher' ? '#4338ca' : '#166534' ?>;">
                                        <?= $user['role'] == 'teacher' ? 'معلم' : 'طالب' ?>
                                    </span>
                                </td>
                                <td style="text-align: left;">
                                    <a href="user-details.php?id=<?= $user['id'] ?>" style="color: var(--p-slate-300);"><i class="fas fa-chevron-left"></i></a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- System Controls -->
        <div style="display: flex; flex-direction: column; gap: 30px;">
            <div class="glass-card" style="padding: 30px; background: var(--p-slate-900); color: white; border: none;">
                <h3 style="margin-top: 0; margin-bottom: 25px; font-size: 1.2rem; font-weight: 800;">تحكم سريع</h3>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <a href="users.php" class="quick-admin-btn"><i class="fas fa-users-cog"></i> <span>المستخدمين</span></a>
                    <a href="courses.php" class="quick-admin-btn"><i class="fas fa-graduation-cap"></i> <span>الدورات</span></a>
                    <a href="finance.php" class="quick-admin-btn"><i class="fas fa-money-check-alt"></i> <span>المالية</span></a>
                    <a href="settings.php" class="quick-admin-btn"><i class="fas fa-cogs"></i> <span>الإعدادات</span></a>
                </div>
            </div>

            <div class="glass-card" style="padding: 30px;">
                <h3 style="margin-top: 0; margin-bottom: 20px; font-size: 1.2rem; font-weight: 800; color: var(--p-slate-800);">حالة الخادم</h3>
                <div style="display: flex; flex-direction: column; gap: 15px;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="color: var(--p-slate-500); font-weight: 600;">قاعدة البيانات</span>
                        <span class="p-badge p-badge-success">متصل</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="color: var(--p-slate-500); font-weight: 600;">نظام التخزين</span>
                        <span class="p-badge p-badge-success">نشط</span>
                    </div>
                    <div style="padding-top: 15px; border-top: 1px solid var(--p-slate-50);">
                        <span style="display: block; font-size: 0.8rem; color: var(--p-slate-400); text-align: center;">آخر تحديث: <?= date('H:i:s') ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.quick-admin-btn {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 12px;
    padding: 20px;
    background: rgba(255,255,255,0.05);
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 20px;
    color: white;
    text-decoration: none;
    transition: 0.3s;
}

.quick-admin-btn:hover {
    background: var(--p-emerald-600);
    border-color: var(--p-emerald-400);
    transform: translateY(-5px);
}

.quick-admin-btn i { font-size: 1.4rem; color: var(--p-emerald-400); }
.quick-admin-btn:hover i { color: white; }
.quick-admin-btn span { font-size: 0.85rem; font-weight: 700; }

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

.p-hover-row:hover {
    border-color: var(--p-emerald-500) !important;
    box-shadow: var(--p-shadow-md);
    transform: translateX(-5px);
}
</style>

<?php
});
?>
