<?php
require_once '../../includes/auth_middleware.php';
checkAuth(['teacher']);

include '../../includes/components/header.php';
include '../../includes/components/dashboard_sidebar.php';

// Mock data for now, will link to DB later
$stats = [
    'students' => 156,
    'courses' => 4,
    'pending' => 1,
    'rating' => 4.8
];
?>

<main class="dashboard-content">
    <div class="container-fluid">
        <div class="dashboard-header-flex">
            <div>
                <h1>مرحباً، <?= $_SESSION['user_name'] ?> 👋</h1>
                <p>إليك نظرة سريعة على أداء دوراتك اليوم.</p>
            </div>
            <a href="add-course.php" class="btn-primary-glow">
                <i class="fas fa-plus"></i> إضافة دورة جديدة
            </a>
        </div>

        <!-- Progress Cards -->
        <div class="stats-grid">
            <div class="stat-card glass-card">
                <div class="stat-info">
                    <span class="stat-label">إجمالي الطلاب</span>
                    <h2 class="stat-value"><?= $stats['students'] ?></h2>
                </div>
                <div class="stat-icon-box blue"><i class="fas fa-users"></i></div>
            </div>
            <div class="stat-card glass-card">
                <div class="stat-info">
                    <span class="stat-label">الدورات النشطة</span>
                    <h2 class="stat-value"><?= $stats['courses'] ?></h2>
                </div>
                <div class="stat-icon-box green"><i class="fas fa-book"></i></div>
            </div>
            <div class="stat-card glass-card">
                <div class="stat-info">
                    <span class="stat-label">طلبات معلقة</span>
                    <h2 class="stat-value"><?= $stats['pending'] ?></h2>
                </div>
                <div class="stat-icon-box orange"><i class="fas fa-clock"></i></div>
            </div>
            <div class="stat-card glass-card">
                <div class="stat-info">
                    <span class="stat-label">تقييم المدرب</span>
                    <h2 class="stat-value"><?= $stats['rating'] ?></h2>
                </div>
                <div class="stat-icon-box gold"><i class="fas fa-star"></i></div>
            </div>
        </div>

        <!-- Recent Courses Table -->
        <div class="content-box glass-card mt-4">
            <div class="box-header">
                <h3>أحدث دوراتك</h3>
                <a href="manage-courses.php" class="view-all">عرض الكل</a>
            </div>
            <div class="table-responsive">
                <table class="modern-table">
                    <thead>
                        <tr>
                            <th>الدورة</th>
                            <th>الحالة</th>
                            <th>الطلاب</th>
                            <th>آخر تحديث</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="course-name-cell">
                                <img src="https://placehold.co/50x50?text=Q" alt="">
                                <span>أصول التجويد الحديثة</span>
                            </td>
                            <td><span class="badge-status published">منشور</span></td>
                            <td>84 طالب</td>
                            <td>منذ يومين</td>
                            <td class="actions-cell">
                                <button title="تعديل"><i class="fas fa-edit"></i></button>
                                <button title="إحصائيات"><i class="fas fa-chart-bar"></i></button>
                            </td>
                        </tr>
                        <!-- More rows... -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<style>
.dashboard-content {
    margin-right: 280px; /* Sidebar width */
    padding: 40px;
    background: #f8fafc;
    min-height: 100vh;
}

.dashboard-header-flex {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 40px;
}

.dashboard-header-flex h1 {
    font-size: 2rem;
    color: var(--dark-900);
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 25px;
}

.stat-card {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 30px;
    border-radius: 20px;
    background: white;
    box-shadow: var(--shadow-sm);
    transition: var(--transition);
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-md);
}

.stat-label {
    color: var(--dark-500);
    font-weight: 500;
    display: block;
    margin-bottom: 5px;
}

.stat-value {
    font-size: 1.8rem;
    font-weight: 800;
    color: var(--dark-900);
}

.stat-icon-box {
    width: 60px;
    height: 60px;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}

.blue { background: rgba(59, 130, 246, 0.1); color: #3b82f6; }
.green { background: rgba(16, 185, 129, 0.1); color: #10b981; }
.orange { background: rgba(245, 158, 11, 0.1); color: #f59e0b; }
.gold { background: rgba(251, 191, 36, 0.1); color: #fbbf24; }

.modern-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

.modern-table th {
    text-align: right;
    padding: 15px;
    color: var(--dark-500);
    font-weight: 600;
    border-bottom: 1px solid var(--light-200);
}

.modern-table td {
    padding: 20px 15px;
    border-bottom: 1px solid var(--light-100);
    color: var(--dark-800);
}

.course-name-cell {
    display: flex;
    align-items: center;
    gap: 15px;
}

.course-name-cell img {
    width: 45px;
    height: 45px;
    border-radius: 10px;
}

.badge-status {
    padding: 5px 12px;
    border-radius: 50px;
    font-size: 0.85rem;
    font-weight: 600;
}

.published { background: #dcfce7; color: #166534; }

.view-all {
    color: var(--primary-600);
    font-weight: 700;
    text-decoration: none;
}

@media (max-width: 992px) {
    .dashboard-content {
        margin-right: 0;
        padding: 20px;
    }
}
</style>

<?php include '../../includes/components/footer.php'; ?>
