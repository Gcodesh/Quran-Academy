<?php
require_once '../includes/auth_middleware.php';
require_once '../includes/config/database.php';
require_once '../includes/classes/Database.php';

$invoice = $_GET['invoice'] ?? '';

if (!$invoice) {
    header('Location: home.php');
    exit;
}

$db = (new Database())->getConnection();

// Fetch payment & course details
$stmt = $db->prepare("SELECT p.*, c.title, c.thumbnail 
                      FROM payments p 
                      JOIN courses c ON p.course_id = c.id 
                      WHERE p.invoice_number = ? AND p.user_id = ?");
$stmt->execute([$invoice, $_SESSION['user_id']]);
$payment = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$payment) {
    die("الفاتورة غير موجودة أو لا تملك صلاحية الوصول إليها.");
}

if ($payment['status'] == 'completed') {
    header("Location: course-lessons.php?id=" . $payment['course_id']);
    exit;
}

include '../includes/components/header.php';
?>

<div class="checkout-v2">
    <div class="container">
        <div class="row g-5">
            <!-- Order Summary -->
            <div class="col-lg-5 order-lg-2">
                <div class="summary-card sticky-top" style="top: 20px;">
                    <h4 class="mb-4">ملخص الطلب</h4>
                    <div class="product-item">
                        <img src="<?= htmlspecialchars($payment['thumbnail']) ?>" alt="Course">
                        <div>
                            <h6><?= htmlspecialchars($payment['title']) ?></h6>
                            <span class="text-muted">دورة تدريبية</span>
                        </div>
                        <div class="price"><?= $payment['amount'] ?> ر.س</div>
                    </div>
                    
                    <div class="checkout-details">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">السعر الأصلي</span>
                            <span><?= $payment['amount'] ?> ر.س</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-success">الرسوم الإدارية</span>
                            <span class="text-success">مجاناً</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between total-row">
                            <span>الإجمالي</span>
                            <span><?= $payment['amount'] ?> ر.س</span>
                        </div>
                    </div>

                    <div class="secure-badge">
                        <i class="fas fa-lock"></i>
                        <span>مدفوعات آمنة ومشفرة 100%</span>
                    </div>
                </div>
            </div>

            <!-- Payment Form -->
            <div class="col-lg-7 order-lg-1">
                <div class="payment-section">
                    <h3 class="mb-4">اختر وسيلة الدفع</h3>
                    
                    <form id="payment-form">
                        <input type="hidden" name="invoice" value="<?= $invoice ?>">

                        <!-- Payment Methods -->
                        <div class="methods-grid">
                            <label class="method-card selected">
                                <input type="radio" name="method" value="card" checked>
                                <div class="card-content">
                                    <div class="logos">
                                        <img src="https://upload.wikimedia.org/wikipedia/commons/5/5e/Visa_Inc._logo.svg" alt="Visa">
                                        <img src="https://upload.wikimedia.org/wikipedia/commons/2/2a/Mastercard-logo.svg" alt="Mastercard">
                                    </div>
                                    <span class="method-name">بطاقة ائتمان</span>
                                </div>
                                <div class="check-icon"><i class="fas fa-check-circle"></i></div>
                            </label>

                            <label class="method-card">
                                <input type="radio" name="method" value="fawry">
                                <div class="card-content">
                                    <div class="logos">
                                        <img src="https://raw.githubusercontent.com/FawryPay/FawryPay-Assets/master/fawry-pay-logo.png" alt="Fawry">
                                    </div>
                                    <span class="method-name">فوري (Fawry)</span>
                                </div>
                                <div class="check-icon"><i class="fas fa-check-circle"></i></div>
                            </label>

                            <label class="method-card">
                                <input type="radio" name="method" value="paypal">
                                <div class="card-content">
                                    <div class="logos">
                                        <img src="https://upload.wikimedia.org/wikipedia/commons/b/b5/PayPal.svg" alt="PayPal">
                                    </div>
                                    <span class="method-name">باي بال</span>
                                </div>
                                <div class="check-icon"><i class="fas fa-check-circle"></i></div>
                            </label>
                        </div>

                        <!-- Card Inputs -->
                        <div id="card-details-section" class="details-section active">
                            <h5 class="mb-3">بيانات البطاقة</h5>
                            <div class="form-grid">
                                <!-- Name -->
                                <div class="form-group full-width">
                                    <label>الاسم على البطاقة</label>
                                    <div class="input-wrapper">
                                        <i class="fas fa-user"></i>
                                        <input type="text" placeholder="الاسم كما يظهر على البطاقة" required>
                                    </div>
                                </div>

                                <!-- Card Number -->
                                <div class="form-group full-width">
                                    <label>رقم البطاقة</label>
                                    <div class="input-wrapper">
                                        <i class="far fa-credit-card"></i>
                                        <input type="text" placeholder="0000 0000 0000 0000" id="card-num" required>
                                    </div>
                                </div>

                                <!-- Expiry & CVC Row -->
                                <div class="form-group">
                                    <label>تاريخ الانتهاء</label>
                                    <input type="text" placeholder="MM/YY" required>
                                </div>
                                <div class="form-group">
                                    <label>رمز الأمان (CVC)</label>
                                    <input type="text" placeholder="123" required>
                                </div>
                            </div>
                        </div>

                        <!-- Fawry Details -->
                        <div id="fawry-details-section" class="details-section">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                <span>سيتم إصدار كود مرجعي صالح لمدة 24 ساعة.</span>
                            </div>
                            <div class="form-group full-width">
                                <label>رقم الموبايل</label>
                                <input type="text" placeholder="01xxxxxxxxx">
                            </div>
                        </div>

                        <button type="submit" class="btn-pay" id="submit-btn">
                            <span id="btn-text">تأكيد الدفع (<?= $payment['amount'] ?> ر.س)</span>
                            <span id="btn-spinner" style="display: none;"><i class="fas fa-circle-notch fa-spin"></i></span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.checkout-v2 {
    padding: 50px 0;
    background-color: #f3f4f6;
    min-height: 100vh;
    font-family: 'Cairo', sans-serif;
}

/* Cards & Layout */
.summary-card, .payment-section {
    background: #fff;
    padding: 35px;
    border-radius: 20px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.06);
    border: 1px solid rgba(0,0,0,0.03);
}

.header-title {
    font-weight: 800;
    color: #1f2937;
    font-size: 1.4rem;
}

/* Course Summary Item */
.course-summary-item {
    display: flex;
    align-items: center;
    gap: 15px;
    background: #f9fafb;
    padding: 15px;
    border-radius: 16px;
    border: 1px solid #f3f4f6;
    margin-bottom: 25px;
}

.course-img-wrapper img {
    width: 70px;
    height: 70px;
    border-radius: 12px;
    object-fit: cover;
    box-shadow: 0 4px 10px rgba(0,0,0,0.05);
}

.course-info {
    flex-grow: 1;
}

.badge.bg-primary-soft {
    background-color: rgba(37, 99, 235, 0.1);
    color: var(--primary-color);
    font-size: 0.75rem;
    padding: 5px 10px;
    border-radius: 20px;
    font-weight: 700;
}

.course-title {
    margin: 5px 0 0;
    font-weight: 700;
    font-size: 1rem;
    color: #374151;
    line-height: 1.4;
}

.course-price {
    font-weight: 800;
    color: var(--primary-color);
    font-size: 1.1rem;
    white-space: nowrap;
}

/* Details List */
.detail-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 12px;
    font-size: 0.95rem;
}

.detail-row .label {
    color: #6b7280;
    font-weight: 500;
}

.detail-row .value {
    color: #111827;
    font-weight: 600;
    font-family: 'IBM Plex Sans Arabic', sans-serif;
}

.divider {
    height: 1px;
    background: #e5e7eb;
    margin: 20px 0;
}

.total-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.total-row span:first-child {
    font-size: 1.2rem;
    font-weight: 700;
    color: #111827;
}

.total-amount {
    font-size: 1.6rem;
    font-weight: 800;
    color: var(--primary-color);
    font-family: 'IBM Plex Sans Arabic', sans-serif;
}

.secure-badge {
    background: #f0fdf4;
    color: #15803d;
    padding: 15px;
    border-radius: 12px;
    text-align: center;
    margin-top: 30px;
    border: 1px dashed #86efac;
    font-weight: 600;
    font-size: 0.9rem;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
}

/* Methods Grid */
.methods-grid {
    display: flex; flex-direction: column; gap: 12px;
    margin-bottom: 30px;
}
.method-card {
    display: flex; align-items: center; justify-content: space-between;
    padding: 15px 20px;
    border: 2px solid #e5e7eb; border-radius: 12px;
    cursor: pointer; transition: all 0.2s;
}
.method-card:hover { border-color: #d1d5db; background: #f9fafb; }
.method-card.selected { border-color: var(--primary-color); background: #eff6ff; }
.method-card input { display: none; }
.card-content { display: flex; align-items: center; gap: 15px; }
.logos img { height: 24px; filter: grayscale(0); }
.check-icon { 
    width: 24px; height: 24px; border-radius: 50%; 
    border: 2px solid #d1d5db; color: #fff; 
    display: flex; align-items: center; justify-content: center;
}
.method-card.selected .check-icon { 
    background: var(--primary-color); border-color: var(--primary-color); 
}

/* Modern Form Grid */
.form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr; /* Two equal columns */
    gap: 20px; /* Space between items */
}
.full-width {
    grid-column: span 2; /* Take up both columns */
}

.form-group label {
    display: block; margin-bottom: 8px;
    font-weight: 600; color: #374151;
}
.input-wrapper {
    position: relative;
}
.input-wrapper i {
    position: absolute; right: 12px; top: 50%; transform: translateY(-50%);
    color: #9ca3af;
}
.form-group input {
    width: 100%;
    padding: 12px 15px;
    padding-right: 40px; /* Space for icon */
    border: 1px solid #d1d5db;
    border-radius: 10px;
    outline: none; transition: border 0.2s;
}
/* Ensure icon-less inputs have padding */
.form-group input:not([id="card-num"]):not([type="text"]) {
    padding-right: 15px;
}
/* Specific fix for date/cvc if no icon */
.form-group input { padding-left: 15px; } 

.form-group input:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

.details-section { display: none; }
.details-section.active { display: block; animation: fadeIn 0.3s ease; }

.btn-pay {
    width: 100%; background: var(--primary-color); color: white;
    padding: 16px; border-radius: 12px; border: none;
    font-weight: 700; font-size: 1.1rem; margin-top: 30px;
    cursor: pointer;
}
.btn-pay:hover { opacity: 0.9; }

@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }

/* RTL Tweaks */
html[dir="rtl"] .input-wrapper i { right: 12px; left: auto; }
html[dir="rtl"] .form-group input { padding-right: 40px; padding-left: 15px; }

</style>

<script>
document.querySelectorAll('input[name="method"]').forEach(input => {
    input.addEventListener('change', function() {
        document.querySelectorAll('.method-card').forEach(c => c.classList.remove('selected'));
        this.closest('.method-card').classList.add('selected');
        document.querySelectorAll('.details-section').forEach(s => s.classList.remove('active'));
        if(this.value === 'card') document.getElementById('card-details-section').classList.add('active');
        else if(this.value === 'fawry') document.getElementById('fawry-details-section').classList.add('active');
    });
});

document.getElementById('payment-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const btn = document.getElementById('submit-btn');
    const txt = document.getElementById('btn-text');
    const spn = document.getElementById('btn-spinner');
    
    btn.disabled = true; btn.style.opacity = '0.7';
    txt.style.display = 'none'; spn.style.display = 'inline-block';
    
    setTimeout(async () => {
        const formData = new FormData(this);
        const response = await fetch('../api/process_payment.php', { method: 'POST', body: formData });
        const result = await response.json();
        if(result.success) window.location.href = result.redirect_url;
        else { alert('Error: ' + result.message); btn.disabled = false; txt.style.display = 'block'; spn.style.display = 'none'; }
    }, 2000);
});
</script>

<?php include '../includes/components/footer.php'; ?>
