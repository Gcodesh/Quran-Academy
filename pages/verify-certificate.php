<?php
require_once '../../includes/config/database.php';
require_once '../../includes/classes/Database.php';
require_once '../../src/Services/CertificateGenerator.php';

use App\Services\CertificateGenerator;

$hash = $_GET['hash'] ?? null;
$db = new Database();
$conn = $db->getConnection();
$certService = new CertificateGenerator($conn);

$cert = $hash ? $certService->verifyCertificate($hash) : null;
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>توثيق الشهادة | منصة التميز</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #0f766e;
            --gold: #d97706;
            --bg: #f8fafc;
        }
        body { font-family: 'Tajawal', sans-serif; background: var(--bg); display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; padding: 20px; }
        .verify-card { background: white; border-radius: 30px; box-shadow: 0 20px 50px rgba(0,0,0,0.05); width: 100%; max-width: 600px; padding: 50px; text-align: center; position: relative; overflow: hidden; }
        .verify-card::before { content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 8px; background: linear-gradient(90deg, var(--primary), var(--gold)); }
        
        .status-icon { width: 80px; height: 80px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 30px; font-size: 2.5rem; }
        .success-icon { background: #dcfce7; color: #166534; }
        .fail-icon { background: #fee2e2; color: #991b1b; }
        
        h1 { color: #1e293b; margin-bottom: 10px; font-size: 1.8rem; }
        .subtitle { color: #64748b; margin-bottom: 40px; }
        
        .info-grid { display: grid; gap: 20px; text-align: right; background: #f8fafc; padding: 30px; border-radius: 20px; border: 1px solid #e2e8f0; }
        .info-row { display: flex; justify-content: space-between; border-bottom: 1px solid #e2e8f0; padding-bottom: 10px; }
        .info-row:last-child { border: none; padding: 0; }
        .label { color: #64748b; font-weight: 500; }
        .value { color: #1e293b; font-weight: 700; }
        
        .gold-badge { display: inline-flex; align-items: center; gap: 8px; background: #fef3c7; color: #92400e; padding: 6px 15px; border-radius: 50px; font-weight: 700; font-size: 0.85rem; margin-top: 15px; border: 1px solid #fcd34d; }
        
        .btn-home { display: inline-block; margin-top: 40px; background: var(--primary); color: white; text-decoration: none; padding: 12px 30px; border-radius: 12px; font-weight: 700; transition: 0.3s; }
        .btn-home:hover { transform: translateY(-2px); box-shadow: 0 10px 20px rgba(15, 118, 110, 0.2); }
    </style>
</head>
<body>

<div class="verify-card">
    <?php if ($cert): ?>
        <div class="status-icon success-icon">
            <i class="fas fa-check-double"></i>
        </div>
        <h1>تم توثيق الشهادة بنجاح</h1>
        <p class="subtitle">هذه الشهادة رسمية وصادرة من منصة التميز للتعليم الإسلامي</p>
        
        <div class="info-grid">
            <div class="info-row">
                <span class="label">اسم الطالب:</span>
                <span class="value"><?= htmlspecialchars($cert['student_name']) ?></span>
            </div>
            <div class="info-row">
                <span class="label">الدورة التدريبية:</span>
                <span class="value"><?= htmlspecialchars($cert['course_title']) ?></span>
            </div>
            <div class="info-row">
                <span class="label">تاريخ الإصدار:</span>
                <span class="value"><?= date('Y/m/d', strtotime($cert['issue_date'])) ?></span>
            </div>
            <div class="info-row">
                <span class="label">الدرجة المكتسبة:</span>
                <span class="value"><?= number_format($cert['score_percent'], 1) ?>%</span>
            </div>
            <div class="info-row">
                <span class="label">رقم التوثيق:</span>
                <span class="value" style="font-family: monospace; font-size: 0.8rem;"><?= $cert['cert_hash'] ?></span>
            </div>
        </div>

        <?php if ($cert['is_gold']): ?>
            <div class="gold-badge">
                <i class="fas fa-award"></i> وسام التميز الذهبي
            </div>
        <?php endif; ?>

    <?php else: ?>
        <div class="status-icon fail-icon">
            <i class="fas fa-times-circle"></i>
        </div>
        <h1>عذراً، الشهادة غير صحيحة</h1>
        <p class="subtitle">لم يتم العثور على سجل لهذه الشهادة في قاعدة بياناتنا.</p>
        <p style="color: #64748b; font-size: 0.9rem;">يرجى التأكد من الرابط أو مسح الكود مرة أخرى.</p>
    <?php endif; ?>

    <a href="../home.php" class="btn-home">العودة للمنصة</a>
</div>

</body>
</html>
