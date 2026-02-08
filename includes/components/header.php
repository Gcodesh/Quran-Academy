<?php
// Header component
if (session_status() === PHP_SESSION_NONE) session_start();

// Determine base path for assets based on directory depth
$is_subfolder = (strpos($_SERVER['PHP_SELF'], '/dashboard/') !== false || strpos($_SERVER['PHP_SELF'], '/admin/') !== false || strpos($_SERVER['PHP_SELF'], '/teacher/') !== false);
$base_path = $is_subfolder ? '../../' : '../';
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php
    $settings_path = __DIR__ . '/../config/site_settings.json';
    $custom_logo = 'assets/images/logo_new.png';
    $custom_favicon = 'assets/images/favicon_new.png';
    $site_name = 'بِنَاءُ الْمُسْلِمِ';
    if (file_exists($settings_path)) {
        $site_settings = json_decode(file_get_contents($settings_path), true);
        $custom_logo = $site_settings['logo_path'] ?? $custom_logo;
        $custom_favicon = $site_settings['favicon_path'] ?? $custom_favicon;
        $site_name = $site_settings['site_name'] ?? $site_name;
    }
    ?>
    <title><?= htmlspecialchars($site_name) ?> | عِلْمٌ يَبْنِي مُسْلِم</title>
    <link rel="icon" type="image/png" href="<?= $base_path ?><?= $custom_favicon ?>">
    <link rel="shortcut icon" href="<?= $base_path ?><?= $custom_favicon ?>">
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&family=Tajawal:wght@400;500;700;800&family=IBM+Plex+Sans+Arabic:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- AOS Animation Library -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <!-- CSS Files -->
    <link rel="stylesheet" href="<?= $base_path ?>assets/css/main.css">
    <link rel="stylesheet" href="<?= $base_path ?>assets/css/components/cards.css">
    <link rel="stylesheet" href="<?= $base_path ?>assets/css/responsive.css">
    <?php if ($is_subfolder): ?>
    <link rel="stylesheet" href="<?= $base_path ?>assets/css/dashboard.css">
    <?php endif; ?>
</head>
<body class="perf-optimized">
    <div class="scroll-progress" id="scrollProgress"></div>
    
    <?php if (!$is_subfolder): ?>
    <header class="site-header">
        <div class="container">
            <div class="logo">
                <a href="<?= $base_path ?>pages/home.php">
                    <img src="<?= $base_path ?><?= $custom_logo ?>" alt="Logo" width="150" height="50" style="object-fit: contain;" onerror="this.src='https://placehold.co/150x50?text=Logo'">
                </a>
            </div>
            <nav class="main-nav" id="mainNav">
                <ul>
                    <li><a href="<?= $base_path ?>pages/home.php">الرئيسية</a></li>
                    <li><a href="<?= $base_path ?>pages/courses.php">الدورات</a></li>
                    <li><a href="<?= $base_path ?>pages/teachers.php">المعلمون</a></li>
                    <li><a href="<?= $base_path ?>pages/about.php">من نحن</a></li>
                    <li><a href="<?= $base_path ?>pages/contact.php">اتصل بنا</a></li>
                </ul>
            </nav>
            
            <button id="themeToggle" class="theme-toggle-btn" aria-label="Toggle Theme">
                <i class="fas fa-moon moon-icon"></i>
                <i class="fas fa-sun sun-icon"></i>
            </button>

            <div class="quick-auth">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <?php 
                        $dashboard_link = $base_path . 'pages/';
                        if ($_SESSION['user_role'] == 'admin') $dashboard_link .= 'admin/index.php';
                        elseif ($_SESSION['user_role'] == 'teacher') $dashboard_link .= 'teacher/index.php';
                        else $dashboard_link .= 'dashboard/student.php';
                    ?>
                    <a href="<?= $dashboard_link ?>" class="btn-primary" style="background: var(--light-bg); color: var(--primary-color); border: 1px solid var(--primary-color);">
                        <i class="fas fa-user-circle"></i> مرحباً، <?= htmlspecialchars(explode(' ', $_SESSION['user_name'])[0]) ?>
                    </a>
                    <a href="<?= $base_path ?>pages/logout.php" class="btn-outline" style="margin-right: 10px;">خروج</a>
                <?php else: ?>
                    <a href="<?= $base_path ?>pages/login.php" class="btn-outline">تسجيل الدخول</a>
                    <a href="<?= $base_path ?>pages/register.php" class="btn-primary">انضم إلينا</a>
                <?php endif; ?>
            </div>

            <div class="mobile-menu-toggle" id="menuToggle">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
    </header>
    <?php endif; ?>

    <!-- Breadcrumb Handling -->
    <?php if (basename($_SERVER['PHP_SELF']) != 'home.php' && strpos($_SERVER['PHP_SELF'], '/dashboard/') === false && strpos($_SERVER['PHP_SELF'], '/admin/') === false): ?>
    <nav class="breadcrumb" aria-label="مسار التنقل">
        <div class="container">
            <ol>
                <li><a href="home.php">الرئيسية</a></li>
                <li>
                    <?php 
                        $current_page = basename($_SERVER['PHP_SELF']);
                        if($current_page == 'courses.php') echo 'الدورات';
                        elseif($current_page == 'course-details.php') echo 'تفاصيل الدورة';
                        elseif($current_page == 'teachers.php') echo 'المعلمين';
                        else echo 'الصفحة الحالية';
                    ?>
                </li>
            </ol>
        </div>
    </nav>
    <div class="section-divider"></div>
    <?php endif; ?>
