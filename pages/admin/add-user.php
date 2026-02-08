<?php
require_once __DIR__ . '/admin_middleware.php';
require_once '../../pages/dashboard/layout.php';
require_once '../../includes/config/database.php';
require_once '../../includes/classes/Database.php';

$page_title = 'إضافة مستخدم جديد';

render_dashboard_layout(function() {
    $db = new Database();
    $conn = $db->getConnection();
    $message = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $role = $_POST['role'];
        
        // Check email
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $message = '<div class="alert error"><i class="fas fa-exclamation-circle"></i> البريد الإلكتروني مسجل مسبقاً</div>';
        } else {
            $stmt = $conn->prepare("INSERT INTO users (full_name, email, password_hash, role, status) VALUES (?, ?, ?, ?, 'active')");
            if ($stmt->execute([$name, $email, $password, $role])) {
                $message = '<div class="alert success"><i class="fas fa-check-circle"></i> تم إنشاء حساب المستخدم بنجاح</div>';
            } else {
                $message = '<div class="alert error">حدث خطأ أثناء الإنشاء</div>';
            }
        }
    }
?>

<div class="center-form-container">
    <div class="form-card glass-panel">
        <div class="form-header">
            <h2><i class="fas fa-user-plus"></i> مستخدم جديد</h2>
            <p>إضافة حساب طالب، معلم، أو مدير جديد للنظام</p>
        </div>
        
        <?= $message ?>
        
        <form method="POST">
            <div class="form-grid">
                <div class="form-group span-2">
                    <label>الاسم الكامل</label>
                    <div class="input-with-icon">
                        <i class="fas fa-user"></i>
                        <input type="text" name="name" required placeholder="مثال: أحمد محمد">
                    </div>
                </div>
                
                <div class="form-group span-2">
                    <label>البريد الإلكتروني</label>
                    <div class="input-with-icon">
                        <i class="fas fa-envelope"></i>
                        <input type="email" name="email" required placeholder="name@example.com">
                    </div>
                </div>
                
                <div class="form-group">
                    <label>كلمة المرور</label>
                    <div class="input-with-icon">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="password" required minlength="6" placeholder="******">
                    </div>
                </div>
                
                <div class="form-group">
                    <label>الدور (Role)</label>
                    <div class="input-with-icon">
                        <i class="fas fa-shield-alt"></i>
                        <select name="role" required>
                            <option value="student">طالب</option>
                            <option value="teacher">معلم</option>
                            <option value="admin">مدير نظام</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="submit-btn">
                    <i class="fas fa-save"></i> إنشاء الحساب
                </button>
                <a href="users.php" class="cancel-btn">إلغاء</a>
            </div>
        </form>
    </div>
</div>

<style>
.center-form-container { max-width: 700px; margin: 40px auto; }

.form-card { background: white; padding: 40px; border-radius: 20px; box-shadow: 0 10px 40px rgba(0,0,0,0.06); }

.form-header { text-align: center; margin-bottom: 30px; }
.form-header h2 { font-size: 1.8rem; color: #1e293b; margin: 0 0 10px; }
.form-header h2 i { color: #8b5cf6; }
.form-header p { color: #64748b; }

.alert { padding: 15px; border-radius: 12px; margin-bottom: 25px; display: flex; align-items: center; gap: 10px; }
.alert.success { background: #dcfce7; color: #166534; }
.alert.error { background: #fee2e2; color: #991b1b; }

.form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
.span-2 { grid-column: span 2; }

.form-group label { display: block; margin-bottom: 8px; font-weight: 600; color: #334155; }

.input-with-icon { position: relative; }
.input-with-icon i { position: absolute; right: 15px; top: 50%; transform: translateY(-50%); color: #94a3b8; }
.input-with-icon input, .input-with-icon select { width: 100%; padding: 12px 45px 12px 15px; border: 2px solid #e2e8f0; border-radius: 10px; font-family: inherit; transition: 0.3s; background: #f8fafc; }
.input-with-icon input:focus, .input-with-icon select:focus { border-color: #8b5cf6; background: white; outline: none; }

.form-actions { margin-top: 30px; display: flex; gap: 15px; }
.submit-btn { flex: 1; background: linear-gradient(135deg, #8b5cf6, #7c3aed); color: white; border: none; padding: 12px; border-radius: 10px; font-weight: 600; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; transition: 0.3s; }
.submit-btn:hover { box-shadow: 0 4px 15px rgba(139, 92, 246, 0.4); transform: translateY(-1px); }

.cancel-btn { padding: 12px 25px; color: #64748b; text-decoration: none; font-weight: 600; border-radius: 10px; transition: 0.2s; }
.cancel-btn:hover { background: #f1f5f9; color: #1e293b; }

@media (max-width: 600px) { .form-grid { grid-template-columns: 1fr; } .span-2 { grid-column: span 1; } }
</style>

<?php
});
?>
