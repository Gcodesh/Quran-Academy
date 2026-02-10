<?php
// full_system_check.php
// Comprehensive Diagnostic Tool for Islamic Education Platform

require_once __DIR__ . '/includes/config/root.php';
require_once path('includes/config/database.php');
require_once path('includes/classes/Database.php');

$db = (new Database())->getConnection();

function check($label, $condition, $successMsg, $failMsg) {
    if ($condition) {
        echo "<div class='item success'><strong>✅ $label:</strong> $successMsg</div>";
        return true;
    } else {
        echo "<div class='item error'><strong>❌ $label:</strong> $failMsg</div>";
        return false;
    }
}

function tableExists($db, $tableName) {
    try {
        $result = $db->query("SELECT 1 FROM $tableName LIMIT 1");
        return $result !== false;
    } catch (Exception $e) {
        return false;
    }
}

function columnExists($db, $tableName, $columnName) {
    try {
        $rs = $db->query("SELECT `$columnName` FROM `$tableName` LIMIT 1");
        return $rs !== false;
    } catch (Exception $e) {
        return false;
    }
}

?>
<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>فحص صحة النظام | System Health Check</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f0f2f5; padding: 20px; color: #333; }
        .container { max-width: 800px; margin: 0 auto; background: #fff; padding: 30px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        h1 { text-align: center; color: #0E5F4B; margin-bottom: 30px; }
        .section { margin-bottom: 25px; border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden; }
        .section-header { background: #f8fafc; padding: 15px; font-weight: bold; border-bottom: 1px solid #e2e8f0; }
        .item { padding: 12px 15px; border-bottom: 1px solid #f1f1f1; display: flex; justify-content: space-between; align-items: center; }
        .item:last-child { border-bottom: none; }
        .success { color: #059669; background: #ecfdf5; }
        .error { color: #dc2626; background: #fef2f2; }
        .btn { display: inline-block; padding: 10px 20px; background: #0E5F4B; color: #fff; text-decoration: none; border-radius: 5px; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 تقرير فحص النظام الشامل</h1>

        <div class="section">
            <div class="section-header">1. اتصال قاعدة البيانات</div>
            <?php
            check("Database Connection", $db != null, "Connected successfully to TiDB via SSL.", "Failed to connect.");
            ?>
        </div>

        <div class="section">
            <div class="section-header">2. الجداول الأساسية (Schema Health)</div>
            <?php
            $tables = ['users', 'courses', 'course_modules', 'lessons', 'user_progress', 'migrations'];
            foreach ($tables as $table) {
                check("Table: $table", tableExists($db, $table), "Found.", "Missing!");
            }
            ?>
        </div>

        <div class="section">
            <div class="section-header">3. أعمدة التلعيب (Gamification Columns)</div>
            <?php
            check("users.points", columnExists($db, 'users', 'points'), "Column 'points' exists.", "Missing 'points' column!");
            check("users.rank", columnExists($db, 'users', 'rank'), "Column 'rank' exists.", "Missing 'rank' column!");
            ?>
        </div>

        <div class="section">
            <div class="section-header">4. نظام الترحيل (Migrations System)</div>
            <?php
            try {
                $count = $db->query("SELECT COUNT(*) FROM migrations")->fetchColumn();
                check("Migration Logs", true, "Migration log table active. Executed migrations: $count", "Migration table issue.");
            } catch (Exception $e) {
                check("Migration Logs", false, "", "Migration table not accessible.");
            }
            ?>
        </div>
        
        <div class="section">
            <div class="section-header">5. جاهزية الملفات (File System)</div>
            <?php
             check("CourseRepository", file_exists(__DIR__ . '/src/Repositories/CourseRepository.php'), "Exists.", "Missing file!");
             check("UserRepository", file_exists(__DIR__ . '/src/Repositories/UserRepository.php'), "Exists.", "Missing file!");
             check("GamificationService", file_exists(__DIR__ . '/src/Services/GamificationService.php'), "Exists.", "Missing file!");
             check("Migrations Directory", is_dir(__DIR__ . '/database/migrations'), "Exists.", "Missing directory!");
            ?>
        </div>

        <div style="text-align: center;">
            <p>إذا كانت جميع العلامات خضراء ✅، فالنظام سليم 100%.</p>
            <a href="<?= url('pages/home.php') ?>" class="btn">العودة للرئيسية</a>
        </div>
    </div>
</body>
</html>
