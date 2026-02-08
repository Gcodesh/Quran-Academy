<?php
require_once '../../includes/auth_middleware.php';
checkAuth(['teacher']);

include '../../includes/components/header.php';
include '../../includes/components/dashboard_sidebar.php';

$csrf_token = generateCsrfToken();
?>

<main class="dashboard-content">
    <div class="container">
        <div class="header-with-back">
            <a href="manage-courses.php" class="back-link"><i class="fas fa-chevron-right"></i> العودة للقائمة</a>
            <h1>إضافة دورة جديدة ✍️</h1>
        </div>

        <div class="content-box glass-card mt-4">
            <form action="../../api/courses.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                <input type="hidden" name="action" value="create">

                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group mb-4">
                            <label for="title">عنوان الدورة</label>
                            <input type="text" id="title" name="title" class="form-control-modern" required placeholder="مثال: أصول التجويد للمبتدئين">
                        </div>

                        <div class="form-group mb-4">
                            <label for="description">وصف الدورة</label>
                            <textarea id="description" name="description" class="form-control-modern" rows="6" placeholder="اكتب وصفاً شاملاً لما سيتعلمه الطالب..."></textarea>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group mb-4">
                            <label>صورة الغلاف</label>
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
                            <p class="form-hint">يفضل أن تكون الأبعاد 800×600 بكسل.</p>
                        </div>

                        <div class="form-group mb-4">
                            <label for="category">التصنيف</label>
                            <select id="category" name="category" class="form-control-modern">
                                <option value="quran">القرآن الكريم</option>
                                <option value="fiqh">الفقه</option>
                                <option value="hadith">الحديث الشريف</option>
                                <option value="arabic">اللغة العربية</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-footer mt-4">
                    <button type="submit" class="btn-primary-glow px-5">حفظ وإرسال للمراجعة</button>
                    <button type="submit" name="status" value="draft" class="btn-outline-dark me-3">حفظ كمسودة</button>
                </div>
            </form>
        </div>
    </div>
</main>

<style>
.header-with-back {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.back-link {
    color: var(--primary-600);
    text-decoration: none;
    font-weight: 700;
    font-size: 0.9rem;
}

.form-control-modern {
    width: 100%;
    padding: 12px 20px;
    border-radius: 12px;
    border: 1px solid var(--light-300);
    background: white;
    font-family: inherit;
    transition: var(--transition);
}

.form-control-modern:focus {
    border-color: var(--primary-500);
    outline: none;
    box-shadow: 0 0 0 4px rgba(20, 184, 166, 0.1);
}

.image-upload-wrapper {
    width: 100%;
    aspect-ratio: 4/3;
    background: #f1f5f9;
    border: 2px dashed var(--light-300);
    border-radius: 16px;
    overflow: hidden;
    position: relative;
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
}

#image-preview {
    width: 100%;
    height: 100%;
    object-fit: cover;
    position: absolute;
    top: 0;
    left: 0;
}

.form-hint {
    font-size: 0.8rem;
    color: var(--dark-400);
    margin-top: 8px;
}

.form-footer {
    border-top: 1px solid var(--light-100);
    padding-top: 30px;
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

<?php include '../../includes/components/footer.php'; ?>
