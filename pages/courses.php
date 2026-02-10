<?php
require_once '../includes/auth_middleware.php'; // Ensureif (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../includes/config/root.php';
require_once path('includes/config/database.php');
require_once path('includes/classes/Database.php');

$db = (new Database())->getConnection();

// Include Header
include path('includes/components/header.php'); 

// Fetch All Published Courses with Teacher & Category info
$sql = "SELECT c.*, u.full_name as teacher_name, cat.name as category_name, cat.slug as category_slug 
        FROM courses c 
        LEFT JOIN users u ON c.teacher_id = u.id 
        LEFT JOIN categories cat ON c.category_id = cat.id 
        WHERE c.status = 'published' 
        ORDER BY c.created_at DESC";
$stmt = $db->prepare($sql);
$stmt->execute();
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

include '../includes/components/header.php';
?>

<!-- Courses Page Header -->
<section class="page-header" style="background: linear-gradient(135deg, var(--primary-color), var(--accent-color)); padding: 80px 20px; text-align: center; color: #fff;">
    <div class="container">
        <h1 style="font-size: 2.5rem; margin-bottom: 15px;">جميع الدورات</h1>
        <p style="font-size: 1.2rem; opacity: 0.9;">اكتشف مجموعة واسعة من الدورات التعليمية الإسلامية</p>
    </div>
</section>

<!-- Search and Filter Section -->
<section class="courses-filter" style="padding: 40px 20px; background: #f9f9f9;">
    <div class="container">
        <div style="display: flex; gap: 20px; flex-wrap: wrap; align-items: center; justify-content: center;">
            <div style="flex: 1; min-width: 250px; max-width: 400px;">
                <input type="text" id="search-courses" placeholder="ابحث عن دورة..." 
                       style="width: 100%; padding: 12px 20px; border: 2px solid #ddd; border-radius: 25px; font-size: 1rem; direction: rtl;">
            </div>
            <select id="filter-category" style="padding: 12px 20px; border: 2px solid #ddd; border-radius: 25px; font-size: 1rem; cursor: pointer; min-width: 200px;">
                <option value="">جميع التصنيفات</option>
                <option value="quran">القرآن الكريم</option>
                <option value="fiqh">الفقه</option>
                <option value="hadith">الحديث الشريف</option>
                <option value="arabic">اللغة العربية</option>
                <option value="aqeedah">العقيدة</option>
            </select>
            <select id="filter-level" style="padding: 12px 20px; border: 2px solid #ddd; border-radius: 25px; font-size: 1rem; cursor: pointer; min-width: 150px;">
                <option value="">جميع المستويات</option>
                <option value="beginner">مبتدئ</option>
                <option value="intermediate">متوسط</option>
                <option value="advanced">متقدم</option>
            </select>
            <button id="favFilter" style="padding: 12px 20px; border: 2px solid #ddd; border-radius: 25px; font-size: 1rem; cursor: pointer; min-width: 150px; background: #fff;"><i class="fas fa-heart" style="color:#e74c3c;"></i> المفضلة</button>
            <button id="freeFilter" style="padding: 12px 20px; border: 2px solid #ddd; border-radius: 25px; font-size: 1rem; cursor: pointer; min-width: 150px; background: #fff;"><i class="fas fa-tag" style="color:#10B981;"></i> مجانية فقط</button>
        </div>
    </div>
</section>

<!-- All Courses Section -->
<section class="all-courses" style="padding: 60px 20px;">
    <div class="container">
        <div class="courses-grid" id="courses-container">
            <?php if (count($courses) > 0): ?>
                <?php foreach ($courses as $course): ?>
                    <?php 
                        // Determine badge based on logic
                        $level = $course['level'] ?? 'beginner'; // Default to beginner if missing
                        $price = $course['price'] ?? 0;
                        
                        $badge = '';
                        if ($level == 'beginner') $badge = 'جديد';
                        elseif ($price == 0) $badge = 'مجاني';
                        elseif ($price > 50) $badge = 'مميز';
                        
                        // Map category slug
                        $cat_slug = $course['category_slug'] ?? 'general';
                    ?>
                    <div class="course-card" 
                         data-id="course_<?= $course['id'] ?>" 
                         data-category="<?= $cat_slug ?>" 
                         data-level="<?= $level ?>" 
                         data-price="<?= $price ?>">
                        <div class="course-image">
                            <?php 
                                $thumb = $course['thumbnail'] ?? ''; // Handle missing thumbnail
                                if ($thumb && strpos($thumb, 'http') !== 0 && strpos($thumb, '../') !== 0) {
                                    $thumb = '../' . $thumb;
                                }
                                $thumb = $thumb ?: url('assets/images/placeholder.jpg'); // Use url() for placeholder
                            ?>
                            <img src="<?= htmlspecialchars($thumb) ?>" alt="<?= htmlspecialchars($course['title']) ?>" onerror="this.src='<?= url('assets/images/placeholder.jpg') ?>'">
                            <?php if($badge): ?><span class="badge"><?= $badge ?></span><?php endif; ?>
                            <button class="wishlist-btn fav-btn" data-id="<?= $course['id'] ?>"><i class="far fa-heart"></i></button>
                        </div>
                        <div class="course-content">
                            <h3><?= htmlspecialchars($course['title']) ?></h3>
                            <p><?= mb_strimwidth(htmlspecialchars($course['description']), 0, 120, '...') ?></p>
                            <p class="teacher">المعلم: <?= htmlspecialchars($course['teacher_name'] ?? 'غير محدد') ?></p>
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 15px;">
                                <div class="course-footer">
                        <div class="rating">
                            <i class="fas fa-star"></i>
                            <span><?= number_format($course['rating'] ?? 0, 1) ?></span>
                        </div>
                        <a href="<?= url('pages/course-details.php?id=' . $course['id']) ?>" class="btn-enroll">التفاصيل</a>
                    </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center" style="grid-column: 1 / -1; padding: 40px;">
                    <h3 style="color: #6c757d;">لا توجد دورات متاحة حالياً.</h3>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- No Results Message (For JS filter) -->
        <div id="no-results" style="display: none; text-align: center; padding: 60px 20px; grid-column: 1 / -1;">
            <h3 style="color: var(--muted-text); margin-bottom: 15px;">لا توجد نتائج مطابقة للبحث</h3>
            <p style="color: var(--muted-text);">جرب تغيير خيارات البحث أو الفلترة</p>
        </div>
    </div>
</section>

<!-- Quick View Modal (Updated for Dynamic content if needed) -->
<div class="quick-view" id="quickView">
    <div class="quick-box">
        <span class="close">&times;</span>
        <h3 id="qTitle"></h3>
        <p id="qDesc"></p>
        <a id="qLink" class="btn-primary">اذهب للدورة</a>
    </div>
</div>

<script>
// Search and Filter Functionality
document.addEventListener('DOMContentLoaded', () => {
    // Check for Highlight URL Parameter
    const urlParams = new URLSearchParams(window.location.search);
    const highlightId = urlParams.get('highlight');

    if (highlightId) {
        setTimeout(() => {
            const targetCard = document.querySelector(`.course-card[data-id="course_${highlightId}"]`) || 
                               document.querySelector(`.course-card a[href*="id=${highlightId}"]`).closest('.course-card');
            
            if (targetCard) {
                targetCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
                targetCard.style.border = '2px solid var(--primary-color)';
                targetCard.style.transform = 'scale(1.02)';
                targetCard.style.boxShadow = '0 0 20px rgba(16, 185, 129, 0.4)';
            }
        }, 500);
    }

    const searchInput = document.getElementById('search-courses');
    const categoryFilter = document.getElementById('filter-category');
    const levelFilter = document.getElementById('filter-level');
    const noResults = document.getElementById('no-results');
    const courseCards = document.querySelectorAll('.course-card');

    // Filter Buttons
    const favFilter = document.getElementById('favFilter');
    const freeFilter = document.getElementById('freeFilter');
    const favBtns = document.querySelectorAll('.fav-btn');

    // Favorites Logic
    favBtns.forEach(btn => {
        const id = btn.dataset.id;
        let favorites = JSON.parse(localStorage.getItem('favorites')) || [];
        if (favorites.includes(id)) btn.classList.add('active');

        btn.addEventListener('click', (e) => {
            e.preventDefault(); e.stopPropagation();
            favorites = JSON.parse(localStorage.getItem('favorites')) || [];
            if (favorites.includes(id)) {
                favorites = favorites.filter(f => f !== id);
                btn.classList.remove('active');
            } else {
                favorites.push(id);
                btn.classList.add('active');
            }
            localStorage.setItem('favorites', JSON.stringify(favorites));
            updateAllFilters();
        });
    });
    
    // Toggle Filter Button Helper
    const toggleFilterBtn = (btn, activeColor, defaultIconColor) => {
        btn.classList.toggle('active');
        const isActive = btn.classList.contains('active');
        const icon = btn.querySelector('i');
        
        if(isActive) {
            btn.style.background = activeColor;
            btn.style.color = '#fff';
            if(icon) icon.style.color = '#fff';
        } else {
            btn.style.background = '#fff';
            btn.style.color = '#000';
            if(icon) icon.style.color = defaultIconColor;
        }
        updateAllFilters();
    };

    if (favFilter) favFilter.addEventListener('click', () => toggleFilterBtn(favFilter, '#e74c3c', '#e74c3c'));
    if (freeFilter) freeFilter.addEventListener('click', () => toggleFilterBtn(freeFilter, '#10B981', '#10B981'));
    
    // Main Filter Function
    function updateAllFilters() {
        const isFavActive = favFilter && favFilter.classList.contains('active');
        const isFreeActive = freeFilter && freeFilter.classList.contains('active');
        let visible = 0;

        courseCards.forEach(card => {
            const title = card.querySelector('h3').textContent.toLowerCase();
            const desc = card.querySelector('p').textContent.toLowerCase();
            const cat = card.dataset.category || '';
            const lvl = card.dataset.level || '';
            const price = card.dataset.price || 0;
            
            // Basic Filters
            let searchOk = (!searchInput.value || title.includes(searchInput.value) || desc.includes(searchInput.value)) &&
                          (!categoryFilter.value || categoryFilter.value === cat) &&
                          (!levelFilter.value || levelFilter.value === lvl);
            
            // Favorites Filter
            let favOk = true;
            if (isFavActive) {
                const favBtn = card.querySelector('.fav-btn');
                favOk = favBtn && favBtn.classList.contains('active');
            }

            // Free Filter
            let freeOk = true;
            if (isFreeActive) {
                freeOk = (price == '0');
            }
            
            if (searchOk && favOk && freeOk) {
                card.classList.remove('hide');
                visible++;
            } else {
                card.classList.add('hide');
            }
        });

        noResults.style.display = visible ? 'none' : 'block';
    }
    
    if (searchInput) searchInput.addEventListener('input', updateAllFilters);
    if (categoryFilter) categoryFilter.addEventListener('change', updateAllFilters);
    if (levelFilter) levelFilter.addEventListener('change', updateAllFilters);

    // Quick View Modal
    const modal = document.getElementById('quickView');
    if (modal) {
        document.querySelectorAll('.course-card .course-image').forEach(img => {
            img.addEventListener('click', function() {
                const card = this.closest('.course-card');
                const title = card.querySelector('h3').innerText;
                const desc = card.querySelector('p').innerText;
                const link = card.querySelector('.btn-primary').href;
                
                document.getElementById('qTitle').innerText = title;
                document.getElementById('qDesc').innerText = desc;
                document.getElementById('qLink').href = link;
                modal.classList.add('active');
            });
        });

        modal.querySelector('.close').onclick = () => modal.classList.remove('active');
        modal.addEventListener('click', (e) => { if (e.target === modal) modal.classList.remove('active'); });
    }
});
</script>

<style>
/* CSS to handle hidden state */
.course-card.hide {
    display: none !important;
}
.course-content {
    display: flex;
    flex-direction: column;
    height: 100%;
}
</style>

<?php include path('includes/components/footer.php'); ?>