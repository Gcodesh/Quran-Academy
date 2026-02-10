<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../includes/config/root.php';
require_once path('includes/config/database.php');
require_once path('includes/classes/Database.php');

// --- AUTO MIGRATION (Safe Fail) ---
// This attempts to add columns if they don't exist, handling the CLI failure issue.
try {
    $db = new Database();
    $conn = $db->getConnection();
    $cols = $conn->query("DESCRIBE users")->fetchAll(PDO::FETCH_COLUMN);
    
    if (!in_array('phone', $cols)) $conn->exec("ALTER TABLE users ADD COLUMN phone VARCHAR(20) NULL");
    if (!in_array('country', $cols)) $conn->exec("ALTER TABLE users ADD COLUMN country VARCHAR(100) NULL");
    if (!in_array('city', $cols)) $conn->exec("ALTER TABLE users ADD COLUMN city VARCHAR(100) NULL");
    if (!in_array('age', $cols)) $conn->exec("ALTER TABLE users ADD COLUMN age INT NULL");
    if (!in_array('avatar', $cols)) $conn->exec("ALTER TABLE users ADD COLUMN avatar VARCHAR(255) NULL");
    if (!in_array('id_card_path', $cols)) $conn->exec("ALTER TABLE users ADD COLUMN id_card_path VARCHAR(255) NULL");
} catch (Exception $e) {
    // Silent fail if already exists or db issue (will be caught later)
}
// ----------------------------------

$message = '';
$messageType = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'student';
    $phone = $_POST['phone'] ?? '';
    $country = $_POST['country'] ?? '';
    $city = $_POST['city'] ?? '';
    $age = $_POST['age'] ?? '';
    
    // Validation
    if (empty($name) || empty($email) || empty($password)) {
        $message = "الرجاء تعبئة الحقول الأساسية";
        $messageType = "error";
    } else {
        // Check Email
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $message = "البريد الإلكتروني مسجل مسبقاً";
            $messageType = "error";
        } else {
            // File Uploads
            $avatarPath = null;
            $idCardPath = null;
            $uploadErrors = [];

            // 1. Avatar
            if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] == 0) {
                $ext = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
                $filename = 'avatar_' . time() . '_' . uniqid() . '.' . $ext;
                if (!is_dir('../uploads/avatars')) mkdir('../uploads/avatars', 0777, true);
                if (move_uploaded_file($_FILES['avatar']['tmp_name'], '../uploads/avatars/' . $filename)) {
                    $avatarPath = 'uploads/avatars/' . $filename;
                }
            }

            // 2. ID Card (Teacher Only)
            if ($role === 'teacher') {
                if (isset($_FILES['id_card']) && $_FILES['id_card']['error'] == 0) {
                    $ext = pathinfo($_FILES['id_card']['name'], PATHINFO_EXTENSION);
                    $filename = 'id_' . time() . '_' . uniqid() . '.' . $ext;
                    if (!is_dir('../uploads/documents')) mkdir('../uploads/documents', 0777, true);
                    if (move_uploaded_file($_FILES['id_card']['tmp_name'], '../uploads/documents/' . $filename)) {
                        $idCardPath = 'uploads/documents/' . $filename;
                    }
                } else {
                    $uploadErrors[] = "صورة الهوية مطلوبة للمعلمين وتوثيق الحساب.";
                }
            }

            if (!empty($uploadErrors)) {
                $message = implode('<br>', $uploadErrors);
                $messageType = "error";
            } else {
                // Insert User
                $status = ($role === 'teacher') ? 'pending' : 'active';
                $password_hash = password_hash($password, PASSWORD_DEFAULT);
                
                $sql = "INSERT INTO users (full_name, email, password_hash, role, status, phone, country, city, age, avatar, id_card_path) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                
                if ($stmt->execute([$name, $email, $password_hash, $role, $status, $phone, $country, $city, $age, $avatarPath, $idCardPath])) {
                    if ($role === 'teacher') {
                        header("Location: " . url('pages/teacher-pending.php'));
                        exit;
                    } else {
                        // Auto Login for Students
                        $_SESSION['user_id'] = $conn->lastInsertId();
                        $_SESSION['user_role'] = 'student';
                        $_SESSION['user_name'] = $name;
                        header("Location: " . url('pages/dashboard/index.php'));
                        exit;
                    }
                } else {
                    $message = "حدث خطأ أثناء التسجيل";
                    $messageType = "error";
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إنشاء حساب جديد | منصة التميز</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body { background: #f8fafc; font-family: 'Tajawal', sans-serif; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; }
        
        .register-container {
            background: white;
            border-radius: 24px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.06);
            width: 100%;
            max-width: 900px;
            overflow: hidden;
            display: flex;
        }

        .side-image {
            width: 40%;
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            padding: 40px;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .side-image h2 { font-size: 2.2rem; margin-bottom: 20px; line-height: 1.4; }
        .side-image p { opacity: 0.8; font-size: 1.1rem; }
        .feature-list { list-style: none; padding: 0; margin-top: 30px; }
        .feature-list li { margin-bottom: 15px; display: flex; align-items: center; gap: 10px; font-weight: 500; }
        .feature-list li i { color: #f59e0b; }

        .form-section {
            width: 60%;
            padding: 40px;
            max-height: 90vh;
            overflow-y: auto;
        }

        /* Tabs */
        .role-tabs {
            display: flex;
            background: #f1f5f9;
            padding: 5px;
            border-radius: 12px;
            margin-bottom: 30px;
        }
        .role-tab {
            flex: 1;
            text-align: center;
            padding: 12px;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 700;
            color: #64748b;
            transition: 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .role-tab.active {
            background: white;
            color: #1e293b;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }
        .role-tab.teacher-active { color: #1e293b; border-bottom: 3px solid #f59e0b; }

        /* Form Grid */
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        .span-2 { grid-column: span 2; }

        .form-group label { display: block; margin-bottom: 8px; font-weight: 600; color: #334155; font-size: 0.95rem; }
        .form-input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-family: inherit;
            transition: 0.3s;
        }
        .form-input:focus { border-color: #8b5cf6; outline: none; background: #fff; }

        /* File Upload */
        .file-upload-box {
            border: 2px dashed #cbd5e1;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            transition: 0.3s;
            background: #f8fafc;
            position: relative;
        }
        .file-upload-box:hover { border-color: #8b5cf6; background: #f5f3ff; }
        .file-upload-box input {
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            opacity: 0;
            cursor: pointer;
        }
        .file-icon { font-size: 2rem; color: #94a3b8; margin-bottom: 10px; }
        .file-label { color: #64748b; font-size: 0.9rem; }

        .btn-submit {
            width: 100%;
            padding: 15px;
            background: #1e293b;
            color: white;
            border: none;
            border-radius: 12px;
            font-weight: 700;
            font-size: 1.1rem;
            cursor: pointer;
            transition: 0.3s;
            margin-top: 20px;
        }
        .btn-submit:hover { background: #8b5cf6; box-shadow: 0 10px 25px rgba(139, 92, 246, 0.3); }

        .auth-footer { text-align: center; margin-top: 20px; font-size: 0.95rem; color: #64748b; }
        .auth-footer a { color: #8b5cf6; text-decoration: none; font-weight: 700; }

        .alert {
            padding: 15px;
            border-radius: 12px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 600;
        }
        .alert.error { background: #fee2e2; color: #991b1b; }

        .teacher-badge-details {
            display: none;
            background: #fffbeb;
            padding: 15px;
            border-radius: 12px;
            margin-bottom: 20px;
            border: 1px solid #fcd34d;
            color: #b45309;
            font-size: 0.9rem;
        }
        
        @media (max-width: 900px) {
            .register-container { flex-direction: column; max-width: 500px; }
            .side-image { display: none; }
            .form-section { width: 100%; }
        }
    </style>
</head>
<body>

<div class="register-container">
    <!-- Side Visual -->
    <div class="side-image">
        <div>
            <h2>انضم إلى مجتمع<br><span style="color: #f59e0b;">التميز والإبداع</span></h2>
            <p>سواء كنت طالباً تبحث عن المعرفة أو معلماً تسعى لنشر العلم، مكانك هنا.</p>
            <ul class="feature-list">
                <li><i class="fas fa-check-circle"></i> وصول غير محدود للدورات</li>
                <li><i class="fas fa-check-circle"></i> شهادات معتمدة</li>
                <li><i class="fas fa-check-circle"></i> مجتمع تعليمي تفاعلي</li>
            </ul>
        </div>
        <div style="font-size: 0.8rem; opacity: 0.5;">
            &copy; 2026 Islamic Platform
        </div>
    </div>

    <!-- Form Section -->
    <div class="form-section">
        <?php if($message): ?>
            <div class="alert <?= $messageType ?>">
                <i class="fas fa-exclamation-circle"></i> <?= $message ?>
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" id="registerForm">
            <!-- Hidden Role Input -->
            <input type="hidden" name="role" id="roleInput" value="student">

            <!-- Custom Tabs -->
            <div class="role-tabs">
                <div class="role-tab active" onclick="setRole('student', this)">
                    <i class="fas fa-user-graduate"></i> حساب طالب
                </div>
                <div class="role-tab" onclick="setRole('teacher', this)">
                    <i class="fas fa-chalkboard-teacher"></i> حساب معلم
                </div>
            </div>
            
            <div id="teacherNote" class="teacher-badge-details">
                <i class="fas fa-info-circle"></i> 
                حسابات المعلمين تتطلب موافقة الإدارة. يرجى رفع صورة الهوية للتفعيل.
            </div>

            <div class="form-grid">
                <!-- Basic Info -->
                <div class="form-group span-2">
                    <label>الاسم الكامل</label>
                    <input type="text" name="name" class="form-input" required placeholder="الاسم الثلاثي">
                </div>
                
                <div class="form-group span-2">
                    <label>البريد الإلكتروني</label>
                    <input type="email" name="email" class="form-input" required placeholder="email@example.com">
                </div>
                
                <div class="form-group span-2">
                    <label>كلمة المرور</label>
                    <input type="password" name="password" class="form-input" required placeholder="******">
                </div>

                <!-- Personal Info -->
                <div class="form-group">
                    <label>رقم الهاتف</label>
                    <input type="tel" name="phone" class="form-input" placeholder="05xxxxxxxx">
                </div>
                <div class="form-group">
                    <label>العمر</label>
                    <input type="number" name="age" class="form-input" placeholder="سنة">
                </div>

                <div class="form-group">
                    <label>الدولة</label>
                    <input type="text" name="country" class="form-input" placeholder="المملكة العربية السعودية">
                </div>
                 <div class="form-group">
                    <label>المحافظة / المدينة</label>
                    <input type="text" name="city" class="form-input" placeholder="الرياض">
                </div>

                <!-- Uploads -->
                <div class="form-group span-2">
                    <label>الصورة الشخصية (اختياري)</label>
                    <div class="file-upload-box">
                        <input type="file" name="avatar" accept="image/*" onchange="previewFile(this, 'avatarDetails')">
                        <div class="file-icon"><i class="fas fa-camera"></i></div>
                        <div class="file-label" id="avatarDetails">اضغط لرفع صورة شخصية</div>
                    </div>
                </div>

                <!-- Teacher Specific -->
                <div class="form-group span-2 teacher-only" style="display: none;">
                    <label>صورة الهوية / التوثيق (مطلوب للمعلم)</label>
                    <div class="file-upload-box" style="border-color: #f59e0b; background: #fff7ed;">
                        <input type="file" name="id_card" accept="image/*" onchange="previewFile(this, 'idDetails')">
                        <div class="file-icon" style="color: #f59e0b;"><i class="fas fa-id-card"></i></div>
                        <div class="file-label" id="idDetails">اضغط لرفع صورة الهوية</div>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn-submit">إنشاء الحساب</button>

            <div class="auth-footer">
                لديك حساب بالفعل؟ <a href="<?= url('pages/login.php') ?>">تسجيل الدخول</a>
            </div>
        </form>
    </div>
</div>

<script>
    function setRole(role, element) {
        // Update input
        document.getElementById('roleInput').value = role;
        
        // Update Tabs UI
        document.querySelectorAll('.role-tab').forEach(el => el.classList.remove('active'));
        element.classList.add('active');
        
        // Show/Hide Fields
        const teacherFields = document.querySelectorAll('.teacher-only');
        const teacherNote = document.getElementById('teacherNote');
        
        if (role === 'teacher') {
            teacherFields.forEach(el => el.style.display = 'block');
            teacherNote.style.display = 'block';
        } else {
            teacherFields.forEach(el => el.style.display = 'none');
            teacherNote.style.display = 'none';
        }
    }

    function previewFile(input, labelId) {
        if (input.files && input.files[0]) {
            document.getElementById(labelId).innerText = "تم الاختيار: " + input.files[0].name;
            document.getElementById(labelId).style.color = "#10b981";
            document.getElementById(labelId).style.fontWeight = "bold";
        }
    }
</script>

</body>
</html>