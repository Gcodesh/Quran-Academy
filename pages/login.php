<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: home.php");
    exit();
}
include __DIR__ . '/../includes/components/header.php'; ?>

<main class="auth-page">
    <section class="login-section">
        <div class="container">
            <div class="auth-back-link">
                <a href="home.php"><i class="fas fa-arrow-right"></i> العودة للرئيسية</a>
            </div>
            <h2>تسجيل الدخول</h2>
            <form id="login-form" method="POST" action="../api/auth.php">
                <div class="form-group">
                    <label for="email">البريد الإلكتروني</label>
                    <div class="input-with-icon">
                        <input type="email" id="email" name="email" required placeholder="أدخل بريدك الإلكتروني">
                        <i class="fas fa-envelope"></i>
                    </div>
                </div>
                <div class="form-group">
                    <label for="password">كلمة المرور</label>
                    <div class="input-with-icon">
                        <input type="password" id="password" name="password" required placeholder="أدخل كلمة المرور">
                        <i class="fas fa-lock"></i>
                    </div>
                </div>
                <button type="submit" class="btn-primary">تسجيل الدخول</button>
                <p class="auth-link">ليس لديك حساب؟ <a href="register.php">إنشاء حساب جديد</a></p>
            </form>
        </div>
    </section>
</main>

<script>
document.getElementById('login-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    
    // Create status message element
    let statusMsg = document.querySelector('.auth-status');
    if (!statusMsg) {
        statusMsg = document.createElement('div');
        statusMsg.className = 'auth-status';
        this.insertBefore(statusMsg, this.firstChild);
    }
    statusMsg.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري التحقق...';
    statusMsg.className = 'auth-status pending';
    
    try {
        const response = await fetch('../api/auth.php', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();
        
        if (data.success) {
            statusMsg.innerHTML = '<i class="fas fa-check-circle"></i> تم تسجيل الدخول بنجاح!';
            statusMsg.className = 'auth-status success';
            
            setTimeout(() => {
                // Redirect based on role
                if (data.user && data.user.role === 'student') {
                    window.location.href = 'home.php';
                } else {
                    window.location.href = 'dashboard/index.php';
                }
            }, 1000);
        } else {
            // Handle specific errors
            if (data.message && (data.message.includes('Invalid credentials') || data.message.includes('not found'))) {
                statusMsg.innerHTML = '<i class="fas fa-exclamation-circle"></i> الحساب غير موجود. جاري توجيهك لإنشاء حساب...';
                statusMsg.className = 'auth-status error';
                
                setTimeout(() => {
                    window.location.href = 'register.php';
                }, 2000);
            } else {
                statusMsg.innerHTML = `<i class="fas fa-times-circle"></i> ${data.message || 'فشل تسجيل الدخول'}`;
                statusMsg.className = 'auth-status error';
            }
        }
    } catch (error) {
        console.error('Error:', error);
        statusMsg.innerHTML = '<i class="fas fa-wifi"></i> خطأ في الاتصال، يرجى المحاولة لاحقاً';
        statusMsg.className = 'auth-status error';
    }
});
</script>

<style>
.auth-status {
    padding: 12px;
    border-radius: 8px;
    margin-bottom: 20px;
    font-size: 0.95rem;
    display: flex;
    align-items: center;
    gap: 10px;
}
.auth-status.pending { background: #e0f2fe; color: #0284c7; }
.auth-status.success { background: #dcfce7; color: #16a34a; }
.auth-status.error { background: #fee2e2; color: #dc2626; }
</style>

<?php include __DIR__ . '/../includes/components/footer.php'; ?>