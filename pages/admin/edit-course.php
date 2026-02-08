<?php
require_once __DIR__ . '/admin_middleware.php';
require_once '../../pages/dashboard/layout.php';
require_once '../../includes/config/database.php';
require_once '../../includes/classes/Database.php';

$page_title = 'تعديل الدورة - لوحة الأدمن';

render_dashboard_layout(function() {
    $db = new Database();
    $conn = $db->getConnection();
    
    // Get course ID
    $course_id = $_GET['id'] ?? null;
    if (!$course_id) {
        echo '<div class="alert error">لم يتم تحديد الدورة</div>';
        return;
    }
    
    // Fetch course with teacher name (admin can edit any course)
    $stmt = $conn->prepare("SELECT c.*, u.name as teacher_name FROM courses c JOIN users u ON c.teacher_id = u.id WHERE c.id = ?");
    $stmt->execute([$course_id]);
    $course = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$course) {
        echo '<div class="alert error">الدورة غير موجودة</div>';
        return;
    }
    
    // Fetch all teachers for dropdown
    $teachers = $conn->query("SELECT id, name FROM users WHERE role = 'teacher' ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
    
    // Handle Update
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $title = $_POST['title'] ?? '';
        $description = $_POST['description'] ?? '';
        $status = $_POST['status'] ?? 'draft';
        $teacher_id = $_POST['teacher_id'] ?? $course['teacher_id'];
        
        // Handle image upload
        $image = $course['image'];
        if (!empty($_FILES['image']['tmp_name'])) {
            $upload_dir = '../../assets/uploads/courses/';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
            
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $filename = 'course_' . $course_id . '_' . time() . '.' . $ext;
            $target = $upload_dir . $filename;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
                $image = '/islamic-education-platform/assets/uploads/courses/' . $filename;
            }
        }
        
        $stmt = $conn->prepare("UPDATE courses SET title = ?, description = ?, image = ?, status = ?, teacher_id = ? WHERE id = ?");
        if ($stmt->execute([$title, $description, $image, $status, $teacher_id, $course_id])) {
            // Log action
            $admin_id = $_SESSION['user_id'];
            $log_stmt = $conn->prepare("INSERT INTO audit_logs (user_id, action, details, severity) VALUES (?, ?, ?, ?)");
            $log_stmt->execute([$admin_id, 'edit_course', "Edited course ID: $course_id", 'info']);
            
            echo '<div class="alert success"><i class="fas fa-check-circle"></i> تم حفظ التعديلات بنجاح</div>';
            
            // Refresh course data
            $stmt = $conn->prepare("SELECT c.*, u.name as teacher_name FROM courses c JOIN users u ON c.teacher_id = u.id WHERE c.id = ?");
            $stmt->execute([$course_id]);
            $course = $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            echo '<div class="alert error">حدث خطأ أثناء الحفظ</div>';
        }
    }
    
    // Fetch lessons
    $stmt = $conn->prepare("SELECT * FROM lessons WHERE course_id = ? ORDER BY id ASC");
    $stmt->execute([$course_id]);
    $lessons = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="dash-card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; flex-wrap: wrap; gap: 15px;">
        <div>
            <a href="courses.php" style="color: var(--primary-500); text-decoration: none; font-size: 0.9rem;">
                <i class="fas fa-arrow-right"></i> العودة لقائمة الدورات
            </a>
            <h2 style="margin: 10px 0 0;">تعديل الدورة (أدمن)</h2>
        </div>
        <div style="display: flex; gap: 10px; align-items: center;">
            <?php
                $st = $course['status'];
                $bg = $st == 'published' ? '#dcfce7' : ($st == 'rejected' ? '#fee2e2' : ($st == 'pending' ? '#fef3c7' : '#f1f5f9'));
                $col = $st == 'published' ? '#166534' : ($st == 'rejected' ? '#991b1b' : ($st == 'pending' ? '#92400e' : '#475569'));
                $lbl = $st == 'published' ? 'منشور' : ($st == 'rejected' ? 'مرفوض' : ($st == 'pending' ? 'قيد المراجعة' : 'مسودة'));
            ?>
            <span style="background: <?= $bg ?>; color: <?= $col ?>; padding: 8px 16px; border-radius: 20px; font-weight: 600;">
                <?= $lbl ?>
            </span>
        </div>
    </div>

    <form method="POST" enctype="multipart/form-data">
        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px;">
            <!-- Main Content -->
            <div>
                <div class="form-group" style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600;">عنوان الدورة</label>
                    <input type="text" name="title" value="<?= htmlspecialchars($course['title']) ?>" required
                           style="width: 100%; padding: 12px 15px; border: 1px solid var(--light-200); border-radius: 10px; font-size: 1rem;">
                </div>
                
                <div class="form-group" style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600;">وصف الدورة</label>
                    <textarea name="description" rows="6" 
                              style="width: 100%; padding: 12px 15px; border: 1px solid var(--light-200); border-radius: 10px; font-size: 1rem; resize: vertical;"><?= htmlspecialchars($course['description']) ?></textarea>
                </div>
            </div>
            
            <!-- Sidebar -->
            <div>
                <div class="form-group" style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600;">صورة الغلاف</label>
                    <div style="border: 2px dashed var(--light-300); border-radius: 12px; overflow: hidden; position: relative;">
                        <?php if($course['image']): ?>
                            <img src="<?= htmlspecialchars($course['image']) ?>" alt="" id="preview-img" style="width: 100%; height: 180px; object-fit: cover;">
                        <?php else: ?>
                            <div id="preview-placeholder" style="width: 100%; height: 180px; display: flex; align-items: center; justify-content: center; background: var(--light-100); color: var(--dark-400);">
                                <i class="fas fa-image" style="font-size: 2rem;"></i>
                            </div>
                            <img src="" alt="" id="preview-img" style="width: 100%; height: 180px; object-fit: cover; display: none;">
                        <?php endif; ?>
                        <input type="file" name="image" id="image-input" accept="image/*" style="display: none;">
                        <label for="image-input" style="position: absolute; bottom: 10px; left: 10px; background: rgba(0,0,0,0.6); color: #fff; padding: 8px 12px; border-radius: 8px; cursor: pointer; font-size: 0.85rem;">
                            <i class="fas fa-camera"></i> تغيير
                        </label>
                    </div>
                </div>
                
                <!-- Admin-only: Change Teacher -->
                <div class="form-group" style="margin-bottom: 20px; background: #fef3c7; padding: 15px; border-radius: 10px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #92400e;">
                        <i class="fas fa-user-shield"></i> المعلم المسؤول
                    </label>
                    <select name="teacher_id" style="width: 100%; padding: 12px 15px; border: 1px solid #fcd34d; border-radius: 10px;">
                        <?php foreach ($teachers as $teacher): ?>
                            <option value="<?= $teacher['id'] ?>" <?= $course['teacher_id'] == $teacher['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($teacher['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group" style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600;">الحالة</label>
                    <select name="status" style="width: 100%; padding: 12px 15px; border: 1px solid var(--light-200); border-radius: 10px;">
                        <option value="draft" <?= $course['status'] == 'draft' ? 'selected' : '' ?>>مسودة</option>
                        <option value="pending" <?= $course['status'] == 'pending' ? 'selected' : '' ?>>قيد المراجعة</option>
                        <option value="published" <?= $course['status'] == 'published' ? 'selected' : '' ?>>منشور</option>
                        <option value="rejected" <?= $course['status'] == 'rejected' ? 'selected' : '' ?>>مرفوض</option>
                    </select>
                </div>
            </div>
        </div>
        
        <div style="border-top: 1px solid var(--light-200); padding-top: 20px; margin-top: 20px; display: flex; gap: 15px;">
            <button type="submit" class="btn-primary-glow" style="padding: 12px 30px;">
                <i class="fas fa-save"></i> حفظ التعديلات
            </button>
            <a href="courses.php" class="btn-outline-dark" style="padding: 12px 30px; text-decoration: none;">إلغاء</a>
        </div>
    </form>
</div>

<!-- Lessons Section (Admin View) -->
<div class="dash-card" style="margin-top: 30px;">
    <h3 style="margin: 0 0 25px;"><i class="fas fa-list-ul" style="color: var(--primary-500); margin-left: 10px;"></i> دروس الدورة (<?= count($lessons) ?> درس)</h3>
    
    <?php if (empty($lessons)): ?>
        <div style="text-align: center; padding: 40px; color: var(--dark-400);">
            <i class="fas fa-book-open" style="font-size: 3rem; margin-bottom: 15px; display: block;"></i>
            <p>لا توجد دروس في هذه الدورة</p>
        </div>
    <?php else: ?>
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: var(--light-50); text-align: right;">
                    <th style="padding: 12px 15px; border-radius: 0 10px 10px 0;">#</th>
                    <th style="padding: 12px 15px;">عنوان الدرس</th>
                    <th style="padding: 12px 15px;">المحتوى</th>
                    <th style="padding: 12px 15px; border-radius: 10px 0 0 10px;">الوسائط</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($lessons as $index => $lesson): ?>
                <tr>
                    <td style="padding: 12px 15px; border-bottom: 1px solid var(--light-100);"><?= $index + 1 ?></td>
                    <td style="padding: 12px 15px; border-bottom: 1px solid var(--light-100); font-weight: 600;"><?= htmlspecialchars($lesson['title']) ?></td>
                    <td style="padding: 12px 15px; border-bottom: 1px solid var(--light-100); color: var(--dark-500); font-size: 0.9rem;">
                        <?= mb_substr(htmlspecialchars($lesson['content']), 0, 80) ?>...
                    </td>
                    <td style="padding: 12px 15px; border-bottom: 1px solid var(--light-100);">
                        <?php if($lesson['video_url']): ?>
                            <i class="fas fa-video" style="color: var(--primary-500);" title="فيديو"></i>
                        <?php endif; ?>
                        <?php if($lesson['audio_url']): ?>
                            <i class="fas fa-headphones" style="color: var(--secondary-500);" title="صوت"></i>
                        <?php endif; ?>
                        <?php if($lesson['pdf_url']): ?>
                            <i class="fas fa-file-pdf" style="color: #ef4444;" title="PDF"></i>
                        <?php endif; ?>
                        <?php if(!$lesson['video_url'] && !$lesson['audio_url'] && !$lesson['pdf_url']): ?>
                            <span style="color: var(--dark-400);">-</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<style>
.alert {
    padding: 15px 20px;
    border-radius: 10px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
}
.alert.success { background: #dcfce7; color: #166534; }
.alert.error { background: #fee2e2; color: #991b1b; }
.btn-outline-dark {
    background: transparent;
    border: 1px solid var(--dark-300);
    color: var(--dark-700);
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}
</style>

<script>
document.getElementById('image-input').onchange = function(e) {
    const file = e.target.files[0];
    if (file) {
        const preview = document.getElementById('preview-img');
        const placeholder = document.getElementById('preview-placeholder');
        preview.src = URL.createObjectURL(file);
        preview.style.display = 'block';
        if (placeholder) placeholder.style.display = 'none';
    }
};
</script>

<?php
});
?>
