<?php
require_once '../../pages/dashboard/layout.php';
require_once '../../includes/config/database.php';
require_once '../../includes/classes/Database.php';
require_once '../../includes/classes/VersioningManager.php';
require_once '../../src/Services/MediaService.php';

$page_title = 'تعديل الدرس';

render_dashboard_layout(function() {
    $db = new Database();
    $conn = $db->getConnection();
    $teacher_id = $_SESSION['user_id'];
    $versioning = new \App\Classes\VersioningManager($conn);

    $lesson_id = $_GET['id'] ?? null;
    $course_id = $_GET['course_id'] ?? null;

    if (!$lesson_id || !$course_id) {
        echo '<div class="alert error">بيانات غير مكتملة</div>';
        return;
    }

    // Verify ownership via course
    $stmt = $conn->prepare("SELECT l.*, c.teacher_id FROM lessons l JOIN courses c ON l.course_id = c.id WHERE l.id = ? AND c.id = ? AND c.teacher_id = ?");
    $stmt->execute([$lesson_id, $course_id, $teacher_id]);
    $lesson = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$lesson) {
        echo '<div class="alert error">لا تملك صلاحية تعديل هذا الدرس أو الدرس غير موجود</div>';
        return;
    }

    // Handle Update
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $title = $_POST['title'] ?? $lesson['title'];
        $content = $_POST['content'] ?? $lesson['content'];
        $media_type = $_POST['media_type'] ?? $lesson['media_type'];
        $media_url = $_POST['media_url'] ?? $lesson['media_url'];
        $lesson_type = $_POST['lesson_type'] ?? $lesson['lesson_type'];
        $change_summary = $_POST['change_summary'] ?? 'تم تحديث المحتوى';
        $submit_review = isset($_POST['submit_review']);

        // Handle File Update if any
        if (isset($_FILES['lesson_file']) && $_FILES['lesson_file']['error'] === 0) {
            $upload_dir = '../../uploads/lessons/';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
            $ext = pathinfo($_FILES['lesson_file']['name'], PATHINFO_EXTENSION);
            $filename = 'lesson_' . time() . '_' . uniqid() . '.' . $ext;
            if (move_uploaded_file($_FILES['lesson_file']['tmp_name'], $upload_dir . $filename)) {
                $media_url = 'uploads/lessons/' . $filename;
            }
        }

        // Create version BEFORE update
        $versioning->createVersion($lesson_id, $teacher_id, $change_summary);

        $status = $lesson['status'];
        if ($submit_review) $status = 'review';
        elseif ($status === 'rejected') $status = 'draft';

        $sql = "UPDATE lessons SET title = ?, content = ?, media_type = ?, media_url = ?, lesson_type = ?, status = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt->execute([$title, $content, $media_type, $media_url, $lesson_type, $status, $lesson_id])) {
            echo '<div class="alert success"><i class="fas fa-check-circle"></i> تم حفظ التعديلات وإنشاء نسخة جديدة</div>';
            // Refresh data
            $stmt = $conn->prepare("SELECT * FROM lessons WHERE id = ?");
            $stmt->execute([$lesson_id]);
            $lesson = $stmt->fetch(PDO::FETCH_ASSOC);
        }
    }

    $history = $versioning->getHistory($lesson_id);
?>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px;">
    <!-- Main Editor -->
    <div>
        <div class="dash-card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
                <h2 style="margin: 0;">تعديل الدرس: <?= htmlspecialchars($lesson['title']) ?></h2>
                <a href="edit-course.php?id=<?= $course_id ?>" class="btn-icon" title="العودة للدورة">
                    <i class="fas fa-arrow-left"></i>
                </a>
            </div>

            <form method="POST" enctype="multipart/form-data">
                <div class="form-group" style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600;">عنوان الدرس</label>
                    <input type="text" name="title" value="<?= htmlspecialchars($lesson['title']) ?>" required style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 10px;">
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                    <div class="form-group">
                        <label style="display: block; margin-bottom: 8px; font-weight: 600;">نوع المادة</label>
                        <select name="lesson_type" style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 10px;">
                            <option value="lecture" <?= $lesson['lesson_type'] == 'lecture' ? 'selected' : '' ?>>محاضرة</option>
                            <option value="quiz" <?= $lesson['lesson_type'] == 'quiz' ? 'selected' : '' ?>>اختبار</option>
                            <option value="assignment" <?= $lesson['lesson_type'] == 'assignment' ? 'selected' : '' ?>>واجب</option>
                            <option value="reading" <?= $lesson['lesson_type'] == 'reading' ? 'selected' : '' ?>>قراءة</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label style="display: block; margin-bottom: 8px; font-weight: 600;">نوع الوسائط</label>
                        <select name="media_type" style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 10px;">
                            <option value="video" <?= $lesson['media_type'] == 'video' ? 'selected' : '' ?>>فيديو</option>
                            <option value="audio" <?= $lesson['media_type'] == 'audio' ? 'selected' : '' ?>>صوت</option>
                            <option value="pdf" <?= $lesson['media_type'] == 'pdf' ? 'selected' : '' ?>>PDF</option>
                            <option value="text" <?= $lesson['media_type'] == 'text' ? 'selected' : '' ?>>نص</option>
                        </select>
                    </div>
                </div>

                <div class="form-group" style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600;">رابط الوسائط (YouTube, Vimeo, etc.)</label>
                    <input type="url" name="media_url" value="<?= htmlspecialchars($lesson['media_url']) ?>" style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 10px;">
                </div>

                <div class="form-group" style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600;">أو رفع ملف جديد</label>
                    <input type="file" name="lesson_file" style="width: 100%; padding: 10px; border: 1px dashed #cbd5e1; border-radius: 10px;">
                </div>

                <div class="form-group" style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600;">محتوى الدرس (شرح نصي)</label>
                    <textarea name="content" rows="10" style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 10px;"><?= htmlspecialchars($lesson['content']) ?></textarea>
                </div>

                <div class="form-group" style="margin-bottom: 20px; background: #f8fafc; padding: 15px; border-radius: 12px; border: 1px solid #e2e8f0;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #1e293b;">ملخص التغيير (Version Note)</label>
                    <input type="text" name="change_summary" placeholder="مثال: تحديث شرح المقدمة، إضافة ملف مرفق جديد..." style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px;">
                </div>

                <div style="display: flex; gap: 15px;">
                    <button type="submit" class="btn-primary-glow" style="padding: 12px 30px;">
                        <i class="fas fa-save"></i> حفظ التغييرات كنسخة جديدة
                    </button>
                    
                    <?php if ($lesson['status'] === 'draft' || $lesson['status'] === 'rejected'): ?>
                    <button type="submit" name="submit_review" class="btn-outline-primary" style="padding: 12px 30px; border: 1px solid var(--primary-500); border-radius: 12px; background: transparent; cursor: pointer; color: var(--primary-600); font-weight: 600;">
                        <i class="fas fa-paper-plane"></i> إرسال للمراجعة والاعتماد
                    </button>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

    <!-- Sidebar: Status & History -->
    <div>
        <!-- Status Card -->
        <div class="dash-card" style="margin-bottom: 20px;">
            <h3 style="margin: 0 0 15px; font-size: 1.1rem;">حالة النشر</h3>
            <?php
                $st = $lesson['status'];
                $bg = $st == 'published' ? '#dcfce7' : ($st == 'rejected' ? '#fee2e2' : ($st == 'review' ? '#fef3c7' : '#f1f5f9'));
                $col = $st == 'published' ? '#166534' : ($st == 'rejected' ? '#991b1b' : ($st == 'review' ? '#92400e' : '#475569'));
                $lbl = $st == 'published' ? 'منشور' : ($st == 'rejected' ? 'مرفوض' : ($st == 'review' ? 'قيد المراجعة' : 'مسودة'));
            ?>
            <div style="background: <?= $bg ?>; color: <?= $col ?>; padding: 15px; border-radius: 12px; text-align: center; font-weight: bold; margin-bottom: 15px;">
                <?= $lbl ?>
            </div>
            
            <?php if ($lesson['moderation_notes'] && $st == 'rejected'): ?>
                <div style="background: #fff1f2; color: #991b1b; padding: 12px; border-radius: 10px; font-size: 0.9rem; border: 1px solid #fda4af;">
                    <strong>ملاحظات المرفض:</strong><br>
                    <?= nl2br(htmlspecialchars($lesson['moderation_notes'])) ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- History Card -->
        <div class="dash-card">
            <h3 style="margin: 0 0 15px; font-size: 1.1rem;"><i class="fas fa-history"></i> تاريخ الإصدارات</h3>
            <div class="history-list">
                <?php foreach ($history as $v): ?>
                <div style="padding: 12px 0; border-bottom: 1px solid #f1f5f9;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 5px;">
                        <span style="font-weight: 600; font-size: 0.9rem;">الإصدار <?= $v['version_number'] ?></span>
                        <span style="font-size: 0.75rem; color: #94a3b8;"><?= date('Y/m/d H:i', strtotime($v['created_at'])) ?></span>
                    </div>
                    <p style="margin: 0; font-size: 0.85rem; color: #64748b; line-height: 1.4;"><?= htmlspecialchars($v['change_summary']) ?></p>
                    <div style="margin-top: 8px; display: flex; gap: 10px;">
                         <button onclick="previewVersion(<?= $v['version_number'] ?>)" style="background: none; border: none; color: #3b82f6; cursor: pointer; font-size: 0.8rem; padding: 0;">معاينة</button>
                         <button onclick="confirmRollback(<?= $v['version_number'] ?>)" style="background: none; border: none; color: #ef4444; cursor: pointer; font-size: 0.8rem; padding: 0;">استعادة</button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<style>
.dash-card { background: #fff; border-radius: 15px; padding: 25px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
.btn-icon { width: 40px; height: 40px; border-radius: 10px; background: #f1f5f9; display: flex; align-items: center; justify-content: center; color: #64748b; text-decoration: none; }
.alert { padding: 15px; border-radius: 10px; margin-bottom: 20px; }
.alert.success { background: #dcfce7; color: #166534; }
.alert.error { background: #fee2e2; color: #991b1b; }
</style>

<script>
function confirmRollback(v) {
    if (confirm('هل أنت متأكد من رغبتك في استعادة الإصدار رقم ' + v + '؟ سيتم اعتبار هذا التعديل كنسخة جديدة كمسودة.')) {
        // Simple rollback handling via URL for now, could be POST
        window.location.href = 'rollback-lesson.php?id=<?= $lesson_id ?>&course_id=<?= $course_id ?>&v=' + v;
    }
}
function previewVersion(v) {
    // Placeholder for preview functionality
    alert('سيتم فتح معاينة الإصدار ' + v + ' قريباً');
}
</script>

<?php
});
?>
