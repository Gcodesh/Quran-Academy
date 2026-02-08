<?php
require_once __DIR__ . '/admin_middleware.php';
require_once '../../pages/dashboard/layout.php';
require_once '../../includes/classes/Database.php';

$page_title = 'مراجعة الدورات';

render_dashboard_layout(function() {
    $db = new Database();
    $conn = $db->getConnection();
    
    // Fetch pending courses
    $stmt = $conn->query("
        SELECT c.*, u.name as teacher_name, cat.name as category_name 
        FROM courses c 
        LEFT JOIN users u ON c.teacher_id = u.id 
        LEFT JOIN categories cat ON c.category_id = cat.id 
        WHERE c.status = 'pending' 
        ORDER BY c.created_at ASC
    ");
    $pending_courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $msg = '';
    if (isset($_GET['success'])) {
        if ($_GET['success'] == 'approved') $msg = 'تم قبول الدورة ونشرها بنجاح';
        if ($_GET['success'] == 'rejected') $msg = 'تم رفض الدورة وإبلاغ المعلم';
    }
?>

<div class="admin-approvals-page">
    <div class="page-header">
        <div class="header-content">
            <div class="header-title">
                <h1><i class="fas fa-clipboard-check"></i> مراجعة الدورات</h1>
                <p>مراجعة طلبات النشر المقدمة من المعلمين</p>
            </div>
            <div class="badge-count">
                <?= count($pending_courses) ?> طلبات معلقة
            </div>
        </div>
    </div>

    <?php if($msg): ?>
        <div class="alert success">
            <i class="fas fa-check-circle"></i> <?= $msg ?>
        </div>
    <?php endif; ?>

    <div class="review-queue">
        <?php if (empty($pending_courses)): ?>
            <div class="empty-state">
                <div class="icon-box">
                    <i class="fas fa-check-double"></i>
                </div>
                <h3>كل شيء ممتاز!</h3>
                <p>لا توجد دورات بانتظار المراجعة حالياً.</p>
                <a href="courses.php" class="btn-outline">الذهاب لكل الدورات</a>
            </div>
        <?php else: ?>
            <?php foreach ($pending_courses as $course): ?>
                <div class="review-card">
                    <div class="card-media">
                        <img src="<?= $course['image'] ?: 'https://placehold.co/300x200?text=No+Image' ?>" alt="Course Thumbnail">
                        <div class="category-badge"><?= htmlspecialchars($course['category_name'] ?? 'عام') ?></div>
                    </div>
                    
                    <div class="card-body">
                        <div class="meta-info">
                            <span class="teacher"><i class="fas fa-user-tie"></i> <?= htmlspecialchars($course['teacher_name']) ?></span>
                            <span class="date"><i class="far fa-clock"></i> <?= date('Y/m/d H:i', strtotime($course['created_at'])) ?></span>
                        </div>
                        
                        <h3><?= htmlspecialchars($course['title']) ?></h3>
                        <p class="description"><?= mb_substr(strip_tags($course['description']), 0, 150) . '...' ?></p>
                        
                        <div class="card-footer">
                            <a href="../course-details.php?id=<?= $course['id'] ?>" target="_blank" class="preview-link">
                                <i class="fas fa-external-link-alt"></i> معاينة كاملة
                            </a>
                            <div class="actions">
                                <button class="btn-reject" onclick="rejectCourse(<?= $course['id'] ?>)">
                                    <i class="fas fa-times"></i> رفض
                                </button>
                                <form action="../../api/admin_actions.php" method="POST">
                                    <input type="hidden" name="action" value="approve_course">
                                    <input type="hidden" name="course_id" value="<?= $course['id'] ?>">
                                    <button type="submit" class="btn-approve">
                                        <i class="fas fa-check"></i> قبول ونشر
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Reject Modal -->
<form method="POST" action="../../api/admin_actions.php" id="rejectForm">
    <input type="hidden" name="action" value="reject_course">
    <input type="hidden" name="course_id" id="rejectCourseId">
    <input type="hidden" name="reason" id="rejectReason">
</form>

<script>
function rejectCourse(id) {
    const reason = prompt('يرجى ذكر سبب الرفض لإرساله للمعلم (مطلوب):');
    if (reason) {
        document.getElementById('rejectCourseId').value = id;
        document.getElementById('rejectReason').value = reason;
        document.getElementById('rejectForm').submit();
    }
}
</script>

<style>
.admin-approvals-page { max-width: 1000px; margin: 0 auto; }

.header-content { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 30px; }
.header-title h1 { color: #1e293b; margin: 0 0 5px; font-size: 1.8rem; }
.header-title p { color: #64748b; margin: 0; }
.badge-count { background: #f59e0b; color: white; padding: 6px 15px; border-radius: 20px; font-weight: 700; font-size: 0.9rem; letter-spacing: 0.5px; }

.alert { margin-bottom: 25px; padding: 15px; border-radius: 10px; background: #dcfce7; color: #166534; display: flex; align-items: center; gap: 10px; }

/* Empty State */
.empty-state { text-align: center; padding: 60px 20px; background: white; border-radius: 20px; box-shadow: 0 4px 25px rgba(0,0,0,0.04); }
.icon-box { width: 90px; height: 90px; background: #dcfce7; color: #166534; border-radius: 50%; margin: 0 auto 20px; display: flex; align-items: center; justify-content: center; font-size: 2.5rem; }
.empty-state h3 { margin: 0 0 10px; color: #1e293b; font-size: 1.5rem; }
.btn-outline { display: inline-block; margin-top: 25px; padding: 10px 25px; border: 2px solid #e2e8f0; border-radius: 10px; color: #64748b; text-decoration: none; font-weight: 600; transition: 0.2s; }
.btn-outline:hover { border-color: #8b5cf6; color: #8b5cf6; }

/* Review Cards */
.review-queue { display: flex; flex-direction: column; gap: 25px; }

.review-card { background: white; border-radius: 20px; overflow: hidden; display: flex; box-shadow: 0 4px 20px rgba(0,0,0,0.05); transition: 0.3s; border: 1px solid rgba(0,0,0,0.02); }
.review-card:hover { transform: translateY(-3px); box-shadow: 0 10px 30px rgba(0,0,0,0.08); }

.card-media { width: 280px; position: relative; flex-shrink: 0; }
.card-media img { width: 100%; height: 100%; object-fit: cover; }
.category-badge { position: absolute; top: 15px; right: 15px; background: rgba(0,0,0,0.6); color: white; padding: 4px 12px; border-radius: 15px; font-size: 0.8rem; backdrop-filter: blur(4px); }

.card-body { padding: 25px; flex: 1; display: flex; flex-direction: column; }

.meta-info { display: flex; gap: 15px; margin-bottom: 12px; font-size: 0.85rem; color: #64748b; }
.meta-info i { color: #8b5cf6; margin-left: 4px; }

.card-body h3 { margin: 0 0 10px; font-size: 1.3rem; color: #1e293b; }
.description { color: #475569; font-size: 0.95rem; line-height: 1.6; margin-bottom: 25px; }

.card-footer { margin-top: auto; display: flex; justify-content: space-between; align-items: center; border-top: 1px solid #f1f5f9; padding-top: 20px; }

.preview-link { color: #64748b; text-decoration: none; font-weight: 600; font-size: 0.9rem; transition: 0.2s; }
.preview-link:hover { color: #8b5cf6; }

.actions { display: flex; gap: 12px; }

.btn-reject { background: #fee2e2; color: #991b1b; border: none; padding: 10px 20px; border-radius: 10px; font-weight: 600; cursor: pointer; transition: 0.2s; display: flex; align-items: center; gap: 6px; }
.btn-reject:hover { background: #fecaca; }

.btn-approve { background: #10b981; color: white; border: none; padding: 10px 25px; border-radius: 10px; font-weight: 600; cursor: pointer; transition: 0.2s; display: flex; align-items: center; gap: 6px; box-shadow: 0 4px 15px rgba(16, 185, 129, 0.2); }
.btn-approve:hover { background: #059669; transform: translateY(-2px); box-shadow: 0 8px 20px rgba(16, 185, 129, 0.3); }

@media (max-width: 900px) {
    .review-card { flex-direction: column; }
    .card-media { width: 100%; height: 200px; }
    .card-footer { flex-direction: column; gap: 20px; align-items: flex-start; }
    .actions { width: 100%; }
    .btn-reject, .btn-approve { flex: 1; justify-content: center; }
}
</style>

<?php
});
?>
