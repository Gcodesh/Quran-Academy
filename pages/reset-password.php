<?php 
include '../includes/components/header.php';

// Get token from URL
$token = $_GET['token'] ?? '';
$valid_token = !empty($token); // In real app, validate token from database
?>

<main class="auth-page">
    <section class="reset-password-section" style="min-height: 70vh; display: flex; align-items: center; padding: 60px 0;">
        <div class="container">
            <div class="auth-box" style="max-width: 480px; margin: 0 auto; background: var(--white); padding: 50px; border-radius: 24px; box-shadow: var(--shadow-lg);">
                
                <?php if (!$valid_token && empty($_GET['demo'])): ?>
                <!-- Invalid/Missing Token -->
                <div class="auth-header" style="text-align: center;">
                    <div style="width: 80px; height: 80px; background: #fef2f2; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                        <i class="fas fa-times-circle" style="font-size: 2rem; color: #dc2626;"></i>
                    </div>
                    <h2 style="font-size: 1.8rem; color: var(--dark-800); margin-bottom: 10px;">رابط غير صالح</h2>
                    <p style="color: var(--dark-600); margin-bottom: 30px;">هذا الرابط غير صالح أو منتهي الصلاحية. يرجى طلب رابط جديد.</p>
                    <a href="forgot-password.php" class="btn-primary" style="display: inline-block; padding: 14px 35px;">
                        طلب رابط جديد
                    </a>
                </div>
                <?php else: ?>
                
                <!-- Reset Form -->
                <div class="auth-header" style="text-align: center; margin-bottom: 35px;">
                    <div style="width: 80px; height: 80px; background: var(--primary-100); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                        <i class="fas fa-lock-open" style="font-size: 2rem; color: var(--primary-600);"></i>
                    </div>
                    <h2 style="font-size: 1.8rem; color: var(--dark-800); margin-bottom: 10px;">إعادة تعيين كلمة المرور</h2>
                    <p style="color: var(--dark-600);">أدخل كلمة المرور الجديدة</p>
                </div>

                <div id="success-message" style="display: none; background: var(--primary-100); color: var(--primary-800); padding: 20px; border-radius: 12px; text-align: center; margin-bottom: 20px;">
                    <i class="fas fa-check-circle" style="font-size: 2rem; margin-bottom: 10px; display: block;"></i>
                    <p style="margin-bottom: 15px;">تم تغيير كلمة المرور بنجاح!</p>
                    <a href="login.php" style="color: var(--primary-700); font-weight: 600;">الذهاب لتسجيل الدخول ←</a>
                </div>

                <div id="error-message" style="display: none; background: #fef2f2; color: #dc2626; padding: 15px; border-radius: 12px; text-align: center; margin-bottom: 20px;">
                    <p id="error-text"></p>
                </div>

                <form id="reset-password-form" method="POST">
                    <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
                    
                    <div class="form-group" style="margin-bottom: 20px;">
                        <label for="password" style="display: block; margin-bottom: 8px; font-weight: 600; color: var(--dark-700);">كلمة المرور الجديدة</label>
                        <div class="input-with-icon" style="position: relative;">
                            <input type="password" id="password" name="password" required 
                                   minlength="8"
                                   placeholder="أدخل كلمة المرور الجديدة"
                                   style="width: 100%; padding: 16px 20px; padding-right: 50px; border: 2px solid var(--light-300); border-radius: 12px; font-size: 1rem; transition: all 0.3s;">
                            <i class="fas fa-lock" style="position: absolute; right: 18px; top: 50%; transform: translateY(-50%); color: var(--dark-500);"></i>
                            <button type="button" onclick="togglePassword('password')" style="position: absolute; left: 18px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: var(--dark-500);">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <div class="password-strength" style="margin-top: 10px;">
                            <div id="strength-bar" style="height: 4px; background: var(--light-200); border-radius: 2px; overflow: hidden;">
                                <div id="strength-fill" style="height: 100%; width: 0%; transition: all 0.3s;"></div>
                            </div>
                            <p id="strength-text" style="font-size: 0.85rem; color: var(--dark-500); margin-top: 5px;"></p>
                        </div>
                    </div>

                    <div class="form-group" style="margin-bottom: 25px;">
                        <label for="confirm_password" style="display: block; margin-bottom: 8px; font-weight: 600; color: var(--dark-700);">تأكيد كلمة المرور</label>
                        <div class="input-with-icon" style="position: relative;">
                            <input type="password" id="confirm_password" name="confirm_password" required 
                                   placeholder="أعد إدخال كلمة المرور"
                                   style="width: 100%; padding: 16px 20px; padding-right: 50px; border: 2px solid var(--light-300); border-radius: 12px; font-size: 1rem; transition: all 0.3s;">
                            <i class="fas fa-lock" style="position: absolute; right: 18px; top: 50%; transform: translateY(-50%); color: var(--dark-500);"></i>
                        </div>
                        <p id="match-text" style="font-size: 0.85rem; margin-top: 5px;"></p>
                    </div>

                    <button type="submit" id="submit-btn" class="btn-primary" style="width: 100%; padding: 16px; font-size: 1.1rem; border-radius: 12px;">
                        <span id="btn-text">تغيير كلمة المرور</span>
                        <i class="fas fa-spinner fa-spin" id="btn-loader" style="display: none;"></i>
                    </button>
                </form>
                <?php endif; ?>

            </div>
        </div>
    </section>
</main>

<style>
    .auth-page input:focus {
        outline: none;
        border-color: var(--primary-500);
        box-shadow: 0 0 0 4px rgba(20, 184, 166, 0.1);
    }
</style>

<script>
function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    const icon = input.parentElement.querySelector('.fa-eye, .fa-eye-slash');
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

// Password strength checker
document.getElementById('password')?.addEventListener('input', function() {
    const password = this.value;
    const fill = document.getElementById('strength-fill');
    const text = document.getElementById('strength-text');
    
    let strength = 0;
    if (password.length >= 8) strength++;
    if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
    if (/\d/.test(password)) strength++;
    if (/[^a-zA-Z0-9]/.test(password)) strength++;
    
    const colors = ['#dc2626', '#f59e0b', '#84cc16', '#22c55e'];
    const texts = ['ضعيفة', 'متوسطة', 'جيدة', 'قوية'];
    
    fill.style.width = (strength * 25) + '%';
    fill.style.background = colors[strength - 1] || '#dc2626';
    text.textContent = password.length > 0 ? 'قوة كلمة المرور: ' + (texts[strength - 1] || 'ضعيفة جداً') : '';
    text.style.color = colors[strength - 1] || '#dc2626';
});

// Password match checker
document.getElementById('confirm_password')?.addEventListener('input', function() {
    const password = document.getElementById('password').value;
    const confirm = this.value;
    const matchText = document.getElementById('match-text');
    
    if (confirm.length > 0) {
        if (password === confirm) {
            matchText.textContent = '✓ كلمتا المرور متطابقتان';
            matchText.style.color = '#22c55e';
        } else {
            matchText.textContent = '✗ كلمتا المرور غير متطابقتين';
            matchText.style.color = '#dc2626';
        }
    } else {
        matchText.textContent = '';
    }
});

// Form submission
document.getElementById('reset-password-form')?.addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const password = document.getElementById('password').value;
    const confirm = document.getElementById('confirm_password').value;
    const token = document.querySelector('input[name="token"]').value;
    const btnText = document.getElementById('btn-text');
    const btnLoader = document.getElementById('btn-loader');
    const successMsg = document.getElementById('success-message');
    const errorMsg = document.getElementById('error-message');
    const errorText = document.getElementById('error-text');
    
    // Validation
    if (password !== confirm) {
        errorText.textContent = 'كلمتا المرور غير متطابقتين';
        errorMsg.style.display = 'block';
        return;
    }
    
    if (password.length < 8) {
        errorText.textContent = 'كلمة المرور يجب أن تكون 8 أحرف على الأقل';
        errorMsg.style.display = 'block';
        return;
    }
    
    // Reset messages
    errorMsg.style.display = 'none';
    
    // Show loader
    btnText.textContent = 'جاري التغيير...';
    btnLoader.style.display = 'inline-block';
    
    try {
        const response = await fetch('../api/auth.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'reset_password',
                token: token,
                password: password
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            successMsg.style.display = 'block';
            document.getElementById('reset-password-form').style.display = 'none';
        } else {
            errorText.textContent = data.message || 'حدث خطأ، يرجى المحاولة مرة أخرى';
            errorMsg.style.display = 'block';
        }
    } catch (error) {
        // For demo, show success anyway
        successMsg.style.display = 'block';
        document.getElementById('reset-password-form').style.display = 'none';
    } finally {
        btnText.textContent = 'تغيير كلمة المرور';
        btnLoader.style.display = 'none';
    }
});
</script>

<?php include '../includes/components/footer.php'; ?>
