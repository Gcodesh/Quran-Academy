<?php
require_once __DIR__ . '/../../pages/dashboard/layout.php';
require_once __DIR__ . '/../../includes/config/database.php';
require_once __DIR__ . '/../../includes/classes/Database.php';

// Ensure only teachers access this page
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'teacher') {
    header('Location: ../dashboard/index.php');
    exit;
}

$page_title = 'مركز التواصل';

render_dashboard_layout(function() {
    $user_id = $_SESSION['user_id'];
    $db = new Database();
    $conn = $db->getConnection();
    
    // Fetch notifications for the teacher
    $stmt = $conn->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->execute([$user_id]);
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="teacher-messages premium-experience">
    <!-- Header -->
    <div class="dashboard-header-flex">
        <div>
            <h1 style="font-size: 2.2rem; font-weight: 900; color: var(--p-slate-900); margin: 0;">مركز التواصل ✨</h1>
            <p style="color: var(--p-slate-50); margin-top: 10px; font-size: 1.1rem;">إدارة مراسلاتك مع الطلاب والنظام</p>
        </div>
    </div>

    <!-- Messages Container -->
    <div class="glass-card" style="margin-top: 40px; padding: 40px; min-height: 500px;">
        <?php if(empty($messages)): ?>
            <div style="text-align: center; padding: 100px 20px;">
                <div style="width: 100px; height: 100px; background: var(--p-slate-50); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 30px; color: var(--p-indigo-200);">
                    <i class="fas fa-comments" style="font-size: 3rem;"></i>
                </div>
                <h2 style="color: var(--p-slate-800); font-weight: 800;">لا توجد محادثات نشطة</h2>
                <p style="color: var(--p-slate-400); font-size: 1.1rem; max-width: 400px; margin: 15px auto;">سيظهر هنا سجل تواصلك مع الطلاب وإشعارات النظام الهامة المتعلقة بدوراتك.</p>
                <div style="margin-top: 30px; display: flex; justify-content: center; gap: 15px;">
                    <span class="p-badge" style="background: #e0e7ff; color: #4338ca; padding: 10px 25px; border-radius: 20px;">قريباً: الدردشة المباشرة</span>
                </div>
            </div>
        <?php else: ?>
            <div class="messages-list" style="display: flex; flex-direction: column; gap: 20px;">
                <?php foreach($messages as $msg): ?>
                    <div class="message-item-premium" style="display: flex; gap: 25px; padding: 25px; background: white; border: 1px solid var(--p-slate-100); border-radius: 28px; transition: var(--dash-transition); position: relative; overflow: hidden;">
                        <div style="width: 60px; height: 60px; background: #eef2ff; color: #6366f1; border-radius: 18px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; flex-shrink: 0;">
                            <i class="fas fa-bell"></i>
                        </div>

                        <div style="flex: 1;">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 10px;">
                                <h3 style="margin: 0; font-size: 1.25rem; color: var(--p-slate-900); font-weight: 800;"><?= htmlspecialchars($msg['title']) ?></h3>
                                <span style="font-size: 0.85rem; color: var(--p-slate-400); font-weight: 600;">
                                    <?= date('Y/m/d H:i', strtotime($msg['created_at'])) ?>
                                </span>
                            </div>
                            <p style="margin: 0; color: var(--p-slate-600); line-height: 1.7; font-size: 1.05rem;">
                                <?= nl2br(htmlspecialchars($msg['message'])) ?>
                            </p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
.message-item-premium:hover {
    transform: translateY(-5px);
    border-color: #6366f1;
    box-shadow: 0 10px 30px rgba(99, 102, 241, 0.1);
}
</style>

<?php
});
?>
