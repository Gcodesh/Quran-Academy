<?php
require_once __DIR__ . '/admin_middleware.php';
require_once '../../pages/dashboard/layout.php';
require_once '../../includes/config/database.php';
require_once '../../includes/classes/Database.php';

$page_title = 'مراجعة الدورة';

render_dashboard_layout(function() {
    $db = new Database();
    $conn = $db->getConnection();
    
    $course_id = $_GET['id'] ?? null;
    $message = '';

    // Handle AJAX actions or Redirects
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = $_POST['action'] ?? '';
        $cid = $_POST['course_id'] ?? '';
        
        if ($action === 'approve') {
            $stmt = $conn->prepare("UPDATE courses SET status = 'published' WHERE id = ?");
            if($stmt->execute([$cid])) $message = '<div class="p-alert-floating success">تم قبول ونشر الدورة بنجاح ✅</div>';
        } elseif ($action === 'reject') {
            $reason = $_POST['reason'] ?? 'لم يتم استيفاء المعايير';
            $stmt = $conn->prepare("UPDATE courses SET status = 'rejected' WHERE id = ?");
            if($stmt->execute([$cid])) $message = '<div class="p-alert-floating danger">تم رفض الدورة وإرسال السبب للمعلم ❌</div>';
        }
    }

    // If ID provided, show single review
    if ($course_id) {
        $stmt = $conn->prepare("
            SELECT c.*, u.name as teacher_name, u.email as teacher_email, cat.name as category_name 
            FROM courses c 
            LEFT JOIN users u ON c.teacher_id = u.id 
            LEFT JOIN categories cat ON c.category_id = cat.id 
            WHERE c.id = ?
        ");
        $stmt->execute([$course_id]);
        $course = $stmt->fetch(PDO::FETCH_ASSOC);
    }
?>

<div class="admin-course-review premium-experience">
    <!-- Header -->
    <div class="dashboard-header-flex">
        <div>
            <h1 style="font-size: 2.2rem; font-weight: 900; color: var(--p-slate-900); margin: 0;">مركز المراجعة والاعتماد 📋</h1>
            <p style="color: var(--p-slate-500); margin-top: 10px;">تحقق من جودة ومحتوى الدورات قبل نشرها</p>
        </div>
        <div>
            <a href="approvals.php" class="p-btn-secondary" style="text-decoration:none; padding:12px 25px; border-radius:15px; background:white; border:1px solid var(--p-slate-100); color:var(--p-slate-600); font-weight:700; display:flex; align-items:center; gap:8px;">
                <i class="fas fa-list-ul"></i> طلبات الانتظار
            </a>
        </div>
    </div>

    <?= $message ?>

    <div class="p-review-layout" style="margin-top:40px;">
        <?php if ($course_id && isset($course)): ?>
            <div class="glass-card" style="padding:0; overflow:hidden;">
                <div class="p-review-grid" style="display:grid; grid-template-columns: 1fr 400px; min-height:600px;">
                    <!-- Content View -->
                    <div class="p-review-content" style="padding:45px; border-left:1px solid var(--p-slate-50);">
                        <div style="margin-bottom:30px;">
                            <span class="p-badge" style="background:var(--p-emerald-50); color:var(--p-emerald-600); margin-bottom:15px;"><?= htmlspecialchars($course['category_name'] ?: 'غير مصنف') ?></span>
                            <h2 style="font-size:2rem; font-weight:900; color:var(--p-slate-900); margin:0 0 20px;"><?= htmlspecialchars($course['title']) ?></h2>
                            
                            <div class="p-course-image-preview" style="width:100%; height:350px; border-radius:32px; overflow:hidden; margin-bottom:30px; border:1px solid var(--p-slate-100);">
                                <img src="<?= $course['image'] ?: 'https://placehold.co/800x450?text=Course+Preview' ?>" style="width:100%; height:100%; object-fit:cover;">
                            </div>

                            <div class="p-description-box" style="line-height:1.8; color:var(--p-slate-600); font-size:1.1rem;">
                                <?= nl2br(htmlspecialchars($course['description'])) ?>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar Actions -->
                    <div class="p-review-sidebar" style="padding:45px; background:rgba(248,250,252,0.5);">
                        <div class="p-teacher-card" style="margin-bottom:40px;">
                            <h4 style="color:var(--p-slate-400); font-size:0.9rem; margin-bottom:15px; text-transform:uppercase;">المعلم</h4>
                            <div style="display:flex; align-items:center; gap:15px;">
                                <div style="width:50px; height:50px; background:var(--p-slate-100); border-radius:15px; display:flex; align-items:center; justify-content:center; font-weight:800; color:var(--p-slate-400);">
                                    <?= mb_substr($course['teacher_name'], 0, 1, 'UTF-8') ?>
                                </div>
                                <div>
                                    <div style="font-weight:800; color:var(--p-slate-800);"><?= htmlspecialchars($course['teacher_name']) ?></div>
                                    <div style="font-size:0.8rem; color:var(--p-slate-400);"><?= htmlspecialchars($course['teacher_email']) ?></div>
                                </div>
                            </div>
                        </div>

                        <div class="p-meta-list" style="margin-bottom:40px;">
                            <div style="display:flex; justify-content:space-between; margin-bottom:15px;">
                                <span style="color:var(--p-slate-400);">سعر الدورة:</span>
                                <strong style="color:var(--p-emerald-600);"><?= $course['price'] > 0 ? $course['price'] . ' ر.س' : 'مجانية' ?></strong>
                            </div>
                            <div style="display:flex; justify-content:space-between; margin-bottom:15px;">
                                <span style="color:var(--p-slate-400);">اللغة:</span>
                                <strong style="color:var(--p-slate-800);">العربية</strong>
                            </div>
                            <div style="display:flex; justify-content:space-between;">
                                <span style="color:var(--p-slate-400);">تاريخ التقديم:</span>
                                <strong style="color:var(--p-slate-800);"><?= date('Y/m/d', strtotime($course['created_at'])) ?></strong>
                            </div>
                        </div>

                        <div class="p-action-area" style="margin-top:60px; display:flex; flex-direction:column; gap:15px;">
                            <form method="POST">
                                <input type="hidden" name="course_id" value="<?= $course['id'] ?>">
                                <input type="hidden" name="action" value="approve">
                                <button type="submit" style="width:100%; padding:18px; border-radius:18px; background:linear-gradient(135deg, var(--p-emerald-500), var(--p-emerald-700)); color:white; border:none; font-weight:800; cursor:pointer; font-size:1.1rem; box-shadow:0 10px 25px rgba(16,185,129,0.25);">
                                    <i class="fas fa-check-circle"></i> موافقة ونشر
                                </button>
                            </form>
                            
                            <button onclick="showReject()" style="width:100%; padding:18px; border-radius:18px; background:white; color:#ef4444; border:2px solid #fee2e2; font-weight:800; cursor:pointer; font-size:1rem;">
                                <i class="fas fa-times-circle"></i> رفض الطلب
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="glass-card" style="padding:100px 20px; text-align:center;">
                <div style="font-size:4rem; color:var(--p-slate-100); margin-bottom:30px;"><i class="fas fa-search"></i></div>
                <h2 style="color:var(--p-slate-800); font-weight:900;">لم يتم اختيار دورة للمراجعة</h2>
                <p style="color:var(--p-slate-400); max-width:500px; margin:15px auto;">يرجى اختيار دورة من قائمة طلبات الانتظار لمراجعة محتواها واعتمادها.</p>
                <a href="approvals.php" class="p-btn-secondary" style="display:inline-block; text-decoration:none; margin-top:20px; background:var(--p-slate-900); color:white; padding:15px 35px; border-radius:15px;">انتقل لطلبات الانتظار</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Custom Modals & Scripts -->
<style>
.p-alert-floating { position: fixed; bottom: 30px; left: 30px; padding: 20px 35px; border-radius: 20px; color: white; font-weight: 700; z-index: 9999; box-shadow: 0 15px 40px rgba(0,0,0,0.2); animation: pFadeInRight 0.5s ease; }
.p-alert-floating.success { background: #10b981; }
.p-alert-floating.danger { background: #ef4444; }

@keyframes pFadeInRight { from { opacity: 0; transform: translateX(-50px); } to { opacity: 1; transform: translateX(0); } }
</style>

<script>
function showReject() {
    const reason = prompt('يرجى كتابة سبب الرفض (سيصل للمعلم):');
    if (reason) {
        const form = document.createElement('form');
        form.method = 'POST';
        const action = document.createElement('input');
        action.name = 'action'; action.value = 'reject';
        const cid = document.createElement('input');
        cid.name = 'course_id'; cid.value = '<?= $course_id ?>';
        const r = document.createElement('input');
        r.name = 'reason'; r.value = reason;
        
        form.appendChild(action); form.appendChild(cid); form.appendChild(r);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>

<?php
});
?>
