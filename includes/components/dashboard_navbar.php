<?php
// Get user info from session
$user_name = $_SESSION['user_name'] ?? 'User';
$user_role = $_SESSION['user_role'] ?? 'guest';
$avatar_initial = mb_substr($user_name, 0, 1, "UTF-8");
?>
<header class="dashboard-navbar">
    <div class="navbar-left">
        <button class="toggle-sidebar" id="sidebarToggle">
            <i class="fas fa-bars"></i>
        </button>
        <div class="search-bar">
            <i class="fas fa-search"></i>
            <input type="text" placeholder="بحث سريع...">
        </div>
    </div>

    <div class="navbar-right">
        <?php
        // Dynamic Notifications Logic
        $notif_count = 0;
        require_once __DIR__ . '/../../includes/config/database.php';
        require_once __DIR__ . '/../../includes/classes/Database.php';
        
        try {
            $db = (new Database())->getConnection();
            if ($user_role === 'admin') {
                // Count pending courses for admin
                $stmt = $db->query("SELECT COUNT(*) FROM courses WHERE status = 'pending'");
                $notif_count = $stmt->fetchColumn();
            } elseif ($user_role === 'teacher') {
                // Count new enrollments or pending courses for teacher
                $stmt = $db->prepare("SELECT COUNT(*) FROM courses WHERE teacher_id = ? AND status = 'rejected'");
                $stmt->execute([$_SESSION['user_id']]);
                $notif_count = $stmt->fetchColumn();
            }
        } catch (Exception $e) { $notif_count = 0; }
        ?>
        <button id="themeToggle" class="theme-toggle-btn" aria-label="Toggle Theme" style="margin-right: 15px;">
            <i class="fas fa-moon moon-icon"></i>
            <i class="fas fa-sun sun-icon"></i>
        </button>

        <div class="nav-item dropdown notifications">
            <button class="icon-btn">
                <i class="far fa-bell"></i>
                <?php if ($notif_count > 0): ?>
                    <span class="badge"><?= $notif_count ?></span>
                <?php endif; ?>
            </button>
        </div>

        <div class="nav-item dropdown profile">
            <button class="profile-btn">
                <div class="avatar"><?= $avatar_initial ?></div>
                <div class="user-info">
                    <span class="name"><?= htmlspecialchars($user_name) ?></span>
                    <span class="role"><?= $user_role == 'admin' ? 'مدير النظام' : ($user_role == 'teacher' ? 'مُعلم' : 'طالب') ?></span>
                </div>
                <i class="fas fa-chevron-down"></i>
            </button>
        </div>
    </div>
</header>
<style>
.dashboard-navbar {
    background: var(--bg-surface);
    backdrop-filter: var(--glass-blur);
    -webkit-backdrop-filter: var(--glass-blur);
    padding: 0 40px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: var(--shadow-sm);
    position: fixed;
    top: 0;
    right: 280px; 
    left: 0;
    z-index: 1000;
    height: 90px;
    border-bottom: 1px solid var(--border-main);
    transition: var(--dash-transition);
}

.navbar-left {
    display: flex;
    align-items: center;
    gap: 20px;
}

.toggle-sidebar {
    background: none;
    border: none;
    font-size: 1.2rem;
    color: var(--text-muted);
    cursor: pointer;
    display: none; /* Show only on mobile/tablet */
}

.search-bar {
    position: relative;
    width: 300px;
}

.search-bar input {
    width: 100%;
    padding: 10px 40px 10px 15px;
    border-radius: 50px;
    border: 1px solid var(--border-main);
    background: var(--bg-accent);
    color: var(--text-main);
    font-family: inherit;
    transition: all 0.3s ease;
}

.search-bar input:focus {
    background: var(--bg-surface);
    border-color: var(--primary-300);
    box-shadow: 0 0 0 3px var(--shadow-glow);
    outline: none;
}

.search-bar i {
    position: absolute;
    right: 15px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--text-muted);
}

.navbar-right {
    display: flex;
    align-items: center;
    gap: 20px;
}

.icon-btn {
    background: none;
    border: none;
    position: relative;
    cursor: pointer;
    color: var(--text-muted);
    font-size: 1.2rem;
    padding: 8px;
    border-radius: 50%;
    transition: background 0.3s;
}

.icon-btn:hover {
    background: var(--bg-accent);
    color: var(--primary-600);
}

.badge {
    position: absolute;
    top: -2px;
    right: -2px;
    background: #ef4444;
    color: white;
    font-size: 0.6rem;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 2px solid var(--bg-surface);
}

.profile-btn {
    background: none;
    border: none;
    display: flex;
    align-items: center;
    gap: 12px;
    cursor: pointer;
    padding: 5px 10px;
    border-radius: 12px;
    transition: background 0.3s;
}

.profile-btn:hover {
    background: var(--bg-accent);
}

.avatar {
    width: 40px;
    height: 40px;
    background: var(--gradient-primary);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 1.1rem;
}

.user-info {
    text-align: right;
    display: flex;
    flex-direction: column;
}

.user-info .name {
    font-weight: 700;
    font-size: 0.9rem;
    color: var(--text-main);
}

.user-info .role {
    font-size: 0.75rem;
    color: var(--text-muted);
}

@media (max-width: 992px) {
    .dashboard-navbar {
        right: 0;
    }
    .toggle-sidebar {
        display: block;
    }
    .search-bar {
        display: none; /* Hide on mobile for space */
    }
    .user-info {
        display: none;
    }
}
</style>
<script>
document.getElementById('sidebarToggle').addEventListener('click', function() {
    document.querySelector('.dashboard-sidebar').classList.toggle('active');
});
</script>
