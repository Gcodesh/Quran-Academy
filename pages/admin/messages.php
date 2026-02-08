<?php
require_once __DIR__ . '/admin_middleware.php';
require_once '../../pages/dashboard/layout.php';
require_once '../../includes/config/database.php';
require_once '../../includes/classes/Database.php';
require_once '../../includes/auth_middleware.php';

$page_title = 'الرسائل الجماعية';

render_dashboard_layout(function() {
    $db = new Database();
    $conn = $db->getConnection();
    $csrf_token = generateCsrfToken();
    
    $success_message = '';
    $error_message = '';
    
    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'send_bulk') {
        $title = trim($_POST['title'] ?? '');
        $message = trim($_POST['message'] ?? '');
        $target = $_POST['target'] ?? 'all';
        
        if (empty($title) || empty($message)) {
            $error_message = 'يرجى ملء جميع الحقول المطلوبة';
        } else {
            try {
                // Build query based on target
                if ($target === 'all') {
                    $users = $conn->query("SELECT id FROM users WHERE status = 'active'")->fetchAll(PDO::FETCH_COLUMN);
                } elseif ($target === 'students') {
                    $users = $conn->query("SELECT id FROM users WHERE role = 'student' AND status = 'active'")->fetchAll(PDO::FETCH_COLUMN);
                } elseif ($target === 'teachers') {
                    $users = $conn->query("SELECT id FROM users WHERE role = 'teacher' AND status = 'active'")->fetchAll(PDO::FETCH_COLUMN);
                } else {
                    $users = [];
                }
                
                // Insert notifications for each user
                $stmt = $conn->prepare("INSERT INTO notifications (user_id, title, message, type) VALUES (?, ?, ?, 'system')");
                $count = 0;
                foreach ($users as $user_id) {
                    $stmt->execute([$user_id, $title, $message]);
                    $count++;
                }
                
                $success_message = "تم إرسال الإشعار بنجاح إلى $count مستخدم";
                
            } catch (Exception $e) {
                $error_message = 'حدث خطأ أثناء إرسال الرسائل: ' . $e->getMessage();
            }
        }
    }
    
    // Get stats
    $total_users = $conn->query("SELECT COUNT(*) FROM users WHERE status = 'active'")->fetchColumn();
    $total_students = $conn->query("SELECT COUNT(*) FROM users WHERE role = 'student' AND status = 'active'")->fetchColumn();
    $total_teachers = $conn->query("SELECT COUNT(*) FROM users WHERE role = 'teacher' AND status = 'active'")->fetchColumn();
    
    // Recent sent notifications
    $recent_notifications = $conn->query("
        SELECT n.*, u.full_name 
        FROM notifications n 
        LEFT JOIN users u ON n.user_id = u.id 
        WHERE n.type = 'system' 
        ORDER BY n.created_at DESC 
        LIMIT 10
    ")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="admin-messages premium-experience">
    <!-- Premium Header -->
    <div class="dashboard-header-flex">
        <div>
            <h1 style="font-size: 2.2rem; font-weight: 900; color: var(--p-slate-900); margin: 0; letter-spacing: -0.5px;">الإشعارات الجماعية 📢</h1>
            <p style="color: var(--p-slate-500); margin-top: 10px; font-size: 1.1rem;">إرسال رسائل وتنبيهات فورية لكافة المستخدمين في المنصة</p>
        </div>
        <div style="display: flex; gap: 15px;">
            <div class="glass-card" style="padding: 12px 25px; display: flex; align-items: center; gap: 10px; color: #8b5cf6; font-weight: 700; border-radius: 18px;">
                <i class="fas fa-history"></i> السجل
            </div>
        </div>
    </div>

    <?php if($success_message): ?>
        <div class="alert success">
            <i class="fas fa-check-circle"></i>
            <?= htmlspecialchars($success_message) ?>
        </div>
    <?php endif; ?>

    <?php if($error_message): ?>
        <div class="alert error">
            <i class="fas fa-exclamation-circle"></i>
            <?= htmlspecialchars($error_message) ?>
        </div>
    <?php endif; ?>

    <div class="content-grid">
        <!-- Send Message Form -->
        <div class="form-panel">
            <div class="panel-title">
                <i class="fas fa-paper-plane"></i>
                <h2>إرسال رسالة جديدة</h2>
            </div>

            <form method="POST" class="message-form">
                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                <input type="hidden" name="action" value="send_bulk">

                <div class="form-group">
                    <label><i class="fas fa-users"></i> المستهدفون</label>
                    <div class="target-cards">
                        <label class="target-card">
                            <input type="radio" name="target" value="all" checked>
                            <div class="card-content">
                                <div class="card-icon all"><i class="fas fa-globe"></i></div>
                                <span>الجميع</span>
                                <small><?= number_format($total_users) ?> مستخدم</small>
                            </div>
                        </label>
                        <label class="target-card">
                            <input type="radio" name="target" value="students">
                            <div class="card-content">
                                <div class="card-icon students"><i class="fas fa-user-graduate"></i></div>
                                <span>الطلاب</span>
                                <small><?= number_format($total_students) ?> طالب</small>
                            </div>
                        </label>
                        <label class="target-card">
                            <input type="radio" name="target" value="teachers">
                            <div class="card-content">
                                <div class="card-icon teachers"><i class="fas fa-chalkboard-teacher"></i></div>
                                <span>المعلمون</span>
                                <small><?= number_format($total_teachers) ?> معلم</small>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label><i class="fas fa-heading"></i> عنوان الرسالة <span class="required">*</span></label>
                    <input type="text" name="title" class="form-input" placeholder="مثال: إعلان هام" required>
                </div>

                <div class="form-group">
                    <label><i class="fas fa-align-right"></i> نص الرسالة <span class="required">*</span></label>
                    <textarea name="message" class="form-input" rows="6" placeholder="اكتب رسالتك هنا..." required></textarea>
                </div>

                <div class="form-group templates">
                    <label><i class="fas fa-magic"></i> قوالب جاهزة</label>
                    <div class="template-buttons">
                        <button type="button" class="template-btn" data-title="ترحيب" data-message="مرحباً بكم في منصتنا التعليمية! نتمنى لكم تجربة تعليمية ممتعة.">ترحيب</button>
                        <button type="button" class="template-btn" data-title="تحديث هام" data-message="تم إضافة ميزات جديدة للمنصة، ندعوكم لاستكشافها!">تحديث</button>
                        <button type="button" class="template-btn" data-title="دورة جديدة" data-message="تم إضافة دورة جديدة للمنصة، سارعوا بالتسجيل!">دورة جديدة</button>
                        <button type="button" class="template-btn" data-title="صيانة" data-message="سيتم إجراء صيانة دورية للمنصة. نعتذر عن أي إزعاج.">صيانة</button>
                    </div>
                </div>

                <button type="submit" class="submit-btn">
                    <i class="fas fa-paper-plane"></i>
                    إرسال الرسالة
                </button>
            </form>
        </div>

        <!-- Recent Messages -->
        <div class="history-panel">
            <div class="panel-title">
                <i class="fas fa-history"></i>
                <h2>آخر الإشعارات المرسلة</h2>
            </div>

            <div class="history-list">
                <?php if(empty($recent_notifications)): ?>
                    <div class="empty-state">
                        <i class="fas fa-inbox"></i>
                        <p>لا توجد رسائل مرسلة بعد</p>
                    </div>
                <?php else: ?>
                    <?php 
                    $displayed = [];
                    foreach($recent_notifications as $notif): 
                        // Group by title and timestamp
                        $key = $notif['title'] . $notif['created_at'];
                        if(isset($displayed[$key])) continue;
                        $displayed[$key] = true;
                    ?>
                        <div class="history-item">
                            <div class="history-icon">
                                <i class="fas fa-bell"></i>
                            </div>
                            <div class="history-content">
                                <strong><?= htmlspecialchars($notif['title']) ?></strong>
                                <p><?= htmlspecialchars(mb_substr($notif['message'], 0, 80)) ?>...</p>
                                <span class="history-time">
                                    <i class="far fa-clock"></i>
                                    <?= date('Y/m/d - H:i', strtotime($notif['created_at'])) ?>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
.messages-page {
    max-width: 1200px;
    margin: 0 auto;
}

/* Page Header */
.page-header {
    background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
    border-radius: 20px;
    padding: 30px 35px;
    margin-bottom: 30px;
    box-shadow: 0 10px 40px rgba(139, 92, 246, 0.3);
}

.header-content {
    display: flex;
    align-items: center;
    gap: 20px;
}

.header-icon {
    width: 70px;
    height: 70px;
    background: rgba(255,255,255,0.2);
    border-radius: 18px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    color: white;
}

.header-text h1 {
    color: white;
    margin: 0;
    font-size: 1.8rem;
}

.header-text p {
    color: rgba(255,255,255,0.8);
    margin: 5px 0 0;
}

/* Alerts */
.alert {
    padding: 16px 20px;
    border-radius: 12px;
    margin-bottom: 25px;
    display: flex;
    align-items: center;
    gap: 12px;
    font-weight: 500;
}

.alert.success {
    background: #d1fae5;
    color: #065f46;
    border: 1px solid #a7f3d0;
}

.alert.error {
    background: #fee2e2;
    color: #991b1b;
    border: 1px solid #fecaca;
}

/* Content Grid */
.content-grid {
    display: grid;
    grid-template-columns: 1.2fr 1fr;
    gap: 25px;
}

/* Panels */
.form-panel, .history-panel {
    background: white;
    border-radius: 20px;
    padding: 30px;
    box-shadow: 0 4px 25px rgba(0,0,0,0.05);
}

.panel-title {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 25px;
    padding-bottom: 15px;
    border-bottom: 2px solid #f1f5f9;
}

.panel-title i {
    color: #8b5cf6;
    font-size: 1.3rem;
}

.panel-title h2 {
    margin: 0;
    font-size: 1.2rem;
    color: #1e293b;
}

/* Form Groups */
.form-group {
    margin-bottom: 25px;
}

.form-group label {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 12px;
    font-weight: 600;
    color: #334155;
}

.form-group label i {
    color: #94a3b8;
}

.required {
    color: #ef4444;
}

.form-input {
    width: 100%;
    padding: 14px 18px;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    font-size: 1rem;
    font-family: inherit;
    transition: all 0.3s;
    background: #f8fafc;
    box-sizing: border-box;
}

.form-input:focus {
    outline: none;
    border-color: #8b5cf6;
    background: white;
    box-shadow: 0 0 0 4px rgba(139, 92, 246, 0.1);
}

textarea.form-input {
    resize: vertical;
    min-height: 120px;
}

/* Target Cards */
.target-cards {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 12px;
}

.target-card {
    cursor: pointer;
}

.target-card input {
    display: none;
}

.target-card .card-content {
    padding: 20px 15px;
    border: 2px solid #e2e8f0;
    border-radius: 14px;
    text-align: center;
    transition: all 0.3s;
    background: #fafbfc;
}

.target-card input:checked + .card-content {
    border-color: #8b5cf6;
    background: linear-gradient(135deg, #faf5ff, #f3e8ff);
    box-shadow: 0 5px 20px rgba(139, 92, 246, 0.2);
}

.card-icon {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    margin: 0 auto 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
}

.card-icon.all { background: #e0e7ff; color: #4f46e5; }
.card-icon.students { background: #dbeafe; color: #2563eb; }
.card-icon.teachers { background: #d1fae5; color: #059669; }

.card-content span {
    display: block;
    font-weight: 600;
    color: #1e293b;
    margin-bottom: 5px;
}

.card-content small {
    color: #64748b;
    font-size: 0.85rem;
}

/* Templates */
.template-buttons {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}

.template-btn {
    padding: 8px 16px;
    background: #f1f5f9;
    border: 1px solid #e2e8f0;
    border-radius: 20px;
    cursor: pointer;
    font-size: 0.9rem;
    color: #475569;
    transition: all 0.3s;
    font-family: inherit;
}

.template-btn:hover {
    background: #e2e8f0;
    color: #1e293b;
}

/* Submit Button */
.submit-btn {
    width: 100%;
    padding: 16px;
    background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
    color: white;
    border: none;
    border-radius: 14px;
    font-size: 1.1rem;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    transition: all 0.3s;
    font-family: inherit;
    box-shadow: 0 10px 30px rgba(139, 92, 246, 0.3);
}

.submit-btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 15px 40px rgba(139, 92, 246, 0.4);
}

/* History List */
.history-list {
    max-height: 500px;
    overflow-y: auto;
}

.history-item {
    display: flex;
    gap: 15px;
    padding: 15px;
    border-radius: 12px;
    margin-bottom: 10px;
    background: #f8fafc;
    transition: all 0.3s;
}

.history-item:hover {
    background: #f1f5f9;
}

.history-icon {
    width: 42px;
    height: 42px;
    background: linear-gradient(135deg, #8b5cf6, #7c3aed);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    flex-shrink: 0;
}

.history-content strong {
    display: block;
    color: #1e293b;
    margin-bottom: 5px;
}

.history-content p {
    margin: 0;
    font-size: 0.9rem;
    color: #64748b;
}

.history-time {
    display: flex;
    align-items: center;
    gap: 5px;
    font-size: 0.8rem;
    color: #94a3b8;
    margin-top: 8px;
}

.empty-state {
    text-align: center;
    padding: 50px 20px;
    color: #94a3b8;
}

.empty-state i {
    font-size: 3rem;
    margin-bottom: 15px;
    opacity: 0.5;
}

/* Responsive */
@media (max-width: 900px) {
    .content-grid {
        grid-template-columns: 1fr;
    }
    
    .target-cards {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
// Template buttons
document.querySelectorAll('.template-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelector('input[name="title"]').value = this.dataset.title;
        document.querySelector('textarea[name="message"]').value = this.dataset.message;
    });
});
</script>

<?php
});
?>
