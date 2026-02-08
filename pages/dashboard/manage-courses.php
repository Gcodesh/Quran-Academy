<?php
require_once '../../includes/auth_middleware.php';
checkAuth(['teacher']);

include '../../includes/components/header.php';
include '../../includes/components/dashboard_sidebar.php';

require_once '../../includes/classes/Database.php';
$db = new Database();
$conn = $db->getConnection();

$teacher_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM courses WHERE teacher_id = ?");
$stmt->execute([$teacher_id]);
$courses = $stmt->fetchAll();
?>

<main class="dashboard-content">
    <div class="container-fluid">
        <div class="dashboard-header-flex">
            <div>
                <h1>إدارة دوراتي 📚</h1>
                <p>يمكنك هنا إدارة محتوى دوراتك وتحديثها.</p>
            </div>
            <a href="add-course.php" class="btn-primary-glow">إضافة دورة جديدة</a>
        </div>

        <div class="content-box glass-card">
            <?php if (empty($courses)): ?>
                <div class="empty-state">
                    <i class="fas fa-book-open"></i>
                    <h3>لا توجد دورات حالياً</h3>
                    <p>ابدأ بإنشاء أول دورة تعليمية لك الآن!</p>
                    <a href="add-course.php" class="btn-primary">أنشئ دورتك الأولى</a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="modern-table">
                        <thead>
                            <tr>
                                <th>اسم الدورة</th>
                                <th>الحالة</th>
                                <th>تاريخ الإنشاء</th>
                                <th>الطلاب</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($courses as $course): ?>
                            <tr>
                                <td class="course-name-cell">
                                    <img src="<?= $course['image'] ?: 'https://placehold.co/50x50?text=Course' ?>" alt="">
                                    <span><?= htmlspecialchars($course['title']) ?></span>
                                </td>
                                <td>
                                    <?php 
                                    $status = $course['status'] ?? 'draft';
                                    $status_map = [
                                        'draft' => ['الدرودة مسودة', 'status-draft'],
                                        'pending' => ['بانتظار الموافقة', 'status-pending'],
                                        'published' => ['منشورة', 'status-published']
                                    ];
                                    ?>
                                    <span class="badge-status <?= $status_map[$status][1] ?>">
                                        <?= $status_map[$status][0] ?>
                                    </span>
                                </td>
                                <td><?= date('Y/m/d', strtotime($course['created_at'])) ?></td>
                                <td>0 طلاب</td> <!-- Placeholder -->
                                <td class="actions-cell">
                                    <a href="edit-course.php?id=<?= $course['id'] ?>" class="btn-icon" title="تعديل"><i class="fas fa-edit"></i></a>
                                    <a href="lessons.php?course_id=<?= $course['id'] ?>" class="btn-icon" title="الدروس"><i class="fas fa-list-ul"></i></a>
                                    <button class="btn-icon delete-course" data-id="<?= $course['id'] ?>" title="حذف"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<style>
/* Dashboard Styles (should eventually move to a css file) */
.status-draft { background: #f1f5f9; color: #475569; }
.status-pending { background: #fef3c7; color: #92400e; }
.status-published { background: #dcfce7; color: #166534; }

.btn-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 35px;
    height: 35px;
    border-radius: 8px;
    background: #f1f5f9;
    color: var(--dark-600);
    margin-left: 5px;
    border: none;
    cursor: pointer;
    transition: var(--transition);
}

.btn-icon:hover {
    background: var(--primary-100);
    color: var(--primary-700);
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
}

.empty-state i {
    font-size: 4rem;
    color: var(--light-400);
    margin-bottom: 20px;
}
</style>

<?php include '../../includes/components/footer.php'; ?>
