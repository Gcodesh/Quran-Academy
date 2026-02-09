<?php
/**
 * Cloud Database Setup Tool
 * Helps migrate local schema and seed data to a remote database (Aiven, TiDB, etc.)
 */

$message = '';
$status = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $host = $_POST['host'] ?? '';
    $user = $_POST['user'] ?? '';
    $pass = $_POST['pass'] ?? '';
    $name = $_POST['name'] ?? '';
    $port = $_POST['port'] ?? 3306;

    try {
        $dsn = "mysql:host=$host;port=$port;dbname=$name;charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::MYSQL_ATTR_SSL_CA => __DIR__ . '/includes/config/ca-cert.pem',
            PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false,
        ];
        
        $conn = new PDO($dsn, $user, $pass, $options);
        
        // Helper function to run SQL file with error tolerance
        $runSqlFile = function($filepath, $conn) {
            if (!file_exists($filepath)) return;
            $sqlContent = file_get_contents($filepath);
            // Split by semicolon but ignore semicolons inside quotes (basic split)
            $statements = array_filter(array_map('trim', explode(';', $sqlContent)));

            foreach ($statements as $stmt) {
                if (empty($stmt)) continue;
                try {
                    $conn->exec($stmt);
                } catch (PDOException $e) {
                    // Ignore specific "Duplicate" errors to allow re-runs
                    // 1050: Table exists, 1060: Duplicate column, 1061: Duplicate key name, 
                    // 1091: Can't drop, 1826: Duplicate foreign key
                    $code = $e->errorInfo[1] ?? 0;
                    if (!in_array($code, [1050, 1051, 1060, 1061, 1091, 1826])) {
                       // If it's not a duplicate error, throw it
                       throw $e;
                    }
                }
            }
        };

        // 1. Run Initial Schema
        $runSqlFile(__DIR__ . '/migrations/001_initial_schema.sql', $conn);
        
        // 2. Run Enterprise Updates
        $runSqlFile(__DIR__ . '/migrations/003_enterprise_lessons_schema.sql', $conn);
        $runSqlFile(__DIR__ . '/migrations/004_enterprise_core_enhancements.sql', $conn);
        
        // 3. Seed Data (Logic from run_seed.php)
        // Insert Users if empty
        $count = $conn->query("SELECT COUNT(*) FROM users")->fetchColumn();
        if ($count == 0) {
            $passwordHash = password_hash('password', PASSWORD_DEFAULT);
            $users = [
                 ['مدير النظام', 'admin@islamic-edu.com', 'admin'],
                 ['الأستاذة سارة أحمد', 'sara@islamic-edu.com', 'teacher'],
                 ['الأستاذ محمد علي', 'mohamed@islamic-edu.com', 'teacher'],
                 ['الطالب الجديد', 'student@islamic-edu.com', 'student'],
            ];
            $stmt = $conn->prepare("INSERT INTO users (full_name, email, password_hash, role, status) VALUES (?, ?, ?, ?, 'active')");
            foreach ($users as $u) $stmt->execute([$u[0], $u[1], $passwordHash, $u[2]]);
        }

        // Insert Courses if empty
        $count = $conn->query("SELECT COUNT(*) FROM courses")->fetchColumn();
        if ($count == 0) {
            $courses = [
                ['تعليم القرآن للأطفال', 'دورة شاملة لتعليم الأطفال القرآن.', 'https://images.unsplash.com/photo-1609599006353-e629aaabfeae?auto=format&fit=crop&w=600', 0, 2],
                ['فقه الصلاة', 'تعلم أساسيات الفقه.', null, 0, 3],
            ];
            $stmt = $conn->prepare("INSERT INTO courses (title, description, image, price, teacher_id, status) VALUES (?, ?, ?, ?, ?, 'published')");
            foreach ($courses as $c) $stmt->execute($c);
        }

        $message = "✅ تمت عملية النقل بنجاح! قاعدة البيانات السحابية جاهزة الآن.";
        $status = 'success';

    } catch (PDOException $e) {
        $message = "❌ خطأ في الاتصال: " . $e->getMessage();
        $status = 'error';
    }
}
?>

<!DOCTYPE html>
<html dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>إعداد قاعدة البيانات السحابية</title>
    <style>
        body { font-family: system-ui, -apple-system, sans-serif; background: #f3f4f6; display: flex; justify-content: center; min-height: 100vh; margin: 0; padding: 20px; }
        .card { background: white; padding: 2rem; border-radius: 1rem; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); width: 100%; max-width: 500px; }
        h1 { color: #111827; margin-bottom: 1.5rem; text-align: center; }
        .form-group { margin-bottom: 1rem; }
        label { display: block; margin-bottom: 0.5rem; color: #374151; font-weight: 500; }
        input { width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.5rem; box-sizing: border-box; }
        button { width: 100%; background: #2563eb; color: white; padding: 0.75rem; border: none; border-radius: 0.5rem; font-weight: bold; cursor: pointer; }
        button:hover { background: #1d4ed8; }
        .message { padding: 1rem; border-radius: 0.5rem; margin-bottom: 1rem; text-align: center; }
        .success { background: #d1fae5; color: #065f46; }
        .error { background: #fee2e2; color: #991b1b; }
    </style>
</head>
<body>
    <div class="card">
        <h1>إعداد قاعدة البيانات السحابية</h1>
        
        <?php if ($message): ?>
            <div class="message <?= $status ?>"><?= $message ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Host (المضيف)</label>
                <input type="text" name="host" placeholder="e.g. mysql-service.aivencloud.com" required>
            </div>
            <div class="form-group">
                <label>Port (المنفذ)</label>
                <input type="number" name="port" value="3306" required>
            </div>
            <div class="form-group">
                <label>Database Name (اسم القاعدة)</label>
                <input type="text" name="name" placeholder="defaultdb" required>
            </div>
            <div class="form-group">
                <label>Username (اسم المستخدم)</label>
                <input type="text" name="user" placeholder="avnadmin" required>
            </div>
            <div class="form-group">
                <label>Password (كلمة المرور)</label>
                <input type="password" name="pass" required>
            </div>
            <button type="submit">بدء النقل (Migrate)</button>
        </form>
        <p style="text-align:center; margin-top:1rem; font-size:0.9rem; color:#6b7280;">
            استخدم بيانات الاتصال التي حصلت عليها من Aiven أو TiDB.
        </p>
    </div>
</body>
</html>
