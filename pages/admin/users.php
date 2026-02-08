<?php
require_once __DIR__ . '/admin_middleware.php';
require_once '../../pages/dashboard/layout.php';
require_once '../../includes/config/database.php';
require_once '../../includes/classes/Database.php';

$page_title = 'إدارة المستخدمين';

ob_start();

render_dashboard_layout(function() {
    $db = new Database();
    $conn = $db->getConnection();
    
    // Actions Logic
    $message = '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
        $user_id = $_POST['user_id'];
        $new_status = '';
        
        switch ($_POST['action']) {
            case 'activate': $new_status = 'active'; break;
            case 'suspend': $new_status = 'suspended'; break;
            case 'ban': $new_status = 'banned'; break;
        }

        if ($new_status) {
            $stmt = $conn->prepare("UPDATE users SET status = ? WHERE id = ?");
            if($stmt->execute([$new_status, $user_id])) {
                $message = 'تم تحديث حالة المستخدم بنجاح';
            }
        }
    }

    // Pagination & Filter Logic
    $page = $_GET['page'] ?? 1;
    $limit = 10;
    $offset = ($page - 1) * $limit;
    $role_filter = $_GET['role'] ?? 'all';
    $status_filter = $_GET['status'] ?? 'all';
    $search = $_GET['q'] ?? '';
    
    $where = ["1=1"];
    $params = [];
    if ($role_filter != 'all') { $where[] = "role = ?"; $params[] = $role_filter; }
    if ($status_filter != 'all') { $where[] = "status = ?"; $params[] = $status_filter; }
    if ($search) { $where[] = "(name LIKE ? OR email LIKE ?)"; $params[] = "%$search%"; $params[] = "%$search%"; }
    
    $where_sql = implode(' AND ', $where);
    $total = $conn->prepare("SELECT COUNT(*) FROM users WHERE $where_sql");
    $total->execute($params);
    $total = $total->fetchColumn();
    $pages = ceil($total / $limit);
    
    $stmt = $conn->prepare("SELECT * FROM users WHERE $where_sql ORDER BY created_at DESC LIMIT $limit OFFSET $offset");
    $stmt->execute($params);
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

    <!-- Link the specific CSS for this page -->
    <link rel="stylesheet" href="../../assets/css/admin-users.css?v=<?= time() ?>">

    <div class="admin-users-page">
        
        <!-- Hero Header -->
        <div class="users-hero">
            <div class="hero-content">
                <h1><i class="fas fa-users"></i> عائلة المنصة</h1>
                <p>لديك <strong><?= $total ?></strong> مستخدم مسجل. تحكم بصلاحياتهم وحالاتهم من هنا.</p>
            </div>
            <div class="hero-actions">
                <a href="add-user.php" class="btn-create-user">
                    <i class="fas fa-plus-circle"></i> إضافة مستخدم
                </a>
            </div>
        </div>

        <!-- Filters Area -->
        <div class="search-filters-container">
            <form method="GET" class="filters-wrapper">
                <div class="search-input-group">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" name="q" value="<?= htmlspecialchars($search) ?>" placeholder="ابحث باسم المستخدم أو البريد الإلكتروني...">
                </div>
                
                <div class="filters-group">
                    <div class="custom-dropdown">
                        <i class="fas fa-filter"></i>
                        <select name="role" onchange="this.form.submit()">
                            <option value="all" <?= $role_filter == 'all' ? 'selected' : '' ?>>جميع الأدوار</option>
                            <option value="student" <?= $role_filter == 'student' ? 'selected' : '' ?>>الطلاب</option>
                            <option value="teacher" <?= $role_filter == 'teacher' ? 'selected' : '' ?>>المعلمون</option>
                            <option value="admin" <?= $role_filter == 'admin' ? 'selected' : '' ?>>المدراء</option>
                        </select>
                    </div>

                    <div class="custom-dropdown">
                        <i class="fas fa-info-circle"></i>
                        <select name="status" onchange="this.form.submit()">
                            <option value="all" <?= $status_filter == 'all' ? 'selected' : '' ?>>جميع الحالات</option>
                            <option value="active" <?= $status_filter == 'active' ? 'selected' : '' ?>>نشط</option>
                            <option value="pending" <?= $status_filter == 'pending' ? 'selected' : '' ?>>معلق</option>
                            <option value="suspended" <?= $status_filter == 'suspended' ? 'selected' : '' ?>>موقوف</option>
                            <option value="banned" <?= $status_filter == 'banned' ? 'selected' : '' ?>>محظور</option>
                        </select>
                    </div>
                </div>
            </form>
        </div>

        <?php if($message): ?>
            <div class="notification-toast">
                <i class="fas fa-check-circle"></i> <?= $message ?>
            </div>
        <?php endif; ?>

        <!-- Users List (Floating Rows Layout) -->
        <div class="users-list-container">
            <?php if (empty($users)): ?>
                <div class="empty-state-card" style="text-align:center; padding:50px;">
                    <h3>لم نعثر على أي مستخدم</h3>
                    <p>جرب تغيير شروط البحث</p>
                    <a href="users.php" class="btn-reset" style="color:#8b5cf6;">إعادة تعيين</a>
                </div>
            <?php else: ?>
                <div class="users-header-row">
                    <div class="col-user">المستخدم</div>
                    <div class="col-role">الدور</div>
                    <div class="col-status">الحالة</div>
                    <div class="col-date">انضم منذ</div>
                    <div class="col-actions">تحكم</div>
                </div>

                <?php foreach ($users as $user): ?>
                <div class="user-card-row">
                    <!-- User Info -->
                    <div class="col-user">
                        <div class="avatar-circle" style="background: <?= getColorFromString($user['name']) ?>">
                            <?= $user['avatar'] ? '<img src="../../'.$user['avatar'].'" style="width:100%;height:100%;border-radius:14px;object-fit:cover;">' : mb_substr($user['name'], 0, 1, 'UTF-8') ?>
                        </div>
                        <div class="user-text">
                            <span class="user-name"><?= htmlspecialchars($user['name']) ?></span>
                            <span class="user-email"><?= htmlspecialchars($user['email']) ?></span>
                            <?php if(!empty($user['phone'])): ?>
                                <span style="font-size:0.8rem; color:#94a3b8;"><i class="fas fa-phone-alt"></i> <?= htmlspecialchars($user['phone']) ?></span>
                            <?php endif; ?>
                            <?php if(!empty($user['country'])): ?>
                                <span style="font-size:0.8rem; color:#94a3b8;"><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($user['country']) . ' - ' . htmlspecialchars($user['city']) ?></span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Role -->
                    <div class="col-role">
                        <?php 
                            $role_icons = ['admin' => 'crown', 'teacher' => 'chalkboard-teacher', 'student' => 'user-graduate'];
                            $role_ar = ['admin' => 'مدير', 'teacher' => 'معلم', 'student' => 'طالب'];
                        ?>
                        <div class="role-chip <?= $user['role'] ?>">
                            <i class="fas fa-<?= $role_icons[$user['role']] ?>"></i>
                            <?= $role_ar[$user['role']] ?>
                        </div>
                    </div>

                    <!-- Status -->
                    <div class="col-status">
                        <div class="status-indicator <?= $user['status'] ?>">
                            <span class="pulse"></span>
                            <?= [
                                'active' => 'نشط', 'pending' => 'معلق', 
                                'suspended' => 'موقوف', 'banned' => 'محظور'
                            ][$user['status']] ?>
                        </div>
                    </div>

                    <!-- Date -->
                    <div class="col-date">
                        <span class="date-badge">
                            <?= date('d M Y', strtotime($user['created_at'])) ?>
                        </span>
                    </div>

                    <!-- Actions -->
                    <div class="col-actions">
                        <div class="action-buttons">
                            <?php if(!empty($user['id_card_path'])): ?>
                                <a href="../../<?= htmlspecialchars($user['id_card_path']) ?>" target="_blank" class="btn-icon" title="عرض الهوية" style="color:#f59e0b; background:#fffbeb;">
                                    <i class="fas fa-id-card"></i>
                                </a>
                            <?php endif; ?>

                            <a href="edit-user.php?id=<?= $user['id'] ?>" class="btn-icon edit" title="تعديل">
                                <i class="fas fa-pen"></i>
                            </a>
                            
                            <?php if($user['id'] != $_SESSION['user_id']): ?>
                                <div class="dropdown-trigger">
                                    <button class="btn-icon more"><i class="fas fa-ellipsis-v"></i></button>
                                    <div class="dropdown-menu">
                                        <?php if($user['status'] !== 'active'): ?>
                                            <form method="POST">
                                                <input type="hidden" name="action" value="activate">
                                                <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                                <button type="submit" class="drop-item success"><i class="fas fa-check"></i> تفعيل الحساب</button>
                                            </form>
                                        <?php else: ?>
                                            <form method="POST">
                                                <input type="hidden" name="action" value="suspend">
                                                <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                                <button type="submit" class="drop-item warning"><i class="fas fa-pause"></i> إيقاف مؤقت</button>
                                            </form>
                                            <form method="POST" onsubmit="return confirm('حظر نهائي؟')">
                                                <input type="hidden" name="action" value="ban">
                                                <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                                <button type="submit" class="drop-item danger"><i class="fas fa-ban"></i> حظر نهائي</button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>

                <!-- Pagination -->
                <?php if ($pages > 1): ?>
                    <div class="pagination-container">
                        <?php for($i = 1; $i <= $pages; $i++): ?>
                            <a href="?page=<?= $i ?>" class="page-pill <?= $page == $i ? 'active' : '' ?>"><?= $i ?></a>
                        <?php endfor; ?>
                    </div>
                <?php endif; ?>

            <?php endif; ?>
        </div>
    </div>

<?php 
});

function getColorFromString($str) {
    $colors = ['#6366f1', '#8b5cf6', '#ec4899', '#f43f5e', '#f59e0b', '#10b981', '#06b6d4', '#3b82f6'];
    return $colors[crc32($str) % count($colors)];
}

ob_end_flush();
?>
