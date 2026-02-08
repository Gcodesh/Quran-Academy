<?php
require_once '../../pages/dashboard/layout.php';
require_once '../../includes/config/database.php';
require_once '../../includes/classes/Database.php';

$page_title = 'دوراتي';

render_dashboard_layout(function() {
    $db = new Database();
    $conn = $db->getConnection();
    $teacher_id = $_SESSION['user_id'];

    // Handle Delete
    if (isset($_POST['delete_id'])) {
        $del_id = $_POST['delete_id'];
        $stmt = $conn->prepare("DELETE FROM courses WHERE id = ? AND teacher_id = ?");
        if ($stmt->execute([$del_id, $teacher_id])) {
            echo '<div class="alert success">تم حذف الدورة بنجاح</div>';
        }
    }

    $stmt = $conn->prepare("SELECT * FROM courses WHERE teacher_id = ? ORDER BY created_at DESC");
    $stmt->execute([$teacher_id]);
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="dash-card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <div>
            <h2 style="margin: 0;">إدارة دوراتي</h2>
            <p style="color: var(--dark-500); margin: 5px 0 0;">قم بإضافة وتعديل المحتوى التعليمي الخاص بك</p>
        </div>
        <a href="../dashboard/add-course.php" class="btn-primary-glow" style="text-decoration: none;">
            <i class="fas fa-plus"></i> إضافة دورة جديدة
        </a>
    </div>

    <?php if (empty($courses)): ?>
        <div style="text-align: center; padding: 60px 20px;">
            <div style="background: var(--light-100); width: 80px; height: 80px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; color: var(--dark-400); font-size: 2rem;">
                <i class="fas fa-book-open"></i>
            </div>
            <h3 style="margin-bottom: 10px;">لا توجد دورات حالياً</h3>
            <p style="color: var(--dark-500); max-width: 400px; margin: 0 auto 20px;">ابدأ رحلتك التعليمية وقم بإنشاء أول دورة لك على المنصة.</p>
            <a href="../dashboard/add-course.php" class="btn-outline-primary">إنشاء دورة الآن</a>
        </div>
    <?php else: ?>
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 25px;">
            <?php foreach ($courses as $course): ?>
            <div style="border: 1px solid var(--light-200); border-radius: 16px; overflow: hidden; transition: 0.3s; background: white;" onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='var(--shadow-md)'" onmouseout="this.style.transform='none'; this.style.boxShadow='none'">
                <div style="height: 160px; background: var(--light-100); position: relative;">
                    <?php if($course['image']): ?>
                        <img src="<?= htmlspecialchars($course['image']) ?>" alt="" style="width: 100%; height: 100%; object-fit: cover;">
                    <?php else: ?>
                        <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; color: var(--dark-400); font-size: 2rem;"><i class="fas fa-image"></i></div>
                    <?php endif; ?>
                    
                    <div style="position: absolute; top: 15px; left: 15px;">
                        <?php
                            $st = $course['status'];
                            $bg = $st == 'published' ? '#dcfce7' : ($st == 'rejected' ? '#fee2e2' : ($st == 'pending' ? '#fef3c7' : '#f1f5f9'));
                            $col = $st == 'published' ? '#166534' : ($st == 'rejected' ? '#991b1b' : ($st == 'pending' ? '#92400e' : '#475569'));
                            $lbl = $st == 'published' ? 'منشور' : ($st == 'rejected' ? 'مرفوض' : ($st == 'pending' ? 'مراجعة' : 'مسودة'));
                        ?>
                        <span style="background: <?= $bg ?>; color: <?= $col ?>; padding: 5px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: 600;"><?= $lbl ?></span>
                    </div>
                </div>
                <div style="padding: 20px;">
                    <h3 style="margin: 0 0 10px; font-size: 1.1rem; line-height: 1.4; height: 3.2em; overflow: hidden;"><?= htmlspecialchars($course['title']) ?></h3>
                    <p style="color: var(--dark-500); font-size: 0.9rem; margin-bottom: 20px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;"><?= htmlspecialchars($course['description']) ?></p>
                    
                    <div style="display: flex; gap: 10px; border-top: 1px solid var(--light-100); padding-top: 15px;">
                        <a href="edit-course.php?id=<?= $course['id'] ?>" class="btn-icon-flex" style="flex: 1; text-decoration: none; display: flex; align-items: center; justify-content: center; gap: 5px; padding: 8px; background: var(--light-50); color: var(--dark-700); border-radius: 8px; font-size: 0.9rem;">
                            <i class="fas fa-edit"></i> تعديل
                        </a>
                        <form method="POST" style="display: block;" onsubmit="return confirm('حذف الدورة؟');">
                            <input type="hidden" name="delete_id" value="<?= $course['id'] ?>">
                            <button type="submit" style="width: 36px; height: 36px; background: #fee2e2; color: #ef4444; border: none; border-radius: 8px; cursor: pointer; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<style>
.alert { padding: 15px; background: #dcfce7; color: #166534; border-radius: 8px; margin-bottom: 20px; }
.btn-outline-primary { border: 1px solid var(--primary-500); color: var(--primary-600); padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 600; display: inline-block; }
.btn-outline-primary:hover { background: var(--primary-50); }
</style>
<?php
});
?>
