<?php
require_once '../../pages/dashboard/layout.php';
require_once '../../includes/auth_middleware.php'; // For csrf helper and checkAuth

$page_title = 'إضافة دورة جديدة';

render_dashboard_layout(function() {
    $csrf_token = generateCsrfToken();
?>
<div class="dash-card">
    <div class="header-with-back" style="display: flex; align-items: center; gap: 15px; margin-bottom: 30px;">
        <a href="my-courses.php" class="back-link" style="width: 40px; height: 40px; background: var(--light-100); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: var(--dark-600); text-decoration: none; transition: 0.2s;"><i class="fas fa-arrow-right"></i></a>
        <div>
            <h2 style="margin: 0;">إضافة دورة جديدة ✍️</h2>
            <p style="color: var(--dark-500); margin: 5px 0 0;">قم بإنشاء محتوى تعليمي مميز لطلابك</p>
        </div>
    </div>

    <form action="../../api/courses.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
        <input type="hidden" name="action" value="create">

        <div class="row" style="display: flex; flex-wrap: wrap; margin-right: -15px; margin-left: -15px;">
            <div class="col-md-8" style="flex: 0 0 66.666667%; max-width: 66.666667%; padding-right: 15px; padding-left: 15px;">
                <div class="form-group mb-4" style="margin-bottom: 25px;">
                    <label for="title" style="display: block; margin-bottom: 8px; font-weight: 600;">عنوان الدورة</label>
                    <input type="text" id="title" name="title" class="form-control-modern" required placeholder="مثال: أصول التجويد للمبتدئين">
                </div>

                <div class="form-group mb-4" style="margin-bottom: 25px;">
                    <label for="description" style="display: block; margin-bottom: 8px; font-weight: 600;">وصف الدورة</label>
                    <textarea id="description" name="description" class="form-control-modern" rows="10" placeholder="اكتب وصفاً شاملاً لما سيتعلمه الطالب..." style="resize: vertical;"></textarea>
                </div>
            </div>

            <div class="col-md-4" style="flex: 0 0 33.333333%; max-width: 33.333333%; padding-right: 15px; padding-left: 15px;">
                <div class="form-group mb-4" style="margin-bottom: 25px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600;">صورة الغلاف</label>
                    <div class="image-upload-wrapper">
                        <label for="image-upload" class="upload-label">
                            <div class="upload-placeholder">
                                <i class="fas fa-image"></i>
                                <span>اضغط لرفع صورة</span>
                            </div>
                            <img id="image-preview" src="#" alt="Preview" style="display:none;">
                        </label>
                        <input type="file" id="image-upload" name="image" accept="image/*" hidden>
                    </div>
                    <p class="form-hint" style="color: var(--dark-400); font-size: 0.85rem; margin-top: 5px;">يفضل أن تكون الأبعاد 800×600 بكسل.</p>
                </div>

                <div class="form-group mb-4" style="margin-bottom: 25px;">
                    <label for="category" style="display: block; margin-bottom: 8px; font-weight: 600;">التصنيف</label>
                    <select id="category" name="category" class="form-control-modern">
                        <option value="quran">القرآن الكريم</option>
                        <option value="fiqh">الفقه</option>
                        <option value="hadith">الحديث الشريف</option>
                        <option value="arabic">اللغة العربية</option>
                    </select>
                </div>
                
                 <div class="form-group mb-4" style="margin-bottom: 25px;">
                    <label for="price" style="display: block; margin-bottom: 8px; font-weight: 600;">سعر الدورة (ر.س)</label>
                    <input type="number" id="price" name="price" class="form-control-modern" placeholder="0.00" step="0.01">
                </div>
            </div>
        </div>

        <div class="form-footer mt-4" style="border-top: 1px solid var(--light-200); padding-top: 25px; margin-top: 25px; display: flex; gap: 15px;">
            <button type="submit" class="btn-primary-glow" style="padding: 12px 30px; font-size: 1rem;">حفظ وإرسال للمراجعة</button>
            <button type="submit" name="status" value="draft" class="btn-outline-dark" style="padding: 12px 30px; font-size: 1rem; background: var(--light-100); border: 1px solid var(--light-300); border-radius: 12px; cursor: pointer;">حفظ كمسودة</button>
        </div>
    </form>
</div>

<style>
.form-control-modern {
    width: 100%;
    padding: 12px 20px;
    border-radius: 12px;
    border: 1px solid var(--light-300);
    background: var(--light-50);
    font-family: inherit;
    transition: var(--transition);
    font-size: 0.95rem;
}

.form-control-modern:focus {
    border-color: var(--primary-500);
    outline: none;
    box-shadow: 0 0 0 4px rgba(20, 184, 166, 0.1);
    background: white;
}

.image-upload-wrapper {
    width: 100%;
    aspect-ratio: 4/3;
    background: var(--light-50);
    border: 2px dashed var(--light-300);
    border-radius: 16px;
    overflow: hidden;
    position: relative;
    transition: 0.3s;
}

.image-upload-wrapper:hover {
    border-color: var(--primary-400);
    background: var(--primary-50);
}

.upload-label {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
}

.upload-placeholder {
    text-align: center;
    color: var(--dark-400);
}

.upload-placeholder i {
    font-size: 2.5rem;
    margin-bottom: 10px;
    color: var(--dark-300);
}

#image-preview {
    width: 100%;
    height: 100%;
    object-fit: cover;
    position: absolute;
    top: 0;
    left: 0;
}
</style>

<script>
document.getElementById('image-upload').onchange = evt => {
    const [file] = evt.target.files;
    if (file) {
        const preview = document.getElementById('image-preview');
        const placeholder = document.querySelector('.upload-placeholder');
        preview.src = URL.createObjectURL(file);
        preview.style.display = 'block';
        placeholder.style.display = 'none';
    }
}
</script>
<?php
});
?>
