<?php
// Function to render the dashboard layout
function render_dashboard_layout($content_callback) {
    if (session_status() === PHP_SESSION_NONE) session_start();
    
    // Ensure user is logged in
    if (!isset($_SESSION['user_id'])) {
        header('Location: ../../pages/login.php');
        exit;
    }

    $page_title = $GLOBALS['page_title'] ?? 'لوحة التحكم';
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php
    $settings_path = __DIR__ . '/../../includes/config/site_settings.json';
    $site_name = 'بِنَاءُ الْمُسْلِمِ';
    $custom_favicon = 'assets/images/favicon_new.png';
    if (file_exists($settings_path)) {
        $site_settings = json_decode(file_get_contents($settings_path), true);
        $site_name = $site_settings['site_name'] ?? $site_name;
        $custom_favicon = $site_settings['favicon_path'] ?? $custom_favicon;
    }
    ?>
    <title><?= $page_title ?> | <?= $site_name ?></title>
    <link rel="icon" type="image/png" href="../../<?= $custom_favicon ?>">
    <link rel="shortcut icon" href="../../<?= $custom_favicon ?>">
    
    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Base Styles -->
    <!-- Base Styles -->
    <link rel="stylesheet" href="../../assets/css/main.css">
    <link rel="stylesheet" href="../../assets/css/dashboard.css">
    <link rel="stylesheet" href="../../assets/css/responsive.css">
    
    <style>
        :root {
            --sidebar-width: 280px;
            --navbar-height: 80px;
        }

        body {
            background-color: var(--light-50);
            min-height: 100vh;
        }

        .dashboard-container {
            padding-right: var(--sidebar-width);
            padding-top: var(--navbar-height);
            min-height: 100vh;
            transition: var(--transition);
        }

        .dashboard-content {
            padding: 30px;
            width: 100%;
        }

        /* Responsive Dashboard */
        @media (max-width: 992px) {
            .dashboard-container {
                padding-right: 0;
            }
        }
        
        /* Dashboard Card Styles */
        .dash-card {
            background: white;
            border-radius: 20px;
            padding: 25px;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--light-200);
            transition: var(--transition);
        }

        .dash-card:hover {
            box-shadow: var(--shadow-md);
            transform: translateY(-2px);
        }

        .dash-stat-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <?php include __DIR__ . '/../../includes/components/dashboard_sidebar.php'; ?>

    <!-- Navbar -->
    <?php include __DIR__ . '/../../includes/components/dashboard_navbar.php'; ?>

    <!-- Main Content -->
    <main class="dashboard-container">
        <div class="dashboard-content">
            <?php call_user_func($content_callback); ?>
        </div>
    </main>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Global Dashboard Scripts
    </script>
</body>
</html>
<?php
}
?>
