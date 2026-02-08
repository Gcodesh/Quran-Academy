<?php
require_once __DIR__ . '/admin_middleware.php';
require_once '../../pages/dashboard/layout.php';
require_once '../../includes/config/database.php';
require_once '../../includes/classes/Database.php';

$page_title = 'إدارة التصنيفات';

render_dashboard_layout(function() {
    $db = new Database();
    $conn = $db->getConnection();
    
    $message = '';
    
    // Handle Add/Delete
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['action']) && $_POST['action'] === 'delete') {
            $id = $_POST['category_id'];
            $stmt = $conn->prepare("DELETE FROM categories WHERE id = ?");
            if ($stmt->execute([$id])) {
                $message = 'تم حذف التصنيف بنجاح';
            }
        } elseif (isset($_POST['name'])) { // Add/Edit
            $name = $_POST['name'];
            $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name))); // Simple slugify
            $icon = $_POST['icon'] ?? 'fas fa-folder';
            
            $stmt = $conn->prepare("INSERT INTO categories (name, slug, icon) VALUES (?, ?, ?)");
            if ($stmt->execute([$name, $slug, $icon])) {
                $message = 'تم إضافة التصنيف بنجاح';
            } else {
                $message = 'حدث خطأ (ربما الاسم مكرر)';
            }
        }
    }
    
    // Fetch Categories with Course Count
    $categories = $conn->query("
        SELECT c.*, COUNT(co.id) as course_count 
        FROM categories c 
        LEFT JOIN courses co ON c.id = co.category_id 
        GROUP BY c.id
        ORDER BY c.created_at DESC
    ")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="admin-categories-page">
    <div class="page-header">
        <div class="header-content">
            <div class="header-title">
                <h1><i class="fas fa-tags"></i> تصنيفات الدورات</h1>
                <p>تنظيم المحتوى، وإنشاء أقسام تعليمية جديدة</p>
            </div>
            <button onclick="openModal()" class="add-btn">
                <i class="fas fa-plus"></i>
                تصنيف جديد
            </button>
        </div>
    </div>

    <?php if($message): ?>
        <div class="alert success-alert">
            <i class="fas fa-check-circle"></i> <?= $message ?>
        </div>
    <?php endif; ?>

    <div class="categories-grid">
        <?php foreach($categories as $cat): ?>
            <div class="category-card">
                <div class="cat-icon">
                    <i class="<?= htmlspecialchars($cat['icon']) ?>"></i>
                </div>
                <div class="cat-info">
                    <h3><?= htmlspecialchars($cat['name']) ?></h3>
                    <p><?= $cat['course_count'] ?> دورة</p>
                </div>
                <div class="cat-actions">
                    <button class="action-icon edit" title="تعديل"><i class="fas fa-pen"></i></button>
                    <form method="POST" onsubmit="return confirm('هل أنت متأكد؟ سيتم حذف التصنيف وإلغاء ارتباط الدورات به.')">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="category_id" value="<?= $cat['id'] ?>">
                        <button class="action-icon delete" title="حذف"><i class="fas fa-trash"></i></button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
        
        <!-- Add New Card Placeholder -->
        <div class="category-card add-new" onclick="openModal()">
            <div class="add-icon"><i class="fas fa-plus"></i></div>
            <h3>إضافة جديد</h3>
        </div>
    </div>
</div>

<!-- Add Modal -->
<div id="categoryModal" class="modal-overlay">
    <div class="modal-content glass-panel">
        <div class="modal-header">
            <h3><i class="fas fa-plus-circle"></i> تصنيف جديد</h3>
            <button onclick="closeModal()" class="close-btn"><i class="fas fa-times"></i></button>
        </div>
        <form method="POST">
            <div class="form-group">
                <label>اسم التصنيف</label>
                <input type="text" name="name" required placeholder="مثلاً: التجويد المصور" class="form-input">
            </div>
            <div class="form-group">
                <label>أيقونة (FontAwesome)</label>
                <div class="icon-input-group">
                    <input type="text" name="icon" value="fas fa-book" class="form-input">
                    <a href="https://fontawesome.com/icons" target="_blank" class="icon-link">عرض الأيقونات</a>
                </div>
            </div>
            <button type="submit" class="submit-btn">حفظ التصنيف</button>
        </form>
    </div>
</div>

<script>
function openModal() {
    document.getElementById('categoryModal').classList.add('active');
}
function closeModal() {
    document.getElementById('categoryModal').classList.remove('active');
}
// Close on outside click
window.onclick = function(event) {
    if (event.target == document.getElementById('categoryModal')) {
        closeModal();
    }
}
</script>

<style>
.admin-categories-page { max-width: 1200px; margin: 0 auto; }

.header-content { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 40px; }
.header-title h1 { color: #1e293b; margin: 0 0 5px; font-size: 1.8rem; }
.header-title p { color: #64748b; margin: 0; }

.add-btn { background: #8b5cf6; color: white; border: none; padding: 12px 25px; border-radius: 12px; cursor: pointer; font-size: 1rem; font-weight: 600; display: flex; align-items: center; gap: 8px; transition: 0.3s; }
.add-btn:hover { background: #7c3aed; transform: translateY(-2px); box-shadow: 0 4px 15px rgba(139, 92, 246, 0.4); }

.categories-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 25px; }

.category-card { background: white; padding: 25px; border-radius: 20px; box-shadow: 0 4px 20px rgba(0,0,0,0.04); display: flex; align-items: center; gap: 20px; position: relative; transition: 0.3s; border: 1px solid transparent; }
.category-card:hover { transform: translateY(-5px); box-shadow: 0 10px 30px rgba(0,0,0,0.08); }

.cat-icon { width: 50px; height: 50px; background: #f0f9ff; color: #0ea5e9; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; }
.cat-info { flex: 1; }
.cat-info h3 { margin: 0 0 4px; font-size: 1.1rem; color: #1e293b; }
.cat-info p { margin: 0; color: #64748b; font-size: 0.9rem; }

.cat-actions { display: flex; gap: 8px; opacity: 0; transition: 0.2s; }
.category-card:hover .cat-actions { opacity: 1; }

.action-icon { width: 32px; height: 32px; border-radius: 8px; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: 0.2s; }
.action-icon.edit { background: #f1f5f9; color: #64748b; }
.action-icon.edit:hover { background: #e2e8f0; color: #1e293b; }
.action-icon.delete { background: #fee2e2; color: #ef4444; }
.action-icon.delete:hover { background: #fecaca; }

/* Add New Card Style */
.category-card.add-new { border: 2px dashed #cbd5e1; box-shadow: none; justify-content: center; flex-direction: column; cursor: pointer; }
.category-card.add-new:hover { border-color: #8b5cf6; background: #f5f3ff; }
.category-card.add-new .add-icon { width: 40px; height: 40px; background: #e2e8f0; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #64748b; margin-bottom: 5px; transition: 0.3s; }
.category-card.add-new:hover .add-icon { background: #8b5cf6; color: white; }

/* Modal */
.modal-overlay { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; opacity: 0; visibility: hidden; transition: 0.3s; z-index: 1000; backdrop-filter: blur(4px); }
.modal-overlay.active { opacity: 1; visibility: visible; }

.modal-content { background: white; padding: 30px; border-radius: 20px; width: 400px; max-width: 90%; transform: translateY(20px); transition: 0.3s; }
.modal-overlay.active .modal-content { transform: translateY(0); }

.modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; }
.modal-header h3 { margin: 0; font-size: 1.3rem; }
.close-btn { background: none; border: none; font-size: 1.2rem; cursor: pointer; color: #94a3b8; }
.close-btn:hover { color: #ef4444; }

.form-group { margin-bottom: 20px; }
.form-group label { display: block; margin-bottom: 8px; font-weight: 600; color: #334155; }
.form-input { width: 100%; padding: 12px; border: 2px solid #e2e8f0; border-radius: 10px; font-family: inherit; }
.form-input:focus { border-color: #8b5cf6; outline: none; }
.icon-link { font-size: 0.85rem; color: #8b5cf6; display: block; margin-top: 5px; text-decoration: none; }

.submit-btn { width: 100%; padding: 12px; background: #10b981; color: white; border: none; border-radius: 10px; font-size: 1rem; font-weight: 600; cursor: pointer; transition: 0.3s; }
.submit-btn:hover { background: #059669; }

.success-alert { background: #dcfce7; color: #166534; padding: 15px; border-radius: 12px; margin-bottom: 25px; display: flex; align-items: center; gap: 10px; }
</style>
</div>

<?php
});
?>
