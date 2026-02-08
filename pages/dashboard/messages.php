<?php
require_once __DIR__ . '/layout.php';
require_once __DIR__ . '/../../includes/config/database.php';
require_once __DIR__ . '/../../includes/classes/Database.php';
require_once __DIR__ . '/../../includes/auth_middleware.php';

// Ensure only students access this page
if ($_SESSION['user_role'] !== 'student') {
    header('Location: index.php');
    exit;
}

$page_title = 'الرسائل والتنبيهات';

render_dashboard_layout(function() {
    $user_id = $_SESSION['user_id'];
    
    $db = new Database();
    $conn = $db->getConnection();
    
    // Fetch all notifications/messages
    $stmt = $conn->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->execute([$user_id]);
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Mark as read if any are unread (optional, but good for UX)
    $conn->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ? AND is_read = 0")->execute([$user_id]);
?>

<div class="student-messages premium-experience">
    <!-- Header -->
    <div class="dashboard-header-flex">
        <div>
            <h1 style="font-size: 2.2rem; font-weight: 900; color: var(--p-slate-900); margin: 0;">مركز الرسائل 💬</h1>
            <p style="color: var(--p-slate-500); margin-top: 10px; font-size: 1.1rem;">تابع أحدث التنبيهات والرسائل من فريق الإدارة والمعلمين</p>
        </div>
    </div>

    <!-- Messages Container -->
    <div class="glass-card" style="margin-top: 40px; padding: 40px; min-height: 500px;">
        <?php if(empty($messages)): ?>
            <div style="text-align: center; padding: 100px 20px;">
                <div style="width: 100px; height: 100px; background: var(--p-slate-50); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 30px; color: var(--p-slate-300);">
                    <i class="fas fa-comment-slash" style="font-size: 3rem;"></i>
                </div>
                <h2 style="color: var(--p-slate-800); font-weight: 800;">لا توجد رسائل بعد</h2>
                <p style="color: var(--p-slate-400); font-size: 1.1rem; max-width: 400px; margin: 15px auto;">ستتلقى هنا إشعارات حول تسجيلك في الدورات، والشهادات الجديدة، والرسائل الهامة.</p>
            </div>
        <?php else: ?>
            <div class="messages-list" style="display: flex; flex-direction: column; gap: 20px;">
                <?php foreach($messages as $msg): ?>
                    <div class="message-item-premium" style="display: flex; gap: 25px; padding: 25px; background: white; border: 1px solid var(--p-slate-100); border-radius: 28px; transition: var(--dash-transition); position: relative; overflow: hidden;">
                        <?php if($msg['is_read'] == 0): ?>
                            <div style="position: absolute; top: 0; right: 0; width: 6px; height: 100%; background: var(--p-emerald-500);"></div>
                        <?php endif; ?>

                        <div style="width: 60px; height: 60px; background: var(--p-emerald-50); color: var(--p-emerald-600); border-radius: 18px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; flex-shrink: 0;">
                            <?php 
                                $icon = 'bell';
                                if($msg['type'] == 'system') $icon = 'info-circle';
                                if($msg['type'] == 'enrollment') $icon = 'university';
                                if($msg['type'] == 'certificate') $icon = 'award';
                            ?>
                            <i class="fas fa-<?= $icon ?>"></i>
                        </div>

                        <div style="flex: 1;">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 10px;">
                                <h3 style="margin: 0; font-size: 1.25rem; color: var(--p-slate-900); font-weight: 800;"><?= htmlspecialchars($msg['title']) ?></h3>
                                <span style="font-size: 0.85rem; color: var(--p-slate-400); background: var(--p-slate-50); padding: 5px 12px; border-radius: 10px; font-weight: 600;">
                                    <i class="far fa-clock" style="margin-left: 5px;"></i>
                                    <?= date('Y/m/d - H:i', strtotime($msg['created_at'])) ?>
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

    <!-- Notification Placeholder for Teachers/Admin integration -->
    <div class="glass-card" style="margin-top: 30px; padding: 25px; background: linear-gradient(135deg, var(--p-emerald-600), var(--p-emerald-900)); color: white; border: none; text-align: center;">
        <h4 style="margin: 0; font-weight: 700;">هل تود التواصل مع معلميك؟</h4>
        <p style="margin: 10px 0 0; color: rgba(255,255,255,0.7); font-size: 0.95rem;">نظام التواصل المباشر مع المعلمين سيتاح قريباً بشكل كامل في التحديث القادم.</p>
    </div>
</div>

<style>
.message-item-premium:hover {
    transform: translateX(-10px);
    border-color: var(--p-emerald-500);
    box-shadow: var(--p-shadow-md);
}

.message-item-premium:hover h3 {
    color: var(--p-emerald-600);
}
</style>

<?php
});
?>
