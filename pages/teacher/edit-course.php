<?php
require_once '../../pages/dashboard/layout.php';
require_once '../../includes/config/database.php';
require_once '../../includes/classes/Database.php';

$page_title = 'تعديل الدورة';

render_dashboard_layout(function() {
    $db = new Database();
    $conn = $db->getConnection();
    $teacher_id = $_SESSION['user_id'];
    
    // Get course ID
    $course_id = $_GET['id'] ?? null;
    if (!$course_id) {
        echo '<div class="alert error">لم يتم تحديد الدورة</div>';
        return;
    }
    
    // Fetch course (only if owned by this teacher)
    $stmt = $conn->prepare("SELECT * FROM courses WHERE id = ? AND teacher_id = ?");
    $stmt->execute([$course_id, $teacher_id]);
    $course = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$course) {
        echo '<div class="alert error">الدورة غير موجودة أو لا تملك صلاحية التعديل</div>';
        return;
    }
    
    // Handle Update
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $title = $_POST['title'] ?? '';
        $description = $_POST['description'] ?? '';
        $status = $_POST['status'] ?? 'draft';
        
        // Handle image upload
        $image = $course['image']; // Keep existing by default
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
        
        $stmt = $conn->prepare("UPDATE courses SET title = ?, description = ?, image = ?, status = ? WHERE id = ? AND teacher_id = ?");
        if ($stmt->execute([$title, $description, $image, $status, $course_id, $teacher_id])) {
            echo '<div class="alert success"><i class="fas fa-check-circle"></i> تم حفظ التعديلات بنجاح</div>';
            // Refresh course data
            $stmt = $conn->prepare("SELECT * FROM courses WHERE id = ?");
            $stmt->execute([$course_id]);
            $course = $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            echo '<div class="alert error">حدث خطأ أثناء الحفظ</div>';
        }
    }
    
    // Fetch sections
    $stmt = $conn->prepare("SELECT * FROM course_sections WHERE course_id = ? ORDER BY order_number ASC, id ASC");
    $stmt->execute([$course_id]);
    $sections = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch lessons organized by section and order
    $stmt = $conn->prepare("SELECT * FROM lessons WHERE course_id = ? ORDER BY section_id ASC, order_number ASC, id ASC");
    $stmt->execute([$course_id]);
    $all_lessons = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Group lessons by section_id
    $grouped_lessons = [];
    foreach ($all_lessons as $lesson) {
        $sid = $lesson['section_id'] ?: 0; // Use 0 for lessons without a section
        $grouped_lessons[$sid][] = $lesson;
    }
?>

<div class="dash-card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; flex-wrap: wrap; gap: 15px;">
        <div>
            <a href="my-courses.php" style="color: var(--primary-500); text-decoration: none; font-size: 0.9rem;">
                <i class="fas fa-arrow-right"></i> العودة لقائمة الدورات
            </a>
            <h2 style="margin: 10px 0 0;">تعديل الدورة</h2>
        </div>
        <div>
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
                
                <div class="form-group" style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600;">الحالة</label>
                    <select name="status" style="width: 100%; padding: 12px 15px; border: 1px solid var(--light-200); border-radius: 10px;">
                        <option value="draft" <?= $course['status'] == 'draft' ? 'selected' : '' ?>>مسودة</option>
                        <option value="pending" <?= $course['status'] == 'pending' ? 'selected' : '' ?>>إرسال للمراجعة</option>
                    </select>
                </div>
            </div>
        </div>
        
        <div style="border-top: 1px solid var(--light-200); padding-top: 20px; margin-top: 20px;">
            <button type="submit" class="btn-primary-glow" style="padding: 12px 30px;">
                <i class="fas fa-save"></i> حفظ التعديلات
            </button>
        </div>
    </form>
</div>

<!-- Curriculum Section -->
<div class="dash-card" style="margin-top: 30px; background: #fff; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.05);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; border-bottom: 1px solid #f1f5f9; padding-bottom: 20px;">
        <h3 style="margin: 0; font-size: 1.3rem; color: #1e293b;">
            <i class="fas fa-layer-group" style="color: #8b5cf6; margin-left: 10px;"></i> هيكل الدورة والمنهج
        </h3>
        <button type="button" onclick="showAddSection()" class="btn-primary-glow" style="padding: 10px 20px; font-size: 0.9rem;">
            <i class="fas fa-plus"></i> إضافة قسم جديد
        </button>
    </div>
    
    <?php if (empty($sections) && empty($grouped_lessons[0])): ?>
        <div style="text-align: center; padding: 60px 40px; color: #94a3b8;">
            <div style="font-size: 4rem; margin-bottom: 20px; opacity: 0.3;"><i class="fas fa-folder-open"></i></div>
            <h4 style="color: #64748b; margin-bottom: 10px;">لا يوجد محتوى في هذه الدورة بعد</h4>
            <p>ابدأ بإضافة أول قسم ثم أضف الدروس بداخله.</p>
        </div>
    <?php else: ?>
        <div id="curriculum-builder">
            <!-- Lessons without a section (Legacy or Direct) -->
            <?php if (!empty($grouped_lessons[0])): ?>
                <div class="curriculum-section" style="margin-bottom: 30px; border: 1px dashed #e2e8f0; border-radius: 12px; padding: 20px;">
                    <h5 style="margin-top: 0; color: #64748b; font-size: 0.9rem;"><i class="fas fa-info-circle"></i> دروس غير مصنفة</h5>
                    <?php foreach ($grouped_lessons[0] as $lesson): ?>
                        <?= renderLessonItem($lesson, $course_id) ?>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <!-- Sections -->
            <?php foreach ($sections as $section): ?>
                <div class="curriculum-section" style="margin-bottom: 25px; background: #f8fafc; border-radius: 16px; border: 1px solid #e2e8f0; overflow: hidden;">
                    <div style="padding: 15px 20px; background: #fff; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <span style="background: #8b5cf6; color: #fff; width: 28px; height: 28px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; font-weight: bold;">
                                <?= $section['order_number'] ?>
                            </span>
                            <h4 style="margin: 0; color: #1e293b; font-size: 1.1rem;"><?= htmlspecialchars($section['title']) ?></h4>
                        </div>
                        <div style="display: flex; gap: 10px;">
                            <button onclick="showAddLesson(<?= $section['id'] ?>)" class="btn-icon" style="background: #f1f5f9; color: #6366f1;" title="إضافة درس لهذا القسم">
                                <i class="fas fa-plus-circle"></i>
                            </button>
                            <button onclick="editSection(<?= $section['id'] ?>, '<?= addslashes($section['title']) ?>')" class="btn-icon" style="background: #f1f5f9; color: #64748b;">
                                <i class="fas fa-pen"></i>
                            </button>
                            <form method="POST" action="delete-section.php" style="display: inline;" onsubmit="return confirm('حذف هذا القسم؟ سيتم نقل الدروس التي بداخله إلى قسم غير مصنف.');">
                                <input type="hidden" name="section_id" value="<?= $section['id'] ?>">
                                <input type="hidden" name="course_id" value="<?= $course_id ?>">
                                <button type="submit" class="btn-icon" style="background: #fff1f2; color: #f43f5e; border: none; cursor: pointer;">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                    
                    <div style="padding: 15px;">
                        <?php if (empty($grouped_lessons[$section['id']])): ?>
                            <div style="text-align: center; padding: 20px; color: #94a3b8; font-size: 0.9rem; border: 1px dashed #cbd5e1; border-radius: 10px; background: #fff;">
                                لا توجد دروس في هذا القسم.
                            </div>
                        <?php else: ?>
                            <?php foreach ($grouped_lessons[$section['id']] as $less): ?>
                                <?= renderLessonItem($less, $course_id) ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php
/**
 * Helper to render a lesson item
 */
function renderLessonItem($lesson, $course_id) {
    $icons = [
        'video' => 'fa-video',
        'audio' => 'fa-volume-up',
        'pdf'   => 'fa-file-pdf',
        'text'  => 'fa-align-left'
    ];
    $icon = $icons[$lesson['media_type'] ?? 'text'] ?? 'fa-book';
    $badge_color = [
        'video' => '#8b5cf6',
        'audio' => '#ec4899',
        'pdf'   => '#ef4444',
        'text'  => '#10b981'
    ][$lesson['media_type'] ?? 'text'] ?? '#64748b';

    ob_start();
    ?>
    <div class="lesson-row" style="display: flex; align-items: center; gap: 15px; padding: 12px 15px; background: #fff; border-radius: 12px; margin-bottom: 8px; border: 1px solid #f1f5f9; transition: 0.3s; position: relative;">
        <div style="width: 40px; height: 40px; border-radius: 10px; background: <?= $badge_color ?>15; color: <?= $badge_color ?>; display: flex; align-items: center; justify-content: center; font-size: 1.1rem;">
            <i class="fas <?= $icon ?>"></i>
        </div>
        <div style="flex: 1;">
            <h5 style="margin: 0; font-size: 0.95rem; color: #1e293b;"><?= htmlspecialchars($lesson['title']) ?></h5>
            <div style="display: flex; gap: 10px; margin-top: 4px; font-size: 0.75rem; color: #94a3b8;">
                <span><i class="far fa-clock"></i> <?= $lesson['duration'] ? gmdate("H:i:s", $lesson['duration']) : '--:--' ?></span>
                <span><i class="fas fa-tag"></i> <?= htmlspecialchars($lesson['lesson_type'] ?? 'lecture') ?></span>
            </div>
        </div>
        <div style="display: flex; gap: 6px;">
            <button onclick="editLesson(<?= $lesson['id'] ?>)" class="btn-icon small" style="background: #f8fafc; color: #64748b;">
                <i class="fas fa-edit"></i>
            </button>
            <form method="POST" action="delete-lesson.php" style="display: inline;" onsubmit="return confirm('حذف هذا الدرس؟');">
                <input type="hidden" name="lesson_id" value="<?= $lesson['id'] ?>">
                <input type="hidden" name="course_id" value="<?= $course_id ?>">
                <button type="submit" class="btn-icon small" style="background: #fff1f2; color: #f43f5e; border: none; cursor: pointer;">
                    <i class="fas fa-trash"></i>
                </button>
            </form>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
?>


<!-- Add Section Modal -->
<div id="sectionModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div style="background: #fff; border-radius: 16px; padding: 30px; width: 90%; max-width: 500px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
            <h3 style="margin: 0;" id="sectionModalTitle">إضافة قسم جديد</h3>
            <button onclick="closeSectionModal()" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: var(--dark-400);">&times;</button>
        </div>
        <form id="sectionForm" method="POST" action="add-section.php">
            <input type="hidden" name="course_id" value="<?= $course_id ?>">
            <input type="hidden" name="section_id" id="edit_section_id">
            <div class="form-group" style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600;">اسم القسم</label>
                <input type="text" name="title" id="section_title" required placeholder="مثال: مقدمة في الفقه" style="width: 100%; padding: 12px 15px; border: 1px solid var(--light-200); border-radius: 10px;">
            </div>
            <button type="submit" class="btn-primary-glow" style="width: 100%; padding: 12px;">
                <i class="fas fa-save"></i> حفظ القسم
            </button>
        </form>
    </div>
</div>

<!-- Add Lesson Modal -->
<div id="lessonModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div style="background: #fff; border-radius: 16px; padding: 30px; width: 90%; max-width: 700px; max-height: 90vh; overflow-y: auto;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
            <h3 style="margin: 0;">إضافة درس جديد</h3>
            <button onclick="closeLessonModal()" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: var(--dark-400);">&times;</button>
        </div>
        <form method="POST" action="add-lesson.php" enctype="multipart/form-data">
            <input type="hidden" name="course_id" value="<?= $course_id ?>">
            <input type="hidden" name="section_id" id="lesson_section_id">
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div class="form-group" style="grid-column: span 2;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600;">عنوان الدرس</label>
                    <input type="text" name="title" required style="width: 100%; padding: 12px 15px; border: 1px solid var(--light-200); border-radius: 10px;">
                </div>
                
                <div class="form-group">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600;">نوع الدرس</label>
                    <select name="lesson_type" style="width: 100%; padding: 12px 15px; border: 1px solid var(--light-200); border-radius: 10px;">
                        <option value="lecture">محاضرة</option>
                        <option value="quiz">اختبار</option>
                        <option value="assignment">واجب</option>
                        <option value="reading">قراءة</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600;">نوع الوسائط</label>
                    <select name="media_type" id="media_type_select" onchange="toggleMediaFields()" style="width: 100%; padding: 12px 15px; border: 1px solid var(--light-200); border-radius: 10px;">
                        <option value="video">فيديو</option>
                        <option value="audio">صوت</option>
                        <option value="pdf">ملف PDF</option>
                        <option value="text">نص فقط</option>
                    </select>
                </div>

                <div class="form-group" id="media_url_group" style="grid-column: span 2;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600;">رابط الفيديو/الوسائط</label>
                    <input type="url" name="media_url" placeholder="YouTube, Vimeo, etc." style="width: 100%; padding: 12px 15px; border: 1px solid var(--light-200); border-radius: 10px;">
                    <small style="color: #64748b; margin-top: 5px; display: block;">أو ارفع ملفاً أدناه</small>
                </div>

                <div class="form-group" style="grid-column: span 2;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600;">رفع ملف (اختياري)</label>
                    <input type="file" name="lesson_file" style="width: 100%; padding: 10px; border: 1px dashed #cbd5e1; border-radius: 10px;">
                </div>

                <div class="form-group" style="grid-column: span 2;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600;">المحتوى النصي</label>
                    <textarea name="content" rows="4" style="width: 100%; padding: 12px 15px; border: 1px solid var(--light-200); border-radius: 10px; resize: vertical;"></textarea>
                </div>
            </div>

            <div style="margin-top: 20px;">
                <button type="submit" class="btn-primary-glow" style="width: 100%; padding: 12px;">
                    <i class="fas fa-plus"></i> إضافة الدرس للمنهج
                </button>
            </div>
        </form>
    </div>
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
.btn-outline-primary {
    background: transparent;
    border: 1px solid var(--primary-500);
    color: var(--primary-600);
    border-radius: 8px;
    cursor: pointer;
    font-weight: 600;
}
.btn-outline-primary:hover { background: var(--primary-50); }
</style>

<script>
// Image Preview
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

// Section Modal
function showAddSection() {
    document.getElementById('sectionModalTitle').innerText = 'إضافة قسم جديد';
    document.getElementById('edit_section_id').value = '';
    document.getElementById('section_title').value = '';
    document.getElementById('sectionModal').style.display = 'flex';
}

function editSection(id, title) {
    document.getElementById('sectionModalTitle').innerText = 'تعديل القسم';
    document.getElementById('edit_section_id').value = id;
    document.getElementById('section_title').value = title;
    document.getElementById('sectionModal').style.display = 'flex';
}

function closeSectionModal() {
    document.getElementById('sectionModal').style.display = 'none';
}

// Lesson Modal with Section ID
function showAddLesson(sectionId = 0) {
    document.getElementById('lesson_section_id').value = sectionId;
    document.getElementById('lessonModal').style.display = 'flex';
    toggleMediaFields(); // Initial toggle
}

function closeLessonModal() {
    document.getElementById('lessonModal').style.display = 'none';
}

function editLesson(id) {
    window.location.href = 'edit-lesson.php?id=' + id + '&course_id=<?= $course_id ?>';
}

function toggleMediaFields() {
    const type = document.getElementById('media_type_select').value;
    const urlGroup = document.getElementById('media_url_group');
    
    if (type === 'text') {
        urlGroup.style.display = 'none';
    } else {
        urlGroup.style.display = 'block';
    }
}
</script>

<?php
});
?>
