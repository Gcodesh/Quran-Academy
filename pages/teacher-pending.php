<?php
$page_title = 'طلبك قيد المراجعة';
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?> | منصة التميز</title>
    
    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Base Styles -->
    <link rel="stylesheet" href="../assets/css/style.css">
    
    <style>
        body {
            background-color: #f8fafc;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Tajawal', sans-serif;
            margin: 0;
        }
        
        .pending-card {
            background: white;
            padding: 50px 40px;
            border-radius: 24px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.08);
            text-align: center;
            max-width: 550px;
            width: 90%;
            position: relative;
            overflow: hidden;
        }
        
        .pending-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 6px;
            background: linear-gradient(90deg, #f59e0b, #d97706);
        }
        
        .icon-wrapper {
            width: 100px;
            height: 100px;
            background: #fffbeb;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
            position: relative;
        }
        
        .icon-wrapper i {
            font-size: 3.5rem;
            color: #f59e0b;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.1); opacity: 0.8; }
            100% { transform: scale(1); opacity: 1; }
        }
        
        h1 {
            color: #1e293b;
            font-size: 1.8rem;
            margin-bottom: 15px;
            font-weight: 800;
        }
        
        p {
            color: #64748b;
            line-height: 1.7;
            font-size: 1.1rem;
            margin-bottom: 10px;
        }
        
        .highlight {
            color: #f59e0b;
            font-weight: 700;
        }
        
        .btn-home {
            display: inline-block;
            margin-top: 30px;
            padding: 12px 30px;
            background: #f1f5f9;
            color: #475569;
            text-decoration: none;
            border-radius: 12px;
            font-weight: 600;
            transition: 0.3s;
        }
        
        .btn-home:hover {
            background: #e2e8f0;
            color: #1e293b;
            transform: translateY(-2px);
        }
        
        .checklist {
            text-align: right;
            background: #f8fafc;
            padding: 20px;
            border-radius: 12px;
            margin-top: 25px;
            border: 1px solid #e2e8f0;
        }
        
        .checklist-item {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
            color: #334155;
            font-size: 0.95rem;
        }
        
        .checklist-item i {
            color: #10b981;
        }
        
        .checklist-item:last-child { margin-bottom: 0; }
    </style>
</head>
<body>

    <div class="pending-card">
        <div class="icon-wrapper">
            <i class="fas fa-hourglass-half"></i>
        </div>
        
        <h1>تم استلام طلب الانضمام بنجاح!</h1>
        
        <p>شكراً لتسجيلك كمعلم في <strong>منصة التميز</strong>.</p>
        <p>بياناتك الآن قيد المراجعة من قبل فريق الإدارة للتأكد من صحتها واعتماد حسابك.</p>
        
        <div class="checklist">
            <div class="checklist-item">
                <i class="fas fa-check-circle"></i>
                <span>تم حفظ بياناتك الشخصية وصورة الهوية بأمان.</span>
            </div>
            <div class="checklist-item">
                <i class="fas fa-envelope"></i>
                <span>سيصلك بريد إلكتروني فور الموافقة على الحساب.</span>
            </div>
            <div class="checklist-item">
                <i class="fas fa-clock"></i>
                <span>تتم المراجعة عادةً خلال 24 ساعة.</span>
            </div>
        </div>

        <a href="../index.php" class="btn-home">
            <i class="fas fa-home"></i> العودة للرئيسية
        </a>
    </div>

</body>
</html>
