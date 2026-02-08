<?php
require_once __DIR__ . '/admin_middleware.php';
require_once '../../pages/dashboard/layout.php';
require_once '../../includes/auth_middleware.php';
require_once '../../includes/config/database.php';
require_once '../../includes/classes/Database.php';

$page_title = 'إضافة دورة جديدة';

$db = new Database();
$conn = $db->getConnection();
$categories = $conn->query("SELECT * FROM categories ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);

render_dashboard_layout(function() use ($categories) {
    $csrf_token = generateCsrfToken();
?>

<div class="add-course-wrapper">
    <!-- Animated Background -->
    <div class="animated-bg">
        <div class="floating-shape shape-1"></div>
        <div class="floating-shape shape-2"></div>
        <div class="floating-shape shape-3"></div>
    </div>

    <!-- Premium Header -->
    <div class="premium-header">
        <div class="header-glass">
            <div class="header-left">
                <div class="icon-wrapper">
                    <div class="icon-glow"></div>
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <div class="header-info">
                    <h1>إنشاء دورة جديدة</h1>
                    <p>أضف محتوى تعليمي قيّم لطلابك</p>
                </div>
            </div>
            <a href="courses.php" class="back-button">
                <i class="fas fa-arrow-right"></i>
                <span>العودة</span>
            </a>
        </div>
    </div>

    <!-- Progress Steps -->
    <div class="progress-steps">
        <div class="step active" data-step="1">
            <div class="step-icon"><i class="fas fa-info"></i></div>
            <span>المعلومات</span>
        </div>
        <div class="step-line active"></div>
        <div class="step" data-step="2">
            <div class="step-icon"><i class="fas fa-image"></i></div>
            <span>الوسائط</span>
        </div>
        <div class="step-line"></div>
        <div class="step" data-step="3">
            <div class="step-icon"><i class="fas fa-cog"></i></div>
            <span>الإعدادات</span>
        </div>
    </div>

    <!-- Main Form -->
    <form action="../../api/courses.php" method="POST" enctype="multipart/form-data" id="courseForm">
        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
        <input type="hidden" name="action" value="create">

        <!-- Step 1: Basic Info -->
        <div class="form-step active" id="step1">
            <div class="glass-card">
                <div class="card-icon-header">
                    <div class="icon-circle">
                        <i class="fas fa-pen-fancy"></i>
                    </div>
                    <h2>المعلومات الأساسية</h2>
                    <p>أدخل تفاصيل الدورة الرئيسية</p>
                </div>

                <div class="input-group">
                    <label>
                        <span class="label-icon"><i class="fas fa-heading"></i></span>
                        عنوان الدورة
                        <span class="required">*</span>
                    </label>
                    <div class="input-wrapper">
                        <input type="text" name="title" id="title" required
                               placeholder="مثال: أساسيات التجويد للمبتدئين"
                               class="fancy-input">
                        <div class="input-border"></div>
                    </div>
                    <div class="char-counter"><span id="titleCount">0</span>/100</div>
                </div>

                <div class="input-group">
                    <label>
                        <span class="label-icon"><i class="fas fa-align-right"></i></span>
                        وصف الدورة
                    </label>
                    <div class="input-wrapper">
                        <textarea name="description" id="description" rows="6"
                                  placeholder="اكتب وصفاً جذاباً يشرح ما سيتعلمه الطالب..."
                                  class="fancy-input"></textarea>
                        <div class="input-border"></div>
                    </div>
                    <div class="char-counter"><span id="descCount">0</span>/500</div>
                </div>

                <button type="button" class="next-btn" onclick="nextStep(2)">
                    التالي
                    <i class="fas fa-arrow-left"></i>
                </button>
            </div>
        </div>

        <!-- Step 2: Media -->
        <div class="form-step" id="step2">
            <div class="glass-card">
                <div class="card-icon-header">
                    <div class="icon-circle purple">
                        <i class="fas fa-photo-video"></i>
                    </div>
                    <h2>الوسائط</h2>
                    <p>أضف صورة غلاف جذابة للدورة</p>
                </div>

                <div class="upload-zone" id="uploadZone">
                    <input type="file" name="image" id="imageInput" accept="image/*" hidden>
                    <div class="upload-content" id="uploadContent">
                        <div class="upload-animation">
                            <div class="upload-circle">
                                <i class="fas fa-cloud-upload-alt"></i>
                            </div>
                            <div class="upload-ripple"></div>
                        </div>
                        <h3>اسحب الصورة وأفلتها هنا</h3>
                        <p>أو انقر لاختيار ملف</p>
                        <span class="file-types">PNG, JPG, WEBP - حتى 10MB</span>
                    </div>
                    <img id="imagePreview" class="image-preview" src="" alt="">
                    <button type="button" id="removeImage" class="remove-image-btn">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div class="step-nav">
                    <button type="button" class="prev-btn" onclick="prevStep(1)">
                        <i class="fas fa-arrow-right"></i>
                        السابق
                    </button>
                    <button type="button" class="next-btn" onclick="nextStep(3)">
                        التالي
                        <i class="fas fa-arrow-left"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Step 3: Settings -->
        <div class="form-step" id="step3">
            <div class="glass-card">
                <div class="card-icon-header">
                    <div class="icon-circle green">
                        <i class="fas fa-sliders-h"></i>
                    </div>
                    <h2>إعدادات الدورة</h2>
                    <p>حدد التصنيف والسعر وحالة النشر</p>
                </div>

                <div class="settings-grid">
                    <div class="input-group">
                        <label>
                            <span class="label-icon"><i class="fas fa-folder"></i></span>
                            التصنيف
                        </label>
                        <div class="select-wrapper">
                            <select name="category" class="fancy-select">
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= htmlspecialchars($cat['slug']) ?>">
                                        <?= htmlspecialchars($cat['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                    </div>

                    <div class="input-group">
                        <label>
                            <span class="label-icon"><i class="fas fa-tag"></i></span>
                            السعر (ر.س)
                        </label>
                        <div class="price-wrapper">
                            <input type="number" name="price" id="priceInput" 
                                   step="0.01" min="0" value="0"
                                   class="fancy-input price-input">
                            <div class="price-tag" id="priceTag">
                                <i class="fas fa-gift"></i>
                                مجاني
                            </div>
                        </div>
                    </div>

                    <div class="input-group full-width">
                        <label>
                            <span class="label-icon"><i class="fas fa-eye"></i></span>
                            حالة النشر
                        </label>
                        <div class="status-cards">
                            <label class="status-card active">
                                <input type="radio" name="status" value="published" checked>
                                <div class="status-content">
                                    <div class="status-icon published">
                                        <i class="fas fa-rocket"></i>
                                    </div>
                                    <h4>نشر مباشر</h4>
                                    <p>ستظهر الدورة للجميع فوراً</p>
                                </div>
                            </label>
                            <label class="status-card">
                                <input type="radio" name="status" value="draft">
                                <div class="status-content">
                                    <div class="status-icon draft">
                                        <i class="fas fa-pencil-alt"></i>
                                    </div>
                                    <h4>حفظ كمسودة</h4>
                                    <p>احفظها للتعديل لاحقاً</p>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="step-nav">
                    <button type="button" class="prev-btn" onclick="prevStep(2)">
                        <i class="fas fa-arrow-right"></i>
                        السابق
                    </button>
                    <button type="submit" class="submit-btn">
                        <span class="btn-content">
                            <i class="fas fa-check-circle"></i>
                            إنشاء الدورة
                        </span>
                        <span class="btn-loader">
                            <i class="fas fa-spinner fa-spin"></i>
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<style>
/* Base Wrapper */
.add-course-wrapper {
    position: relative;
    max-width: 800px;
    margin: 0 auto;
    padding: 20px 0;
}

/* Animated Background */
.animated-bg {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    pointer-events: none;
    z-index: -1;
    overflow: hidden;
}

.floating-shape {
    position: absolute;
    border-radius: 50%;
    opacity: 0.1;
    animation: float 20s infinite;
}

.shape-1 {
    width: 400px;
    height: 400px;
    background: linear-gradient(135deg, #667eea, #764ba2);
    top: -100px;
    right: -100px;
    animation-delay: 0s;
}

.shape-2 {
    width: 300px;
    height: 300px;
    background: linear-gradient(135deg, #10b981, #059669);
    bottom: 20%;
    left: -50px;
    animation-delay: -7s;
}

.shape-3 {
    width: 200px;
    height: 200px;
    background: linear-gradient(135deg, #f59e0b, #d97706);
    top: 50%;
    right: 10%;
    animation-delay: -14s;
}

@keyframes float {
    0%, 100% { transform: translateY(0) rotate(0deg); }
    33% { transform: translateY(-30px) rotate(5deg); }
    66% { transform: translateY(20px) rotate(-5deg); }
}

/* Premium Header */
.premium-header {
    margin-bottom: 30px;
}

.header-glass {
    background: linear-gradient(135deg, #1e3a5f 0%, #0d253f 100%);
    border-radius: 24px;
    padding: 28px 32px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 20px 50px rgba(0,0,0,0.15);
    position: relative;
    overflow: hidden;
}

.header-glass::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    opacity: 0.5;
}

.header-left {
    display: flex;
    align-items: center;
    gap: 20px;
    position: relative;
    z-index: 1;
}

.icon-wrapper {
    position: relative;
    width: 65px;
    height: 65px;
    background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
    border-radius: 18px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.8rem;
    color: white;
    box-shadow: 0 10px 30px rgba(251, 191, 36, 0.4);
}

.icon-glow {
    position: absolute;
    inset: -3px;
    background: linear-gradient(135deg, #fbbf24, #f59e0b);
    border-radius: 20px;
    opacity: 0.5;
    filter: blur(10px);
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 0.5; transform: scale(1); }
    50% { opacity: 0.8; transform: scale(1.05); }
}

.header-info h1 {
    color: white;
    margin: 0;
    font-size: 1.6rem;
    font-weight: 700;
}

.header-info p {
    color: rgba(255,255,255,0.7);
    margin: 5px 0 0;
    font-size: 0.95rem;
}

.back-button {
    background: rgba(255,255,255,0.1);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255,255,255,0.2);
    color: white;
    padding: 12px 24px;
    border-radius: 12px;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 10px;
    font-weight: 600;
    transition: all 0.3s;
    position: relative;
    z-index: 1;
}

.back-button:hover {
    background: rgba(255,255,255,0.2);
    transform: translateX(5px);
}

/* Progress Steps */
.progress-steps {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0;
    margin-bottom: 35px;
    padding: 0 20px;
}

.step {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
    opacity: 0.4;
    transition: all 0.4s;
}

.step.active {
    opacity: 1;
}

.step-icon {
    width: 50px;
    height: 50px;
    background: #e2e8f0;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
    color: #64748b;
    transition: all 0.4s;
}

.step.active .step-icon {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
}

.step.completed .step-icon {
    background: #10b981;
    color: white;
}

.step span {
    font-size: 0.85rem;
    font-weight: 600;
    color: #64748b;
}

.step.active span {
    color: #667eea;
}

.step-line {
    flex: 1;
    height: 3px;
    background: #e2e8f0;
    max-width: 80px;
    margin: 0 5px;
    margin-bottom: 28px;
    border-radius: 2px;
    transition: all 0.4s;
}

.step-line.active {
    background: linear-gradient(90deg, #667eea, #764ba2);
}

/* Glass Card */
.glass-card {
    background: white;
    border-radius: 24px;
    padding: 35px;
    box-shadow: 0 10px 50px rgba(0,0,0,0.08);
    border: 1px solid rgba(0,0,0,0.05);
}

.card-icon-header {
    text-align: center;
    margin-bottom: 35px;
}

.icon-circle {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
    font-size: 2rem;
    color: white;
    box-shadow: 0 15px 40px rgba(102, 126, 234, 0.35);
}

.icon-circle.purple {
    background: linear-gradient(135deg, #a855f7 0%, #7c3aed 100%);
    box-shadow: 0 15px 40px rgba(168, 85, 247, 0.35);
}

.icon-circle.green {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    box-shadow: 0 15px 40px rgba(16, 185, 129, 0.35);
}

.card-icon-header h2 {
    margin: 0;
    font-size: 1.5rem;
    color: #1e293b;
}

.card-icon-header p {
    margin: 8px 0 0;
    color: #64748b;
}

/* Input Groups */
.input-group {
    margin-bottom: 28px;
}

.input-group label {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 12px;
    font-weight: 600;
    color: #334155;
    font-size: 0.95rem;
}

.label-icon {
    color: #94a3b8;
}

.required {
    color: #ef4444;
}

.input-wrapper {
    position: relative;
}

.fancy-input {
    width: 100%;
    padding: 16px 20px;
    border: 2px solid #e2e8f0;
    border-radius: 14px;
    font-size: 1rem;
    font-family: inherit;
    transition: all 0.3s;
    background: #f8fafc;
    box-sizing: border-box;
}

.fancy-input:focus {
    outline: none;
    border-color: #667eea;
    background: white;
    box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.15);
}

textarea.fancy-input {
    resize: vertical;
    min-height: 140px;
}

.char-counter {
    text-align: left;
    margin-top: 8px;
    font-size: 0.85rem;
    color: #94a3b8;
}

/* Upload Zone */
.upload-zone {
    border: 3px dashed #e2e8f0;
    border-radius: 20px;
    padding: 50px 30px;
    text-align: center;
    cursor: pointer;
    transition: all 0.4s;
    position: relative;
    overflow: hidden;
    background: linear-gradient(135deg, #fafbfc 0%, #f1f5f9 100%);
}

.upload-zone:hover, .upload-zone.dragover {
    border-color: #a855f7;
    background: linear-gradient(135deg, #faf5ff 0%, #f3e8ff 100%);
}

.upload-animation {
    position: relative;
    margin-bottom: 20px;
}

.upload-circle {
    width: 90px;
    height: 90px;
    background: linear-gradient(135deg, #a855f7 0%, #7c3aed 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
    font-size: 2.2rem;
    color: white;
    position: relative;
    z-index: 2;
}

.upload-ripple {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 90px;
    height: 90px;
    border-radius: 50%;
    border: 3px solid #a855f7;
    animation: ripple 2s infinite;
}

@keyframes ripple {
    0% { width: 90px; height: 90px; opacity: 1; }
    100% { width: 150px; height: 150px; opacity: 0; }
}

.upload-zone h3 {
    margin: 0 0 8px;
    color: #334155;
    font-size: 1.2rem;
}

.upload-zone p {
    margin: 0 0 10px;
    color: #64748b;
}

.file-types {
    font-size: 0.85rem;
    color: #94a3b8;
    background: #e2e8f0;
    padding: 6px 16px;
    border-radius: 20px;
    display: inline-block;
}

.image-preview {
    display: none;
    max-width: 100%;
    max-height: 300px;
    border-radius: 16px;
    object-fit: cover;
}

.upload-zone.has-image .upload-content {
    display: none;
}

.upload-zone.has-image .image-preview {
    display: block;
}

.remove-image-btn {
    display: none;
    position: absolute;
    top: 15px;
    right: 15px;
    width: 40px;
    height: 40px;
    background: #ef4444;
    color: white;
    border: none;
    border-radius: 50%;
    cursor: pointer;
    font-size: 1rem;
    box-shadow: 0 4px 15px rgba(239, 68, 68, 0.4);
    transition: all 0.3s;
}

.upload-zone.has-image .remove-image-btn {
    display: flex;
    align-items: center;
    justify-content: center;
}

.remove-image-btn:hover {
    transform: scale(1.1);
}

/* Select Wrapper */
.select-wrapper {
    position: relative;
}

.fancy-select {
    width: 100%;
    padding: 16px 50px 16px 20px;
    border: 2px solid #e2e8f0;
    border-radius: 14px;
    font-size: 1rem;
    font-family: inherit;
    background: #f8fafc;
    cursor: pointer;
    appearance: none;
    transition: all 0.3s;
}

.fancy-select:focus {
    outline: none;
    border-color: #667eea;
    background: white;
}

.select-wrapper i {
    position: absolute;
    left: 20px;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    pointer-events: none;
}

/* Price Wrapper */
.price-wrapper {
    position: relative;
}

.price-input {
    padding-left: 120px !important;
}

.price-tag {
    position: absolute;
    left: 10px;
    top: 50%;
    transform: translateY(-50%);
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
    padding: 8px 16px;
    border-radius: 10px;
    font-size: 0.9rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 6px;
    transition: all 0.3s;
}

.price-tag.paid {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

/* Status Cards */
.settings-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

.settings-grid .full-width {
    grid-column: 1 / -1;
}

.status-cards {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
}

.status-card {
    position: relative;
    cursor: pointer;
}

.status-card input {
    position: absolute;
    opacity: 0;
}

.status-content {
    padding: 25px 20px;
    border: 2px solid #e2e8f0;
    border-radius: 16px;
    text-align: center;
    transition: all 0.3s;
    background: #fafbfc;
}

.status-card.active .status-content,
.status-card input:checked + .status-content {
    border-color: #10b981;
    background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
    box-shadow: 0 8px 25px rgba(16, 185, 129, 0.2);
}

.status-icon {
    width: 55px;
    height: 55px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 12px;
    font-size: 1.3rem;
}

.status-icon.published {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
}

.status-icon.draft {
    background: #e2e8f0;
    color: #64748b;
}

.status-content h4 {
    margin: 0 0 5px;
    font-size: 1rem;
    color: #1e293b;
}

.status-content p {
    margin: 0;
    font-size: 0.85rem;
    color: #64748b;
}

/* Navigation Buttons */
.step-nav {
    display: flex;
    justify-content: space-between;
    margin-top: 35px;
    gap: 15px;
}

.prev-btn, .next-btn {
    padding: 14px 28px;
    border-radius: 12px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 10px;
    transition: all 0.3s;
    font-family: inherit;
    border: none;
}

.prev-btn {
    background: #f1f5f9;
    color: #64748b;
}

.prev-btn:hover {
    background: #e2e8f0;
}

.next-btn {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    margin-right: auto;
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.35);
}

.next-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 35px rgba(102, 126, 234, 0.45);
}

/* Submit Button */
.submit-btn {
    padding: 16px 40px;
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
    border: none;
    border-radius: 14px;
    font-size: 1.1rem;
    font-weight: 700;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 12px;
    transition: all 0.3s;
    font-family: inherit;
    box-shadow: 0 10px 35px rgba(16, 185, 129, 0.4);
    position: relative;
    overflow: hidden;
}

.submit-btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 15px 45px rgba(16, 185, 129, 0.5);
}

.btn-loader {
    display: none;
}

.submit-btn.loading .btn-content {
    display: none;
}

.submit-btn.loading .btn-loader {
    display: block;
}

/* Form Steps */
.form-step {
    display: none;
    animation: fadeIn 0.5s ease;
}

.form-step.active {
    display: block;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Responsive */
@media (max-width: 768px) {
    .header-glass {
        flex-direction: column;
        gap: 20px;
        text-align: center;
    }
    
    .header-left {
        flex-direction: column;
    }
    
    .settings-grid, .status-cards {
        grid-template-columns: 1fr;
    }
    
    .step-nav {
        flex-direction: column;
    }
    
    .next-btn {
        margin-right: 0;
    }
}
</style>

<script>
// Step Navigation
function nextStep(step) {
    document.querySelectorAll('.form-step').forEach(s => s.classList.remove('active'));
    document.getElementById('step' + step).classList.add('active');
    
    document.querySelectorAll('.progress-steps .step').forEach((s, i) => {
        if (i < step) {
            s.classList.add('active', 'completed');
        } else if (i === step - 1) {
            s.classList.add('active');
            s.classList.remove('completed');
        } else {
            s.classList.remove('active', 'completed');
        }
    });
    
    document.querySelectorAll('.step-line').forEach((line, i) => {
        if (i < step - 1) {
            line.classList.add('active');
        } else {
            line.classList.remove('active');
        }
    });
}

function prevStep(step) {
    nextStep(step);
}

// Character Counter
document.getElementById('title').addEventListener('input', function() {
    document.getElementById('titleCount').textContent = this.value.length;
});

document.getElementById('description').addEventListener('input', function() {
    document.getElementById('descCount').textContent = this.value.length;
});

// Image Upload
const uploadZone = document.getElementById('uploadZone');
const imageInput = document.getElementById('imageInput');
const imagePreview = document.getElementById('imagePreview');
const removeBtn = document.getElementById('removeImage');

uploadZone.addEventListener('click', () => imageInput.click());

imageInput.addEventListener('change', function() {
    if (this.files && this.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            imagePreview.src = e.target.result;
            uploadZone.classList.add('has-image');
        };
        reader.readAsDataURL(this.files[0]);
    }
});

removeBtn.addEventListener('click', function(e) {
    e.stopPropagation();
    imageInput.value = '';
    imagePreview.src = '';
    uploadZone.classList.remove('has-image');
});

// Drag & Drop
['dragenter', 'dragover', 'dragleave', 'drop'].forEach(event => {
    uploadZone.addEventListener(event, e => {
        e.preventDefault();
        e.stopPropagation();
    });
});

['dragenter', 'dragover'].forEach(event => {
    uploadZone.addEventListener(event, () => uploadZone.classList.add('dragover'));
});

['dragleave', 'drop'].forEach(event => {
    uploadZone.addEventListener(event, () => uploadZone.classList.remove('dragover'));
});

uploadZone.addEventListener('drop', function(e) {
    const files = e.dataTransfer.files;
    if (files.length) {
        imageInput.files = files;
        imageInput.dispatchEvent(new Event('change'));
    }
});

// Price Tag
document.getElementById('priceInput').addEventListener('input', function() {
    const tag = document.getElementById('priceTag');
    if (this.value && parseFloat(this.value) > 0) {
        tag.innerHTML = '<i class="fas fa-coins"></i>' + this.value + ' ر.س';
        tag.classList.add('paid');
    } else {
        tag.innerHTML = '<i class="fas fa-gift"></i>مجاني';
        tag.classList.remove('paid');
    }
});

// Status Cards
document.querySelectorAll('.status-card input').forEach(input => {
    input.addEventListener('change', function() {
        document.querySelectorAll('.status-card').forEach(card => card.classList.remove('active'));
        this.closest('.status-card').classList.add('active');
    });
});

// Form Submit Animation
document.getElementById('courseForm').addEventListener('submit', function() {
    document.querySelector('.submit-btn').classList.add('loading');
});
</script>

<?php
});
?>
