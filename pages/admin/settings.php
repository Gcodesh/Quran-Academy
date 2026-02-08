<?php
require_once __DIR__ . '/admin_middleware.php';
require_once '../../pages/dashboard/layout.php';
require_once '../../includes/config/database.php';
require_once '../../includes/classes/Database.php';

$page_title = 'إعدادات المنصة الملكية';

// Simple JSON info storage
$settings_file = __DIR__ . '/../../includes/config/site_settings.json';
$settings = [];

if (file_exists($settings_file)) {
    $settings = json_decode(file_get_contents($settings_file), true);
}

// Defaults
$defaults = [
    'site_name' => 'منصة التميز',
    'site_description' => 'منصة التعليم الإسلامي الرائدة',
    'contact_email' => 'admin@islamic-edu.com',
    'maintenance_mode' => false,
    'allow_registration' => true,
    'currency' => 'SAR',
    'payment_gateway' => 'stripe_mock',
    'primary_color' => '#10b981',
    'logo_path' => 'assets/images/logo of academy .png',
    'favicon_path' => 'assets/images/fiv icon .PNG'
];

$settings = array_merge($defaults, $settings);
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $settings['site_name'] = $_POST['site_name'] ?? $settings['site_name'];
    $settings['site_description'] = $_POST['site_description'] ?? $settings['site_description'];
    $settings['contact_email'] = $_POST['contact_email'] ?? $settings['contact_email'];
    $settings['maintenance_mode'] = isset($_POST['maintenance_mode']);
    $settings['allow_registration'] = isset($_POST['allow_registration']);
    $settings['currency'] = $_POST['currency'] ?? $settings['currency'];
    $settings['logo_path'] = $_POST['logo_path'] ?? $settings['logo_path'];
    $settings['favicon_path'] = $_POST['favicon_path'] ?? $settings['favicon_path'];
    
    file_put_contents($settings_file, json_encode($settings, JSON_PRETTY_PRINT));
    $message = 'تم حفظ الإعدادات الملكية بنجاح ✨';
}

render_dashboard_layout(function() use ($settings, $message) {
?>

<div class="p-luxury-center-wrapper">
    <!-- Background Decor (Artistic Blurs) -->
    <div class="p-decor-blob blob-1"></div>
    <div class="p-decor-blob blob-2"></div>

    <div class="p-master-centered-hub">
        <!-- Floating Save Button (Always Ready) -->
        <div class="p-hub-header">
            <div class="hub-title">
                <h1>إعدادات المنصة 🏛️</h1>
                <p>إدارة الهوية، النظام، والعمليات المالية من مكان واحد</p>
            </div>
            <button type="submit" form="masterSettingsForm" class="p-btn-glow">
                <span>حفظ التغييرات</span>
                <i class="fas fa-magic"></i>
            </button>
        </div>

        <?php if($message): ?>
            <div class="p-floating-alert" id="pAlert">
                <i class="fas fa-check-circle"></i> <?= $message ?>
            </div>
            <script>setTimeout(() => document.getElementById('pAlert').style.opacity = '0', 3000);</script>
        <?php endif; ?>

        <!-- The Central Luxury Card -->
        <div class="p-luxury-card glass-card">
            <!-- Sidebar Nav (Inside the card for a unified look) -->
            <div class="p-hub-nav">
                <div class="hub-nav-item active" data-target="general">
                    <i class="fas fa-fingerprint"></i>
                    <span>الهوية</span>
                </div>
                <div class="hub-nav-item" data-target="payment">
                    <i class="fas fa-wallet"></i>
                    <span>المالية</span>
                </div>
                <div class="hub-nav-item" data-target="system">
                    <i class="fas fa-shield-halved"></i>
                    <span>النظام</span>
                </div>
            </div>

            <!-- Content Area -->
            <div class="p-hub-content">
                <form method="POST" id="masterSettingsForm">
                    
                    <!-- Section: General -->
                    <div class="hub-pane active" id="pane-general">
                        <h2 class="pane-title">هوية المنصة والظهور</h2>
                        <div class="p-input-group">
                            <label>اسم المنصة الرئيسي</label>
                            <input type="text" name="site_name" value="<?= htmlspecialchars($settings['site_name']) ?>" placeholder="ادخل اسم المنصة هنا...">
                        </div>
                        <div class="p-input-group">
                            <label>وصف المنصة (SEO)</label>
                            <textarea name="site_description" rows="4" placeholder="وصف المختصر للمنصة..."><?= htmlspecialchars($settings['site_description']) ?></textarea>
                        </div>
                        <div class="p-grid-row">
                            <div class="p-input-group">
                                <label>بريد المراسلات</label>
                                <input type="email" name="contact_email" value="<?= htmlspecialchars($settings['contact_email']) ?>" placeholder="admin@domain.com">
                            </div>
                            <div class="p-input-group">
                                <label>اللون الأساسي</label>
                                <div class="p-color-field">
                                    <input type="color" name="primary_color" value="<?= $settings['primary_color'] ?>">
                                    <span><?= $settings['primary_color'] ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="p-grid-row">
                            <div class="p-input-group">
                                <label>رابط اللوجو (الرئيسي)</label>
                                <input type="text" name="logo_path" value="<?= htmlspecialchars($settings['logo_path']) ?>" placeholder="assets/images/logo.png">
                            </div>
                            <div class="p-input-group">
                                <label>رابط الفيف أيقونة (Favicon)</label>
                                <input type="text" name="favicon_path" value="<?= htmlspecialchars($settings['favicon_path']) ?>" placeholder="assets/images/favicon.ico">
                            </div>
                        </div>
                    </div>

                    <!-- Section: Payment -->
                    <div class="hub-pane" id="pane-payment">
                        <h2 class="pane-title">المعاملات المالية</h2>
                        <div class="p-input-group">
                            <label>عملة المنصة</label>
                            <select name="currency">
                                <option value="SAR" <?= $settings['currency'] == 'SAR' ? 'selected' : '' ?>>ريال سعودي (SAR)</option>
                                <option value="USD" <?= $settings['currency'] == 'USD' ? 'selected' : '' ?>>دولار أمريكي (USD)</option>
                                <option value="EGP" <?= $settings['currency'] == 'EGP' ? 'selected' : '' ?>>جنيه مصري (EGP)</option>
                            </select>
                        </div>
                        <div class="p-gateway-stack">
                            <div class="p-gt-card active">
                                <i class="fab fa-stripe"></i>
                                <div class="gt-info">
                                    <strong>Stripe Gateway</strong>
                                    <span>الوضع: تجريبي (متصل)</span>
                                </div>
                                <div class="gt-badge">فعال</div>
                            </div>
                            <div class="p-gt-card disabled">
                                <i class="fab fa-paypal"></i>
                                <div class="gt-info">
                                    <strong>PayPal Service</strong>
                                    <span>الوضع: غير مفعل</span>
                                </div>
                                <div class="gt-link">تفعيل</div>
                            </div>
                        </div>
                    </div>

                    <!-- Section: System -->
                    <div class="hub-pane" id="pane-system">
                        <h2 class="pane-title">إعدادات النظام والأمان</h2>
                        <div class="p-toggle-stack">
                            <div class="p-toggle-row">
                                <div class="tr-text">
                                    <strong>وضع الصيانة الشامل</strong>
                                    <p>إغلاق المنصة للصيانة ومنع الدخول العام</p>
                                </div>
                                <label class="p-fancy-switch">
                                    <input type="checkbox" name="maintenance_mode" <?= $settings['maintenance_mode'] ? 'checked' : '' ?>>
                                    <span></span>
                                </label>
                            </div>
                            <div class="p-toggle-row">
                                <div class="tr-text">
                                    <strong>تفعيل التسجيل الجديد</strong>
                                    <p>السماح للطلاب الجدد بإنشاء حسابات</p>
                                </div>
                                <label class="p-fancy-switch">
                                    <input type="checkbox" name="allow_registration" <?= $settings['allow_registration'] ? 'checked' : '' ?>>
                                    <span></span>
                                </label>
                            </div>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

<style>
/* Luxury Centralized CSS */
.p-luxury-center-wrapper {
    position: relative;
    width: 100%;
    min-height: 80vh;
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 20px 0;
}

/* Artistic Blobs for Depth */
.p-decor-blob {
    position: absolute;
    width: 400px;
    height: 400px;
    border-radius: 50%;
    filter: blur(80px);
    z-index: 0;
    opacity: 0.1;
}
.blob-1 { top: -100px; right: -100px; background: var(--p-emerald-500); }
.blob-2 { bottom: -100px; left: -100px; background: var(--p-slate-500); }

.p-master-centered-hub {
    width: 100%;
    max-width: 900px;
    position: relative;
    z-index: 10;
    animation: pFadeUp 0.8s cubic-bezier(0.16, 1, 0.3, 1);
}

/* Header */
.p-hub-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    margin-bottom: 30px;
    padding: 0 10px;
}
.hub-title h1 { font-size: 2.2rem; font-weight: 950; color: #1e293b; margin: 0; }
.hub-title p { color: #64748b; margin: 10px 0 0; font-size: 1.1rem; }

/* The Luxury Card Layout */
.p-luxury-card {
    display: grid;
    grid-template-columns: 100px 1fr;
    background: white;
    border-radius: 35px;
    box-shadow: 0 50px 100px rgba(0,0,0,0.06);
    overflow: hidden;
    min-height: 600px;
}

/* Inner Nav (Iconic) */
.p-hub-nav {
    background: #f8fafc;
    border-left: 1px solid #f1f5f9;
    display: flex;
    flex-direction: column;
    padding: 40px 0;
    gap: 10px;
}
.hub-nav-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 20px 0;
    cursor: pointer;
    color: #94a3b8;
    transition: 0.3s;
    gap: 8px;
    position: relative;
}
.hub-nav-item i { font-size: 1.4rem; }
.hub-nav-item span { font-size: 0.8rem; font-weight: 800; }

.hub-nav-item::after {
    content: '';
    position: absolute;
    right: 0;
    top: 50%;
    height: 0;
    width: 4px;
    background: var(--p-emerald-500);
    transform: translateY(-50%);
    transition: 0.3s;
    border-radius: 4px 0 0 4px;
}

.hub-nav-item.active { color: var(--p-emerald-600); background: white; }
.hub-nav-item.active::after { height: 40px; }

/* Content Area */
.p-hub-content {
    padding: 60px;
}
.hub-pane { display: none; animation: pFadeInDown 0.6s cubic-bezier(0.16, 1, 0.3, 1); }
.hub-pane.active { display: block; }
.pane-title { font-size: 1.6rem; font-weight: 900; color: #1e293b; margin-bottom: 40px; padding-bottom: 20px; border-bottom: 2px solid #f8fafc; }

/* Form Styles */
.p-input-group { margin-bottom: 25px; }
.p-input-group label { display: block; font-weight: 800; color: #334155; margin-bottom: 12px; font-size: 1rem; }
.p-input-group input, .p-input-group select, .p-input-group textarea {
    width: 100%; padding: 16px 20px; background: #f8fafc; border: 2px solid transparent; border-radius: 18px; font-size: 1rem; transition: 0.3s;
}
.p-input-group input:focus { background: white; border-color: var(--p-emerald-500); box-shadow: 0 10px 25px rgba(16, 185, 129, 0.1); outline: none; }

.p-grid-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }

.p-color-field { display: flex; align-items: center; gap: 15px; background: #f8fafc; padding: 10px 20px; border-radius: 18px; width: fit-content; }
.p-color-field input { width: 40px; height: 40px; border: none; border-radius: 50%; cursor: pointer; background: none; }
.p-color-field span { font-weight: 700; color: #64748b; font-family: monospace; }

/* Gateway Cards */
.p-gateway-stack { display: flex; flex-direction: column; gap: 15px; }
.p-gt-card { display: flex; align-items: center; gap: 20px; padding: 25px; border-radius: 25px; border: 2px solid #f1f5f9; background: white; transition: 0.3s; }
.p-gt-card.active { border-color: var(--p-emerald-500); background: #f0fdf4; }
.p-gt-card i { font-size: 2.2rem; color: #94a3b8; }
.p-gt-card.active i { color: #635bff; }
.gt-info { flex: 1; text-align: right; }
.gt-info strong { display: block; color: #1e293b; }
.gt-info span { font-size: 0.9rem; color: #64748b; }
.gt-badge { padding: 6px 15px; background: var(--p-emerald-500); color: white; border-radius: 12px; font-size: 0.8rem; font-weight: 800; }
.gt-link { color: var(--p-emerald-600); font-weight: 800; cursor: pointer; }

/* Fancy Switches */
.p-toggle-row { display: flex; justify-content: space-between; align-items: center; padding: 25px; background: #f8fafc; border-radius: 25px; margin-bottom: 15px; }
.tr-text { text-align: right; }
.tr-text strong { display: block; font-size: 1.1rem; color: #1e293b; }
.tr-text p { margin: 5px 0 0; font-size: 0.9rem; color: #64748b; }

.p-fancy-switch { width: 64px; height: 34px; position: relative; }
.p-fancy-switch input { opacity: 0; width: 0; height: 0; }
.p-fancy-switch span { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background: #cbd5e1; border-radius: 34px; transition: 0.4s; }
.p-fancy-switch span:before { position: absolute; content: ""; height: 26px; width: 26px; left: 4px; bottom: 4px; background: white; border-radius: 50%; transition: 0.4s; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
input:checked + span { background: var(--p-emerald-500); }
input:checked + span:before { transform: translateX(30px); }

/* Glowing Button */
.p-btn-glow {
    padding: 18px 35px;
    background: linear-gradient(135deg, #1e293b, #0f172a);
    color: white; border: none; border-radius: 22px; font-weight: 800; cursor: pointer;
    display: flex; align-items: center; gap: 15px; box-shadow: 0 20px 40px rgba(0,0,0,0.15); transition: 0.4s;
}
.p-btn-glow:hover { transform: translateY(-5px); box-shadow: 0 25px 50px rgba(0,0,0,0.25); }

/* Floating Alert */
.p-floating-alert {
    position: fixed; top: 40px; left: 50%; transform: translateX(-50%);
    background: #1e293b; color: white; padding: 18px 40px; border-radius: 20px;
    z-index: 10000; display: flex; align-items: center; gap: 15px; font-weight: 800;
    box-shadow: 0 30px 60px rgba(0,0,0,0.3); border-bottom: 3px solid var(--p-emerald-500);
    transition: 0.6s;
}

@media (max-width: 992px) {
    .p-luxury-card { grid-template-columns: 1fr; }
    .p-hub-nav { flex-direction: row; justify-content: center; padding: 20px; }
    .hub-nav-item::after { display: none; }
    .p-hub-content { padding: 40px 30px; }
    .p-grid-row { grid-template-columns: 1fr; }
}

@keyframes pFadeUp { from { opacity: 0; transform: translateY(40px); } to { opacity: 1; transform: translateY(0); } }
</style>

<script>
document.querySelectorAll('.hub-nav-item').forEach(item => {
    item.addEventListener('click', function() {
        document.querySelectorAll('.hub-nav-item').forEach(i => i.classList.remove('active'));
        this.classList.add('active');
        
        const target = this.dataset.target;
        document.querySelectorAll('.hub-pane').forEach(p => p.classList.remove('active'));
        document.getElementById('pane-' + target).classList.add('active');
    });
});
</script>

<?php
});
?>
