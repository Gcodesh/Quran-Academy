<?php
require_once __DIR__ . '/admin_middleware.php';
require_once '../../pages/dashboard/layout.php';
require_once '../../includes/config/database.php';
require_once '../../includes/classes/Database.php';

$page_title = 'التقارير والتحليلات';

render_dashboard_layout(function() {
    $db = new Database();
    $conn = $db->getConnection();
    
    // User Distribution
    $students = $conn->query("SELECT COUNT(*) FROM users WHERE role = 'student'")->fetchColumn();
    $teachers = $conn->query("SELECT COUNT(*) FROM users WHERE role = 'teacher'")->fetchColumn();
    $admins = $conn->query("SELECT COUNT(*) FROM users WHERE role = 'admin'")->fetchColumn();
    
    // Course Status
    $pub_courses = $conn->query("SELECT COUNT(*) FROM courses WHERE status = 'published'")->fetchColumn();
    $pen_courses = $conn->query("SELECT COUNT(*) FROM courses WHERE status = 'pending'")->fetchColumn();
    $drf_courses = $conn->query("SELECT COUNT(*) FROM courses WHERE status = 'draft'")->fetchColumn();
    
    // Top Courses by Enrollment
    $top_courses = $conn->query("
        SELECT c.title, COUNT(e.id) as enrollments 
        FROM courses c 
        LEFT JOIN enrollments e ON c.id = e.course_id 
        GROUP BY c.id 
        ORDER BY enrollments DESC 
        LIMIT 5
    ")->fetchAll(PDO::FETCH_ASSOC);

    // Monthly Signups (Mock data generation if empty for visual)
    $months = ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو'];
    $signups = [12, 19, 3, 5, 2, 3]; // Placeholder if no data, ideally query users created_at
?>

<div class="admin-analytics-page">
    <div class="page-header">
        <div class="header-content">
            <div class="header-title">
                <h1><i class="fas fa-chart-pie"></i> مركز التحليلات</h1>
                <p>نظرة شاملة على أداء المنصة والنمو</p>
            </div>
            <div class="date-range">
                <i class="far fa-calendar-alt"></i>
                <span>آخر 30 يوم</span>
            </div>
        </div>
    </div>

    <div class="charts-grid">
        <!-- Users Distribution -->
        <div class="chart-card">
            <h3><i class="fas fa-users"></i> توزيع المستخدمين</h3>
            <div class="chart-wrapper">
                <canvas id="usersChart"></canvas>
            </div>
        </div>

        <!-- Courses Status -->
        <div class="chart-card">
            <h3><i class="fas fa-graduation-cap"></i> توزيع الدورات</h3>
            <div class="chart-wrapper">
                <canvas id="coursesChart"></canvas>
            </div>
        </div>
        
        <!-- Growth Chart (Line) -->
        <div class="chart-card wide">
            <h3><i class="fas fa-chart-line"></i> نمو التسجيلات والطلاب</h3>
            <div class="chart-wrapper">
                <canvas id="growthChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Top Performing -->
    <div class="top-list-section glass-panel">
        <div class="section-header">
            <h3><i class="fas fa-trophy"></i> الدورات الأكثر رواجاً</h3>
        </div>
        <div class="top-courses-list">
            <?php foreach($top_courses as $index => $course): ?>
                <div class="top-item">
                    <div class="rank">#<?= $index + 1 ?></div>
                    <div class="info">
                        <h4><?= htmlspecialchars($course['title']) ?></h4>
                        <div class="bar-bg">
                            <div class="bar-fill" style="width: <?= min(100, $course['enrollments'] * 10) ?>%"></div>
                        </div>
                    </div>
                    <div class="count"><?= $course['enrollments'] ?> طالب</div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Users Pie Chart
    new Chart(document.getElementById('usersChart'), {
        type: 'doughnut',
        data: {
            labels: ['طلاب', 'معلمون', 'مدراء'],
            datasets: [{
                data: [<?= $students ?>, <?= $teachers ?>, <?= $admins ?>],
                backgroundColor: ['#3b82f6', '#10b981', '#ef4444'],
                borderWidth: 0
            }]
        },
        options: {
            cutout: '70%',
            plugins: {
                legend: { position: 'bottom', labels: { usePointStyle: true, padding: 20 } }
            }
        }
    });

    // Courses Bar Chart (Vertical)
    new Chart(document.getElementById('coursesChart'), {
        type: 'bar',
        data: {
            labels: ['منشورة', 'معلقة', 'مسودة'],
            datasets: [{
                label: 'الدورات',
                data: [<?= $pub_courses ?>, <?= $pen_courses ?>, <?= $drf_courses ?>],
                backgroundColor: ['#10b981', '#f59e0b', '#64748b'],
                borderRadius: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { beginAtZero: true, grid: { display: false } },
                x: { grid: { display: false } }
            },
            plugins: { legend: { display: false } }
        }
    });

    // Growth Line Chart
    new Chart(document.getElementById('growthChart'), {
        type: 'line',
        data: {
            labels: <?= json_encode($months) ?>,
            datasets: [{
                label: 'تسجيلات جديدة',
                data: <?= json_encode($signups) ?>,
                borderColor: '#8b5cf6',
                backgroundColor: 'rgba(139, 92, 246, 0.1)',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { grid: { borderDash: [5, 5] } }
            },
            plugins: { legend: { display: false } }
        }
    });
});
</script>

<style>
.admin-analytics-page { max-width: 1400px; margin: 0 auto; }

/* Header */
.header-content { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 30px; }
.header-title h1 { color: #1e293b; margin: 0 0 5px; font-size: 1.8rem; }
.header-title p { color: #64748b; margin: 0; }
.date-range { background: white; padding: 10px 20px; border-radius: 10px; border: 1px solid #e2e8f0; display: flex; align-items: center; gap: 8px; color: #64748b; font-weight: 600; }

/* Charts Grid */
.charts-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 25px; margin-bottom: 30px; }
.chart-card { background: white; border-radius: 20px; padding: 25px; box-shadow: 0 4px 20px rgba(0,0,0,0.04); }
.chart-card.wide { grid-column: 1 / -1; }
.chart-card h3 { margin: 0 0 20px; font-size: 1.1rem; color: #1e293b; display: flex; align-items: center; gap: 10px; }
.chart-card h3 i { color: #8b5cf6; }
.chart-wrapper { height: 250px; position: relative; }
.chart-card.wide .chart-wrapper { height: 350px; }

/* Top List */
.top-list-section { background: white; border-radius: 20px; padding: 30px; box-shadow: 0 4px 20px rgba(0,0,0,0.04); }
.section-header h3 { margin: 0 0 25px; font-size: 1.2rem; color: #1e293b; display: flex; align-items: center; gap: 10px; }
.section-header h3 i { color: #f59e0b; }

.top-item { display: flex; align-items: center; gap: 20px; padding: 15px 0; border-bottom: 1px solid #f8fafc; }
.top-item:last-child { border-bottom: none; }
.rank { font-size: 1.2rem; font-weight: 800; color: #cbd5e1; width: 40px; }
.top-item:nth-child(1) .rank { color: #f59e0b; }
.top-item:nth-child(2) .rank { color: #94a3b8; }
.top-item:nth-child(3) .rank { color: #b45309; }

.info { flex: 1; }
.info h4 { margin: 0 0 8px; color: #1e293b; font-size: 1rem; }
.bar-bg { height: 8px; background: #f1f5f9; border-radius: 10px; overflow: hidden; }
.bar-fill { height: 100%; background: linear-gradient(90deg, #8b5cf6, #c4b5fd); border-radius: 10px; }

.count { font-weight: 700; color: #64748b; font-size: 0.9rem; }

@media (max-width: 900px) {
    .charts-grid { grid-template-columns: 1fr; }
}
</style>

<?php
});
?>
