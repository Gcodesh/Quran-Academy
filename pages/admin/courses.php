<?php
require_once __DIR__ . '/admin_middleware.php';
require_once '../../pages/dashboard/layout.php';
require_once '../../includes/config/database.php';
require_once '../../includes/classes/Database.php';

$page_title = 'إدارة الدورات';

render_dashboard_layout(function() {
    $db = new Database();
    $conn = $db->getConnection();
    
    // Pagination
    $page = $_GET['page'] ?? 1;
    $limit = 9;
    $offset = ($page - 1) * $limit;
    
    // Filters
    $status_filter = $_GET['status'] ?? 'all';
    $search = $_GET['q'] ?? '';
    
    $where = ["1=1"];
    $params = [];
    
    if ($status_filter != 'all') {
        $where[] = "c.status = ?";
        $params[] = $status_filter;
    }
    
    if ($search) {
        $where[] = "(c.title LIKE ? OR c.description LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }
    
    // Fetch courses
    $where_sql = implode(' AND ', $where);
    $total_courses = $conn->prepare("SELECT COUNT(*) FROM courses c WHERE $where_sql");
    $total_courses->execute($params);
    $total = $total_courses->fetchColumn();
    $pages = ceil($total / $limit);
    
    $sql = "SELECT c.*, u.name as teacher_name, cat.name as category_name 
            FROM courses c 
            LEFT JOIN users u ON c.teacher_id = u.id 
            LEFT JOIN categories cat ON c.category_id = cat.id 
            WHERE $where_sql 
            ORDER BY c.created_at DESC 
            LIMIT $limit OFFSET $offset";
            
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="admin-courses-page">
    
    <!-- Premium Header -->
    <div class="page-header">
        <div class="header-content">
            <div class="header-title">
                <h1><i class="fas fa-graduation-cap"></i> إدارة الدورات</h1>
                <p>تحكم في المحتوى التعليمي، راجع، وعدّل الدورات بسهولة</p>
            </div>
            <a href="add-course.php" class="add-btn">
                <i class="fas fa-plus"></i>
                دورة جديدة
            </a>
        </div>
        
        <!-- Filters Bar -->
        <div class="filters-bar glass-panel">
            <form method="GET" class="filters-form">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" name="q" value="<?= htmlspecialchars($search) ?>" placeholder="ابحث عن دورة...">
                </div>
                
                <div class="filter-group">
                    <select name="status" onchange="this.form.submit()" class="custom-select">
                        <option value="all" <?= $status_filter == 'all' ? 'selected' : '' ?>>جميع الحالات</option>
                        <option value="published" <?= $status_filter == 'published' ? 'selected' : '' ?>>منشور</option>
                        <option value="pending" <?= $status_filter == 'pending' ? 'selected' : '' ?>>قيد المراجعة</option>
                        <option value="draft" <?= $status_filter == 'draft' ? 'selected' : '' ?>>مسودة</option>
                    </select>
                </div>
            </form>
        </div>
    </div>

    <!-- Courses Grid -->
    <?php if (empty($courses)): ?>
        <div class="empty-state">
            <div class="empty-icon">
                <i class="fas fa-search"></i>
            </div>
            <h3>لا توجد نتائج</h3>
            <p>لم يتم العثور على دورات تطابق بحثك</p>
            <?php if($status_filter != 'all' || $search): ?>
                <a href="courses.php" class="reset-btn">إعادة تعيين الفلاتر</a>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="courses-grid">
            <?php foreach ($courses as $course): ?>
                <?php 
                    $thumb = $course['thumbnail'] ?: $course['image'];
                    if ($thumb && strpos($thumb, 'http') !== 0 && strpos($thumb, '../') !== 0) {
                        $thumb = '../../' . $thumb;
                    }
                    $thumb = $thumb ?: 'https://placehold.co/400x250?text=No+Image';
                    
                    $status_class = $course['status'];
                    $status_label = [
                        'published' => 'منشور',
                        'pending' => 'مراجعة',
                        'draft' => 'مسودة',
                        'rejected' => 'مرفوض'
                    ][$course['status']] ?? $course['status'];
                ?>
                <div class="course-card">
                    <div class="card-image">
                        <img src="<?= htmlspecialchars($thumb) ?>" alt="<?= htmlspecialchars($course['title']) ?>">
                        <div class="status-badge <?= $status_class ?>">
                            <?= $status_label ?>
                        </div>
                        <div class="price-badge">
                            <?= $course['price'] > 0 ? $course['price'] . ' ر.س' : 'مجاني' ?>
                        </div>
                    </div>
                    
                    <div class="card-content">
                        <div class="category">
                            <i class="fas fa-folder"></i>
                            <?= htmlspecialchars($course['category_name'] ?? 'عام') ?>
                        </div>
                        
                        <h3><?= htmlspecialchars($course['title']) ?></h3>
                        
                        <div class="teacher-info">
                            <i class="fas fa-chalkboard-teacher"></i>
                            <span><?= htmlspecialchars($course['teacher_name']) ?></span>
                        </div>
                        
                        <div class="card-actions">
                            <a href="edit-course.php?id=<?= $course['id'] ?>" class="action-btn edit" title="تعديل">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="../course-details.php?id=<?= $course['id'] ?>" class="action-btn view" title="عرض" target="_blank">
                                <i class="fas fa-external-link-alt"></i>
                            </a>
                            <button class="action-btn delete" onclick="deleteCourse(<?= $course['id'] ?>)" title="حذف">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Pagination -->
        <?php if ($pages > 1): ?>
            <div class="pagination">
                <?php for($i = 1; $i <= $pages; $i++): ?>
                    <a href="?page=<?= $i ?>&status=<?= $status_filter ?>&q=<?= $search ?>" 
                       class="page-link <?= $page == $i ? 'active' : '' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<style>
/* Page Styles */
.admin-courses-page {
    max-width: 1400px;
    margin: 0 auto;
}

/* Header */
.page-header {
    margin-bottom: 40px;
}

.header-content {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    margin-bottom: 25px;
}

.header-title h1 {
    font-size: 2rem;
    color: #1e293b;
    margin: 0 0 10px;
    display: flex;
    align-items: center;
    gap: 15px;
}

.header-title h1 i {
    color: #8b5cf6;
}

.header-title p {
    color: #64748b;
    margin: 0;
    font-size: 1.1rem;
}

.add-btn {
    background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
    color: white;
    padding: 12px 25px;
    border-radius: 12px;
    text-decoration: none;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 10px;
    box-shadow: 0 4px 15px rgba(139, 92, 246, 0.3);
    transition: all 0.3s;
}

.add-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(139, 92, 246, 0.4);
}

/* Filters */
.filters-bar {
    background: white;
    padding: 15px 25px;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.03);
    border: 1px solid rgba(0,0,0,0.05);
}

.filters-form {
    display: flex;
    gap: 20px;
}

.search-box {
    flex: 1;
    position: relative;
}

.search-box i {
    position: absolute;
    right: 15px;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
}

.search-box input {
    width: 100%;
    padding: 12px 45px 12px 15px;
    border: 2px solid #e2e8f0;
    border-radius: 10px;
    font-family: inherit;
    transition: all 0.3s;
}

.search-box input:focus {
    border-color: #8b5cf6;
    outline: none;
}

.custom-select {
    padding: 12px 35px 12px 15px;
    border: 2px solid #e2e8f0;
    border-radius: 10px;
    background: white;
    font-family: inherit;
    cursor: pointer;
    min-width: 180px;
}

/* Grid */
.courses-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 30px;
}

.course-card {
    background: white;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 4px 25px rgba(0,0,0,0.05);
    transition: all 0.3s;
    border: 1px solid rgba(0,0,0,0.05);
    display: flex;
    flex-direction: column;
}

.course-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 35px rgba(0,0,0,0.1);
}

.card-image {
    height: 200px;
    position: relative;
}

.card-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.status-badge {
    position: absolute;
    top: 15px;
    right: 15px;
    padding: 6px 14px;
    border-radius: 20px;
    font-weight: 600;
    font-size: 0.85rem;
    backdrop-filter: blur(5px);
}

.status-badge.published { background: rgba(220, 252, 231, 0.9); color: #166534; }
.status-badge.pending { background: rgba(254, 243, 199, 0.9); color: #92400e; }
.status-badge.draft { background: rgba(241, 245, 249, 0.9); color: #475569; }
.status-badge.rejected { background: rgba(254, 226, 226, 0.9); color: #991b1b; }

.price-badge {
    position: absolute;
    bottom: 15px;
    left: 15px;
    background: rgba(0,0,0,0.7);
    color: white;
    padding: 6px 12px;
    border-radius: 10px;
    font-weight: 600;
    font-size: 0.9rem;
    backdrop-filter: blur(5px);
}

.card-content {
    padding: 25px;
    flex: 1;
    display: flex;
    flex-direction: column;
}

.category {
    color: #8b5cf6;
    font-size: 0.85rem;
    font-weight: 600;
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    gap: 6px;
}

.card-content h3 {
    margin: 0 0 15px;
    font-size: 1.15rem;
    color: #1e293b;
    line-height: 1.5;
}

.teacher-info {
    display: flex;
    align-items: center;
    gap: 8px;
    color: #64748b;
    font-size: 0.95rem;
    margin-bottom: 25px;
    margin-top: auto;
}

.card-actions {
    display: flex;
    gap: 10px;
    border-top: 1px solid #f1f5f9;
    padding-top: 20px;
}

.action-btn {
    flex: 1;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    border: none;
    transition: all 0.3s;
    font-size: 1.1rem;
    text-decoration: none;
}

.action-btn.edit {
    background: #f1f5f9;
    color: #475569;
}
.action-btn.edit:hover { background: #e2e8f0; }

.action-btn.view {
    background: #e0e7ff;
    color: #4f46e5;
}
.action-btn.view:hover { background: #c7d2fe; }

.action-btn.delete {
    background: #fee2e2;
    color: #ef4444;
}
.action-btn.delete:hover { background: #fecaca; }

/* Empty State */
.empty-state {
    text-align: center;
    padding: 60px 20px;
    background: white;
    border-radius: 20px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.03);
}

.empty-icon {
    width: 80px;
    height: 80px;
    background: #f1f5f9;
    border-radius: 50%;
    margin: 0 auto 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    color: #94a3b8;
}

.reset-btn {
    display: inline-block;
    margin-top: 20px;
    color: #8b5cf6;
    text-decoration: none;
    font-weight: 600;
}

/* Pagination */
.pagination {
    display: flex;
    justify-content: center;
    gap: 10px;
    margin-top: 40px;
}

.page-link {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 10px;
    background: white;
    color: #64748b;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s;
    border: 1px solid #e2e8f0;
}

.page-link.active, .page-link:hover {
    background: #8b5cf6;
    color: white;
    border-color: #8b5cf6;
    box-shadow: 0 4px 10px rgba(139, 92, 246, 0.3);
}
</style>

<script>
function deleteCourse(id) {
    if(confirm('هل أنت متأكد من حذف هذه الدورة نهائياً؟')) {
        // Implement delete logic via API
        alert('سيتم تنفيذ حذف الدورة (API pending)');
    }
}
</script>

<?php
});
?>
