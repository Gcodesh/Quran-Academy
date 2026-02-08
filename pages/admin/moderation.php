<?php
require_once '../../pages/dashboard/layout.php';
require_once '../../includes/config/database.php';
require_once '../../includes/classes/Database.php';
require_once '../../src/Services/MediaService.php';

$page_title = 'مراجعة المحتوى';

render_dashboard_layout(function() {
    $db = new Database();
    $conn = $db->getConnection();

    // Ensure only admins can access (middleware check already handles basic dash access, but let's be safe)
    if ($_SESSION['user_role'] !== 'admin') {
        echo '<div class="alert error">غير مسموح لك بالوصول لهذه الصفحة</div>';
        return;
    }

    // Handle Moderation Action
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
        $lesson_id = $_POST['lesson_id'];
        $action = $_POST['action'];
        $notes = $_POST['notes'] ?? '';

        $status = ($action === 'approve') ? 'published' : 'rejected';

        $stmt = $conn->prepare("UPDATE lessons SET status = ?, moderation_notes = ? WHERE id = ?");
        if ($stmt->execute([$status, $notes, $lesson_id])) {
            echo '<div class="alert success">تمت معالجة الطلب بنجاح ✅</div>';
        }
    }

    // Fetch lessons pending review
    $stmt = $conn->prepare("
        SELECT l.*, c.title as course_title, u.full_name as teacher_name 
        FROM lessons l 
        JOIN courses c ON l.course_id = c.id 
        JOIN users u ON c.teacher_id = u.id 
        WHERE l.status = 'review' 
        ORDER BY l.created_at ASC
    ");
    $stmt->execute();
    $pending_lessons = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="dash-card">
    <h2 style="margin-top: 0; margin-bottom: 25px;"><i class="fas fa-tasks" style="color: #6366f1;"></i> طلبات مراجعة الدروس</h2>

    <?php if (empty($pending_lessons)): ?>
        <div style="text-align: center; padding: 50px; color: #94a3b8;">
            <i class="fas fa-check-circle" style="font-size: 3rem; margin-bottom: 15px; opacity: 0.3;"></i>
            <p>لا توجد دروس بانتظار المراجعة حالياً.</p>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table style="width: 100%; border-collapse: collapse; margin-top: 10px;">
                <thead>
                    <tr style="text-align: right; background: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                        <th style="padding: 15px;">الدرس</th>
                        <th style="padding: 15px;">الدورة</th>
                        <th style="padding: 15px;">المعلم</th>
                        <th style="padding: 15px;">التاريخ</th>
                        <th style="padding: 15px;">الإجراء</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pending_lessons as $l): ?>
                        <tr style="border-bottom: 1px solid #f1f5f9;">
                            <td style="padding: 15px;">
                                <div style="font-weight: 600; color: #1e293b;"><?= htmlspecialchars($l['title']) ?></div>
                                <div style="font-size: 0.8rem; color: #64748b;"><?= htmlspecialchars($l['lesson_type']) ?></div>
                            </td>
                            <td style="padding: 15px;"><?= htmlspecialchars($l['course_title']) ?></td>
                            <td style="padding: 15px;"><?= htmlspecialchars($l['teacher_name']) ?></td>
                            <td style="padding: 15px;"><?= date('Y/m/d', strtotime($l['created_at'])) ?></td>
                            <td style="padding: 15px;">
                                <button onclick='showReviewModal(<?= json_encode($l) ?>)' class="btn-primary-glow" style="padding: 8px 16px; font-size: 0.85rem;">
                                    مراجعة
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<!-- Review Modal -->
<div id="reviewModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div style="background: #fff; border-radius: 20px; padding: 30px; width: 90%; max-width: 800px; max-height: 90vh; overflow-y: auto;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3 id="modalTitle">مراجعة الدرس</h3>
            <button onclick="closeReviewModal()" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: #94a3b8;">&times;</button>
        </div>

        <div id="modalContent" style="margin-bottom: 25px;">
            <!-- Content via JS -->
        </div>

        <form method="POST">
            <input type="hidden" name="lesson_id" id="modal_lesson_id">
            <div class="form-group" style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600;">ملاحظات المراجعة (تظهر للمعلم في حال الرفض)</label>
                <textarea name="notes" rows="3" style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 10px;"></textarea>
            </div>
            
            <div style="display: flex; gap: 15px;">
                <button type="submit" name="action" value="approve" class="btn-primary-glow" style="padding: 12px 30px; background: #10b981;">
                    <i class="fas fa-check"></i> موافقة ونشر
                </button>
                <button type="submit" name="action" value="reject" class="btn-outline-primary" style="padding: 12px 30px; border: 1px solid #ef4444; color: #ef4444; background: #fff;">
                    <i class="fas fa-times"></i> رفض وإعادة للمعلم
                </button>
            </div>
        </form>
    </div>
</div>

<style>
.dash-card { background: #fff; border-radius: 15px; padding: 25px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
.alert { padding: 15px; border-radius: 10px; margin-bottom: 20px; }
.alert.success { background: #dcfce7; color: #166534; }
.alert.error { background: #fee2e2; color: #991b1b; }
</style>

<script>
function showReviewModal(lesson) {
    document.getElementById('modal_lesson_id').value = lesson.id;
    document.getElementById('modalTitle').innerText = 'مراجعة: ' + lesson.title;
    
    let content = `
        <div style="background: #f8fafc; padding: 15px; border-radius: 12px; margin-bottom: 20px;">
            <div style="font-weight: 600; margin-bottom: 5px;">محتوى الدرس:</div>
            <div style="color: #475569; line-height: 1.6;">${lesson.content || 'لا يوجد محتوى نصي'}</div>
        </div>
        ${lesson.media_url ? `
            <div style="margin-bottom: 20px;">
                <div style="font-weight: 600; margin-bottom: 10px;">الوسائط المرفقة (<a href="../../${lesson.media_url}" target="_blank">رابط مباشر</a>):</div>
                <div style="border-radius: 12px; overflow: hidden; border: 1px solid #e2e8f0;">
                    ${lesson.media_type === 'video' ? `<iframe src="${lesson.media_url.includes('youtube') ? lesson.media_url : '../../'+lesson.media_url}" style="width:100%; aspect-ratio:16/9;" frameborder="0" allowfullscreen></iframe>` : `<p style="padding: 15px;">نوع الوسائط: ${lesson.media_type}</p>`}
                </div>
            </div>
        ` : ''}
    `;
    
    document.getElementById('modalContent').innerHTML = content;
    document.getElementById('reviewModal').style.display = 'flex';
}

function closeReviewModal() {
    document.getElementById('reviewModal').style.display = 'none';
}
</script>

<?php
});
?>
