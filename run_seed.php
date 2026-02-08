<?php
/**
 * Standalone Seed Script - Works directly in htdocs
 * Access: http://localhost/run_seed.php
 */

// Direct database connection (standalone)
$host = 'localhost';
$dbname = 'islamic_education';
$username = 'root';
$password = '';

echo "<html dir='rtl'><head><meta charset='UTF-8'><title>Seed Data</title>";
echo "<style>body{font-family:Tahoma;padding:40px;background:#f5f5f5;} .box{background:#fff;padding:30px;border-radius:15px;max-width:600px;margin:0 auto;box-shadow:0 5px 20px rgba(0,0,0,0.1);} .success{color:#10b981;} .error{color:#ef4444;} h2{color:#0d9488;} table{width:100%;border-collapse:collapse;margin:20px 0;} td,th{padding:12px;text-align:right;border-bottom:1px solid #eee;} th{background:#f8f9fa;}</style>";
echo "</head><body><div class='box'>";

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check if users already exist
    $existingUsers = $conn->query("SELECT COUNT(*) FROM users")->fetchColumn();
    
    if ($existingUsers > 0 && !isset($_GET['force'])) {
        echo "<h2>⚠️ البيانات موجودة بالفعل</h2>";
        echo "<p>يوجد $existingUsers مستخدم في قاعدة البيانات.</p>";
        echo "<p><a href='?force=1' style='color:#ef4444;font-weight:bold;'>🔄 اضغط هنا لإعادة تعيين البيانات</a></p>";
        showLoginInfo($conn);
        echo "</div></body></html>";
        exit;
    }
    
    if (isset($_GET['force'])) {
        // Force reset
        $conn->exec("SET FOREIGN_KEY_CHECKS = 0");
        $conn->exec("TRUNCATE TABLE progress");
        $conn->exec("TRUNCATE TABLE enrollments");
        $conn->exec("TRUNCATE TABLE lessons");
        $conn->exec("TRUNCATE TABLE courses");
        $conn->exec("TRUNCATE TABLE audit_logs");
        $conn->exec("TRUNCATE TABLE messages");
        $conn->exec("TRUNCATE TABLE users");
        $conn->exec("SET FOREIGN_KEY_CHECKS = 1");
        echo "<p class='success'>✓ تم حذف البيانات القديمة</p>";
    }
    
    // Password hash for 'password'
    $passwordHash = password_hash('password', PASSWORD_DEFAULT);
    
    // Insert Users
    $users = [
        ['مدير النظام', 'مدير النظام', 'admin@islamic-edu.com', 'admin'],
        ['الأستاذة سارة أحمد', 'الأستاذة سارة أحمد', 'sara@islamic-edu.com', 'teacher'],
        ['الأستاذ محمد علي', 'الأستاذ محمد علي', 'mohamed@islamic-edu.com', 'teacher'],
        ['الأستاذة فاطمة محمود', 'الأستاذة فاطمة محمود', 'fatima@islamic-edu.com', 'teacher'],
        ['الأستاذ خالد أحمد', 'الأستاذ خالد أحمد', 'khaled@islamic-edu.com', 'teacher'],
        ['طالب تجريبي', 'طالب تجريبي', 'student@islamic-edu.com', 'student'],
    ];
    
    $stmt = $conn->prepare("INSERT INTO users (full_name, name, email, password_hash, role, status) VALUES (?, ?, ?, ?, ?, 'active')");
    foreach ($users as $user) {
        $stmt->execute([$user[0], $user[1], $user[2], $passwordHash, $user[3]]);
    }
    echo "<p class='success'>✓ تم إضافة " . count($users) . " مستخدم</p>";
    
    // Insert Courses
    $courses = [
        ['تعليم القرآن للأطفال', 'دورة شاملة لتعليم الأطفال القرآن بطريقة ممتعة وتفاعلية.', 'https://images.unsplash.com/photo-1609599006353-e629aaabfeae?auto=format&fit=crop&w=600', 0, 2],
        ['فقه الصلاة', 'تعلم أساسيات الفقه بطريقة مبسطة وعملية.', null, 0, 3],
        ['تجويد القرآن', 'دورة متقدمة لتعلم أحكام التجويد والتلاوة الصحيحة.', 'https://images.unsplash.com/photo-1585036156171-384164a8c675?auto=format&fit=crop&w=600', 29, 4],
        ['شرح صحيح البخاري', 'دورة متخصصة في شرح أحاديث صحيح البخاري.', null, 0, 5],
        ['النحو والصرف للمبتدئين', 'تعلم أساسيات النحو والصرف في اللغة العربية.', null, 0, 2],
        ['العقيدة الإسلامية', 'دراسة شاملة لأركان الإيمان وأصول العقيدة.', null, 0, 3],
        ['حفظ القرآن الكريم', 'برنامج منهجي لحفظ القرآن الكريم.', null, 0, 4],
        ['فقه الزكاة والصيام', 'تعلم أحكام الزكاة والصيام بالتفصيل.', null, 25, 5],
    ];
    
    $stmt = $conn->prepare("INSERT INTO courses (title, description, image, price, teacher_id, status) VALUES (?, ?, ?, ?, ?, 'published')");
    foreach ($courses as $course) {
        $stmt->execute($course);
    }
    echo "<p class='success'>✓ تم إضافة " . count($courses) . " دورات</p>";
    
    // Insert Sample Lessons
    $lessons = [
        [1, 'مقدمة عن القرآن الكريم', 'التعريف بالقرآن الكريم'],
        [1, 'الحروف الهجائية', 'تعلم نطق الحروف'],
        [2, 'شروط الصلاة', 'الشروط الواجب توفرها'],
        [2, 'أركان الصلاة', 'الأركان الأساسية'],
        [3, 'أحكام النون الساكنة', 'الإظهار والإدغام'],
        [3, 'أحكام الميم الساكنة', 'الإخفاء الشفوي'],
    ];
    
    $stmt = $conn->prepare("INSERT INTO lessons (course_id, title, content) VALUES (?, ?, ?)");
    foreach ($lessons as $lesson) {
        $stmt->execute($lesson);
    }
    echo "<p class='success'>✓ تم إضافة " . count($lessons) . " دروس</p>";
    
    // Add enrollment for student
    $conn->exec("INSERT INTO enrollments (user_id, course_id, progress_percentage) VALUES (6, 1, 25), (6, 2, 50)");
    echo "<p class='success'>✓ تم تسجيل الطالب في دورتين</p>";
    
    echo "<h2 class='success'>✅ تم إضافة البيانات بنجاح!</h2>";
    
    showLoginInfo($conn);
    
} catch (PDOException $e) {
    echo "<h2 class='error'>❌ خطأ في الاتصال بقاعدة البيانات</h2>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p>تأكد من:</p><ul><li>تشغيل MySQL في XAMPP</li><li>وجود قاعدة بيانات islamic_education</li></ul>";
}

function showLoginInfo($conn) {
    echo "<h3>🔐 بيانات تسجيل الدخول:</h3>";
    echo "<table>";
    echo "<tr><th>الدور</th><th>البريد الإلكتروني</th><th>كلمة المرور</th></tr>";
    echo "<tr><td>🔧 أدمن</td><td><strong>admin@islamic-edu.com</strong></td><td>password</td></tr>";
    echo "<tr><td>👨‍🏫 معلم</td><td><strong>sara@islamic-edu.com</strong></td><td>password</td></tr>";
    echo "<tr><td>👨‍🎓 طالب</td><td><strong>student@islamic-edu.com</strong></td><td>password</td></tr>";
    echo "</table>";
    echo "<p style='margin-top:20px;'><a href='islamic-education-platform/pages/login.php' style='background:#0d9488;color:#fff;padding:12px 25px;border-radius:8px;text-decoration:none;display:inline-block;'>🚀 اذهب لتسجيل الدخول</a></p>";
}

echo "</div></body></html>";
?>
