<?php
require_once __DIR__ . '/admin_middleware.php';
require_once '../../pages/dashboard/layout.php';
require_once '../../includes/config/database.php';
require_once '../../includes/classes/Database.php';

$page_title = 'المالية';

render_dashboard_layout(function() {
    $db = new Database();
    $conn = $db->getConnection();
    
    // Stats
    $total_revenue = $conn->query("SELECT SUM(amount) FROM payments WHERE status = 'completed'")->fetchColumn() ?: 0;
    
    $current_month = date('Y-m');
    $monthly_revenue = $conn->query("SELECT SUM(amount) FROM payments WHERE status = 'completed' AND DATE_FORMAT(created_at, '%Y-%m') = '$current_month'")->fetchColumn() ?: 0;
    
    $completed_txns = $conn->query("SELECT COUNT(*) FROM payments WHERE status = 'completed'")->fetchColumn();
    $pending_txns = $conn->query("SELECT COUNT(*) FROM payments WHERE status = 'pending'")->fetchColumn();
    
    // Recent Transactions
    $txns = $conn->query("
        SELECT p.*, u.full_name as user_name, c.title as course_title 
        FROM payments p 
        LEFT JOIN users u ON p.user_id = u.id 
        LEFT JOIN courses c ON p.course_id = c.id 
        ORDER BY p.created_at DESC 
        LIMIT 10
    ")->fetchAll(PDO::FETCH_ASSOC);

    // Chart Data (Last 6 Months)
    $months = [];
    $revenues = [];
    for ($i = 5; $i >= 0; $i--) {
        $date = date('Y-m', strtotime("-$i months"));
        $months[] = date('M Y', strtotime("-$i months"));
        $rev = $conn->query("SELECT SUM(amount) FROM payments WHERE status = 'completed' AND DATE_FORMAT(created_at, '%Y-%m') = '$date'")->fetchColumn() ?: 0;
        $revenues[] = $rev;
    }
?>

<div class="admin-finance-page">
    <div class="page-header">
        <div class="header-content">
            <div class="header-title">
                <h1><i class="fas fa-money-bill-wave"></i> المالية والأرباح</h1>
                <p>متابعة الإيرادات وتفاصيل المعاملات المالية</p>
            </div>
            <button onclick="window.print()" class="print-btn">
                <i class="fas fa-print"></i>
                طباعة تقرير
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="finance-stats">
        <div class="stat-card revenue">
            <div class="stat-icon"><i class="fas fa-coins"></i></div>
            <div class="stat-info">
                <h3><?= number_format($total_revenue, 2) ?> <small>ر.س</small></h3>
                <p>إجمالي الأرباح</p>
            </div>
            <div class="stat-trend positive">
                <i class="fas fa-arrow-up"></i> تراكمي
            </div>
        </div>

        <div class="stat-card monthly">
            <div class="stat-icon"><i class="fas fa-calendar-check"></i></div>
            <div class="stat-info">
                <h3><?= number_format($monthly_revenue, 2) ?> <small>ر.س</small></h3>
                <p>أرباح هذا الشهر</p>
            </div>
        </div>

        <div class="stat-card transactions">
            <div class="stat-icon"><i class="fas fa-receipt"></i></div>
            <div class="stat-info">
                <h3><?= number_format($completed_txns) ?></h3>
                <p>عمليات ناجحة</p>
            </div>
        </div>

        <div class="stat-card pending">
            <div class="stat-icon"><i class="fas fa-hourglass-half"></i></div>
            <div class="stat-info">
                <h3><?= number_format($pending_txns) ?></h3>
                <p>عمليات معلقة</p>
            </div>
        </div>
    </div>

    <div class="content-grid">
        <!-- Revenue Chart -->
        <div class="chart-section glass-panel">
            <div class="section-header">
                <h3><i class="fas fa-chart-line"></i> نمو الإيرادات</h3>
            </div>
            <div class="chart-container">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        <!-- Recent Transactions -->
        <div class="transactions-section glass-panel">
            <div class="section-header">
                <h3><i class="fas fa-history"></i> أحدث العمليات</h3>
                <a href="#" class="view-all">عرض الكل</a>
            </div>
            
            <?php if(empty($txns)): ?>
                <div class="empty-list">
                    <p>لا توجد مدفوعات مسجلة بعد</p>
                </div>
            <?php else: ?>
                <div class="transactions-list">
                    <?php foreach($txns as $txn): ?>
                    <div class="txn-item">
                        <div class="txn-icon <?= $txn['status'] ?>">
                            <i class="fas fa-<?= $txn['status'] == 'completed' ? 'check' : 'clock' ?>"></i>
                        </div>
                        <div class="txn-details">
                            <span class="txn-user"><?= htmlspecialchars($txn['user_name'] ?? 'مستخدم محذوف') ?></span>
                            <span class="txn-course"><?= htmlspecialchars($txn['course_title'] ?? 'دورة محذوفة') ?></span>
                            <span class="txn-date"><?= date('Y/m/d H:i', strtotime($txn['created_at'])) ?></span>
                        </div>
                        <div class="txn-amount <?= $txn['status'] ?>">
                            <?= number_format($txn['amount'], 2) ?> ر.س
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('revenueChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?= json_encode($months) ?>,
            datasets: [{
                label: 'الإيرادات (ر.س)',
                data: <?= json_encode($revenues) ?>,
                borderColor: '#8b5cf6',
                backgroundColor: 'rgba(139, 92, 246, 0.1)',
                borderWidth: 3,
                tension: 0.4,
                fill: true,
                pointBackgroundColor: '#ffffff',
                pointBorderColor: '#8b5cf6',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: '#f1f5f9' }
                },
                x: {
                    grid: { display: false }
                }
            }
        }
    });
});
</script>

<style>
.admin-finance-page { max-width: 1400px; margin: 0 auto; }

/* Header */
.header-content { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 30px; }
.header-title h1 { color: #1e293b; margin: 0 0 5px; font-size: 1.8rem; }
.header-title p { color: #64748b; margin: 0; }
.print-btn { background: white; border: 1px solid #e2e8f0; padding: 10px 20px; border-radius: 10px; cursor: pointer; color: #475569; font-weight: 600; display: flex; align-items: center; gap: 8px; transition: 0.2s; }
.print-btn:hover { background: #f8fafc; color: #1e293b; }

/* Stats Cards */
.finance-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px; margin-bottom: 30px; }
.stat-card { background: white; padding: 25px; border-radius: 20px; box-shadow: 0 4px 20px rgba(0,0,0,0.04); display: flex; align-items: center; gap: 20px; position: relative; overflow: hidden; }

.stat-icon { width: 60px; height: 60px; border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; flex-shrink: 0; }
.stat-card.revenue .stat-icon { background: #dcfce7; color: #166534; }
.stat-card.monthly .stat-icon { background: #e0e7ff; color: #3730a3; }
.stat-card.transactions .stat-icon { background: #f3e8ff; color: #7e22ce; }
.stat-card.pending .stat-icon { background: #ffedd5; color: #c2410c; }

.stat-info h3 { margin: 0; font-size: 1.6rem; color: #1e293b; }
.stat-info h3 small { font-size: 0.9rem; color: #64748b; }
.stat-info p { margin: 5px 0 0; color: #64748b; font-size: 0.95rem; }

.stat-trend { position: absolute; top: 20px; left: 20px; font-size: 0.8rem; font-weight: 600; display: flex; align-items: center; gap: 4px; }
.stat-trend.positive { color: #166534; }

/* Grid */
.content-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 30px; }
.glass-panel { background: white; border-radius: 20px; padding: 25px; box-shadow: 0 4px 20px rgba(0,0,0,0.04); border: 1px solid rgba(0,0,0,0.05); }

.section-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 1px solid #f1f5f9; }
.section-header h3 { margin: 0; font-size: 1.1rem; color: #1e293b; display: flex; align-items: center; gap: 10px; }
.section-header h3 i { color: #8b5cf6; }
.view-all { font-size: 0.85rem; color: #8b5cf6; text-decoration: none; font-weight: 600; }

.chart-container { height: 350px; }

/* Transactions List */
.transactions-list { max-height: 400px; overflow-y: auto; }
.txn-item { display: flex; align-items: center; gap: 15px; padding: 15px 0; border-bottom: 1px solid #f8fafc; }
.txn-item:last-child { border-bottom: none; }

.txn-icon { width: 40px; height: 40px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1rem; flex-shrink: 0; }
.txn-icon.completed { background: #dcfce7; color: #166534; }
.txn-icon.pending { background: #ffedd5; color: #c2410c; }
.txn-icon.failed { background: #fee2e2; color: #991b1b; }

.txn-details { flex: 1; display: flex; flex-direction: column; }
.txn-user { font-weight: 600; color: #1e293b; font-size: 0.95rem; }
.txn-course { font-size: 0.85rem; color: #64748b; margin-top: 2px; }
.txn-date { font-size: 0.75rem; color: #94a3b8; margin-top: 2px; }

.txn-amount { font-weight: 700; font-size: 1rem; }
.txn-amount.completed { color: #166534; }
.txn-amount.pending { color: #c2410c; }

@media (max-width: 992px) {
    .content-grid { grid-template-columns: 1fr; }
}
</style>

<?php
});
?>
