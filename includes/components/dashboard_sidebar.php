<?php
$current_role = $_SESSION['user_role'] ?? '';
$page = basename($_SERVER['PHP_SELF']);

// Settings for Logo
$settings_path = __DIR__ . '/../../includes/config/site_settings.json';
$custom_logo = 'assets/images/logo_new.png';
if (file_exists($settings_path)) {
    $site_settings = json_decode(file_get_contents($settings_path), true);
    $custom_logo = $site_settings['logo_path'] ?? $custom_logo;
}
?>
<aside class="dashboard-sidebar">
    <div class="sidebar-header">
        <a href="../home.php" class="logo-wrapper-sidebar">
            <img src="../../<?= $custom_logo ?>" alt="Logo">
        </a>
        <p><?= ucfirst($current_role) ?> Panel</p>
    </div>
    <nav class="sidebar-nav">
        <ul>
            <?php if ($current_role === 'student'): ?>
                <li><a href="../dashboard/student.php" class="<?= $page == 'student.php' ? 'active' : '' ?>"><i class="fas fa-th-large"></i> لوحة التحكم</a></li>
                <li><a href="../courses.php"><i class="fas fa-search"></i> تصفح الدورات</a></li>
                <li><a href="../dashboard/messages.php" class="<?= $page == 'messages.php' ? 'active' : '' ?>"><i class="fas fa-comment-dots"></i> الرسائل</a></li>
            <?php elseif ($current_role === 'teacher'): ?>
                <li><a href="../teacher/index.php" class="<?= $page == 'index.php' ? 'active' : '' ?>"><i class="fas fa-chart-line"></i> نظرة عامة</a></li>
                <li><a href="../teacher/my-courses.php" class="<?= strpos($page, 'course') !== false ? 'active' : '' ?>"><i class="fas fa-book"></i> دوراتي</a></li>
                <li><a href="../teacher/students.php" class="<?= $page == 'students.php' ? 'active' : '' ?>"><i class="fas fa-users"></i> طلابي</a></li>
                <li><a href="../teacher/earnings.php"><i class="fas fa-wallet"></i> الأرباح</a></li>
                <li><a href="../teacher/calendar.php"><i class="far fa-calendar-alt"></i> جدولي</a></li>
                <li><a href="../teacher/messages.php"><i class="fas fa-envelope"></i> التواصل</a></li>
            <?php elseif ($current_role === 'admin'): ?>
                <li><a href="../admin/index.php" class="<?= $page == 'index.php' ? 'active' : '' ?>"><i class="fas fa-tachometer-alt"></i> الرئيسية</a></li>
                <li><a href="../admin/users.php" class="<?= $page == 'users.php' ? 'active' : '' ?>"><i class="fas fa-users-cog"></i> المستخدمين</a></li>
                <li><a href="../admin/courses.php" class="<?= $page == 'courses.php' ? 'active' : '' ?>"><i class="fas fa-graduation-cap"></i> الدورات</a></li>
                <li><a href="../admin/finance.php"><i class="fas fa-money-bill-wave"></i> المالية</a></li>
                <li><a href="../admin/analytics.php"><i class="fas fa-chart-pie"></i> التقارير</a></li>
                <li><a href="../admin/settings.php"><i class="fas fa-cogs"></i> الإعدادات</a></li>
            <?php endif; ?>
            <li class="nav-divider"></li>
            <li><a href="../home.php"><i class="fas fa-external-link-alt"></i> عرض الموقع</a></li>
            <li><a href="../logout.php" class="logout-link"><i class="fas fa-sign-out-alt"></i> تسجيل الخروج</a></li>
        </ul>
    </nav>
</aside>

<style>
.dashboard-sidebar {
    width: 280px;
    height: 100vh;
    background: linear-gradient(180deg, #0f172a 0%, #020617 100%);
    color: #f8fafc;
    position: fixed;
    right: 0;
    top: 0;
    z-index: 1100;
    padding: 40px 20px;
    box-shadow: -10px 0 40px rgba(0,0,0,0.2);
    border-left: 1px solid rgba(255,255,255,0.05);
    transition: var(--dash-transition);
}

.sidebar-header {
    text-align: center;
    margin-bottom: 50px;
}
.sidebar-header .logo-wrapper-sidebar {
    display: inline-block;
    background: rgba(255, 255, 255, 0.9);
    padding: 10px;
    border-radius: 15px;
    margin-bottom: 10px;
    transition: 0.3s;
}
.sidebar-header .logo-wrapper-sidebar:hover {
    transform: scale(1.05);
    background: #fff;
}
.sidebar-header img {
    width: 140px;
    height: auto;
    max-height: 60px;
    object-fit: contain;
    filter: brightness(1.05) contrast(1.1);
}

.sidebar-header h3 {
    color: white;
    font-weight: 800;
    margin-bottom: 8px;
    letter-spacing: 1px;
    background: linear-gradient(to left, #2dd4bf, #38bdf8);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

.sidebar-header p {
    font-size: 0.75rem;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 2px;
}

.sidebar-nav ul {
    list-style: none;
    padding: 0;
}

.sidebar-nav li {
    margin-bottom: 8px;
}

.sidebar-nav a {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 14px 20px;
    border-radius: 16px;
    color: #94a3b8;
    text-decoration: none;
    font-weight: 600;
    font-size: 0.95rem;
    transition: all 0.3s ease;
}

.sidebar-nav a:hover, .sidebar-nav a.active {
    background: rgba(56, 189, 248, 0.1);
    color: #38bdf8;
    box-shadow: inset 3px 0 0 #38bdf8;
}

.sidebar-nav a.active {
    background: rgba(56, 189, 248, 0.15);
}

.sidebar-nav i {
    font-size: 1.15rem;
    width: 28px;
    text-align: center;
    transition: transform 0.3s ease;
}

.sidebar-nav a:hover i {
    transform: translateX(-3px);
}

.nav-divider {
    height: 1px;
    background: rgba(255, 255, 255, 0.05);
    margin: 25px 0;
}

.logout-link:hover {
    background: rgba(239, 68, 68, 0.1) !important;
    color: #ef4444 !important;
    box-shadow: inset 3px 0 0 #ef4444 !important;
}

@media (max-width: 992px) {
    .dashboard-sidebar {
        transform: translateX(100%);
    }
    .dashboard-sidebar.active {
        transform: translateX(0);
    }
}
</style>
