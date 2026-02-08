<?php
require_once __DIR__ . '/admin_middleware.php';
require_once '../../pages/dashboard/layout.php';
require_once '../../includes/config/database.php';
require_once '../../includes/classes/Database.php';

$page_title = 'تعديل بيانات المستخدم';

render_dashboard_layout(function() {
    $db = new Database();
    $conn = $db->getConnection();
    
    $user_id = $_GET['id'] ?? null;
    if (!$user_id) {
        header('Location: users.php');
        exit;
    }

    $message = '';
    
    // Handle Update
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $role = $_POST['role'];
        $status = $_POST['status'];
        
        try {
            // Check if email taken by others
            $check = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
            $check->execute([$email, $user_id]);
            if ($check->fetch()) {
                $message = '<div class="p-alert-floating danger"><i class="fas fa-exclamation-triangle"></i> البريد الإلكتروني مستخدم بالفعل</div>';
            } else {
                $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, role = ?, status = ? WHERE id = ?");
                $stmt->execute([$name, $email, $role, $status, $user_id]);
                $message = '<div class="p-alert-floating success"><i class="fas fa-check-circle"></i> تم تحديث البيانات بنجاح</div>';
            }
        } catch (Exception $e) {
            $message = '<div class="p-alert-floating danger">حدث خطأ: ' . $e->getMessage() . '</div>';
        }
    }

    // Fetch User Data
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo '<div class="glass-card" style="padding:40px; text-align:center;"><h2>المستخدم غير موجود</h2><a href="users.php">العودة للقائمة</a></div>';
        return;
    }
?>

<div class="admin-edit-user premium-experience">
    <!-- Header -->
    <div class="dashboard-header-flex">
        <div>
            <h1 style="font-size: 2.2rem; font-weight: 900; color: var(--p-slate-900); margin: 0;">تعديل المستخدم 👤</h1>
            <p style="color: var(--p-slate-500); margin-top: 10px;">أنت تقوم بتعديل بيانات: <strong><?= htmlspecialchars($user['name']) ?></strong></p>
        </div>
        <div style="display:flex; gap:15px;">
            <a href="users.php" class="p-btn-secondary" style="text-decoration:none; padding:12px 25px; border-radius:15px; background:white; border:1px solid var(--p-slate-100); color:var(--p-slate-600); font-weight:700; display:flex; align-items:center; gap:8px;">
                <i class="fas fa-arrow-right"></i> عرض الكل
            </a>
            <button type="submit" form="editUserForm" class="p-save-btn" style="padding:12px 30px; border-radius:15px; background: linear-gradient(135deg, var(--p-emerald-600), var(--p-emerald-700)); color:white; border:none; font-weight:800; cursor:pointer; box-shadow:0 10px 20px rgba(13,148,136,0.2);">
                <i class="fas fa-save"></i> حفظ التغييرات
            </button>
        </div>
    </div>

    <?= $message ?>

    <div class="p-edit-layout" style="display:grid; grid-template-columns: 350px 1fr; gap:30px; margin-top:40px;">
        <!-- Left: Profile Summary -->
        <div class="p-profile-sidebar">
            <div class="glass-card" style="padding:35px; text-align:center;">
                <div class="p-avatar-large" style="width:120px; height:120px; border-radius:40px; background:linear-gradient(135deg, #f59e0b, #d97706); color:white; display:flex; align-items:center; justify-content:center; font-size:3.5rem; margin:0 auto 25px; box-shadow:var(--p-shadow-lg);">
                    <?= mb_substr($user['name'], 0, 1, 'UTF-8') ?>
                </div>
                <h3 style="color:var(--p-slate-900); font-size:1.5rem; margin:0 0 5px;"><?= htmlspecialchars($user['name']) ?></h3>
                <span class="p-badge" style="background:var(--p-slate-50); color:var(--p-slate-400);"><?= strtoupper($user['role']) ?></span>
                
                <div style="margin-top:30px; text-align:right; border-top:1px solid var(--p-slate-50); padding-top:20px;">
                    <div style="display:flex; justify-content:space-between; margin-bottom:12px;">
                        <span style="color:var(--p-slate-400);">رقم المعرف:</span>
                        <strong style="color:var(--p-slate-800);">#<?= $user['id'] ?></strong>
                    </div>
                    <div style="display:flex; justify-content:space-between;">
                        <span style="color:var(--p-slate-400);">تاريخ الانضمام:</span>
                        <strong style="color:var(--p-slate-800);"><?= date('Y/m/d', strtotime($user['created_at'])) ?></strong>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right: Edit Form -->
        <div class="p-form-area">
            <form method="POST" id="editUserForm" class="glass-card" style="padding:45px;">
                <div style="margin-bottom:30px; border-bottom:1px solid var(--p-slate-50); padding-bottom:20px;">
                    <h4 style="margin:0; font-size:1.3rem; color:var(--p-slate-900);">البيانات الشخصية</h4>
                    <p style="color:var(--p-slate-400); margin-top:5px;">تأكد من صحة البريد الإلكتروني والاسم الكامل</p>
                </div>

                <div class="p-form-grid" style="display:grid; grid-template-columns:1fr 1fr; gap:25px;">
                    <div class="form-group" style="grid-column: span 2;">
                        <label style="font-weight:800; color:var(--p-slate-800); margin-bottom:10px; display:block;">الاسم الكامل</label>
                        <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" class="premium-input" required>
                    </div>

                    <div class="form-group">
                        <label style="font-weight:800; color:var(--p-slate-800); margin-bottom:10px; display:block;">البريد الإلكتروني</label>
                        <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" class="premium-input" required>
                    </div>

                    <div class="form-group">
                        <label style="font-weight:800; color:var(--p-slate-800); margin-bottom:10px; display:block;">رقم الهاتف</label>
                        <input type="text" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>" class="premium-input" placeholder="05xxxxxxxx">
                    </div>

                    <div class="form-group">
                        <label style="font-weight:800; color:var(--p-slate-800); margin-bottom:10px; display:block;">الدور في المنصة</label>
                        <select name="role" class="premium-input">
                            <option value="student" <?= $user['role'] == 'student' ? 'selected' : '' ?>>طالب</option>
                            <option value="teacher" <?= $user['role'] == 'teacher' ? 'selected' : '' ?>>معلم</option>
                            <option value="admin" <?= $user['role'] == 'admin' ? 'selected' : '' ?>>مدير نظام</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label style="font-weight:800; color:var(--p-slate-800); margin-bottom:10px; display:block;">حالة الحساب</label>
                        <select name="status" class="premium-input">
                            <option value="active" <?= $user['status'] == 'active' ? 'selected' : '' ?>>نشط</option>
                            <option value="suspended" <?= $user['status'] == 'suspended' ? 'selected' : '' ?>>موقوف مؤقتاً</option>
                            <option value="banned" <?= $user['status'] == 'banned' ? 'selected' : '' ?>>محظور نهائياً</option>
                        </select>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.p-alert-floating {
    position: fixed;
    bottom: 30px;
    left: 30px;
    padding: 20px 35px;
    border-radius: 20px;
    color: white;
    font-weight: 700;
    z-index: 9999;
    box-shadow: 0 15px 40px rgba(0,0,0,0.2);
    animation: pFadeInRight 0.5s ease;
}
.p-alert-floating.success { background: #10b981; border: 1px solid #14b8a6; }
.p-alert-floating.danger { background: #ef4444; border: 1px solid #f43f5e; }

@keyframes pFadeInRight {
    from { opacity: 0; transform: translateX(-50px); }
    to { opacity: 1; transform: translateX(0); }
}

.premium-input {
    width: 100%;
    padding: 15px 20px;
    background: var(--p-slate-50);
    border: 2px solid transparent;
    border-radius: 15px;
    font-size: 1rem;
    transition: 0.3s;
}
.premium-input:focus {
    background: white;
    border-color: var(--p-emerald-500);
    box-shadow: 0 0 0 5px rgba(20,184,166,0.1);
    outline: none;
}
</style>

<?php
});
?>
