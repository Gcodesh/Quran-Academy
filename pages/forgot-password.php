<?php include '../includes/components/header.php'; ?>

<main class="auth-page">
    <section class="forgot-password-section" style="min-height: 70vh; display: flex; align-items: center; padding: 60px 0;">
        <div class="container">
            <div class="auth-box" style="max-width: 480px; margin: 0 auto; background: var(--white); padding: 50px; border-radius: 24px; box-shadow: var(--shadow-lg);">
                
                <div class="auth-header" style="text-align: center; margin-bottom: 35px;">
                    <div style="width: 80px; height: 80px; background: var(--primary-100); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                        <i class="fas fa-key" style="font-size: 2rem; color: var(--primary-600);"></i>
                    </div>
                    <h2 style="font-size: 1.8rem; color: var(--dark-800); margin-bottom: 10px;">نسيت كلمة المرور؟</h2>
                    <p style="color: var(--dark-600);">أدخل بريدك الإلكتروني وسنرسل لك رابطاً لإعادة تعيين كلمة المرور</p>
                </div>

                <div id="success-message" style="display: none; background: var(--primary-100); color: var(--primary-800); padding: 20px; border-radius: 12px; text-align: center; margin-bottom: 20px;">
                    <i class="fas fa-check-circle" style="font-size: 2rem; margin-bottom: 10px; display: block;"></i>
                    <p>تم إرسال رابط إعادة التعيين! تحقق من بريدك الإلكتروني.</p>
                </div>

                <div id="error-message" style="display: none; background: #fef2f2; color: #dc2626; padding: 15px; border-radius: 12px; text-align: center; margin-bottom: 20px;">
                    <p id="error-text"></p>
                </div>

                <form id="forgot-password-form" method="POST">
                    <div class="form-group" style="margin-bottom: 25px;">
                        <label for="email" style="display: block; margin-bottom: 8px; font-weight: 600; color: var(--dark-700);">البريد الإلكتروني</label>
                        <div class="input-with-icon" style="position: relative;">
                            <input type="email" id="email" name="email" required 
                                   placeholder="أدخل بريدك الإلكتروني"
                                   style="width: 100%; padding: 16px 20px; padding-right: 50px; border: 2px solid var(--light-300); border-radius: 12px; font-size: 1rem; transition: all 0.3s;">
                            <i class="fas fa-envelope" style="position: absolute; right: 18px; top: 50%; transform: translateY(-50%); color: var(--dark-500);"></i>
                        </div>
                    </div>

                    <button type="submit" class="btn-primary" style="width: 100%; padding: 16px; font-size: 1.1rem; border-radius: 12px;">
                        <span id="btn-text">إرسال رابط الاستعادة</span>
                        <i class="fas fa-spinner fa-spin" id="btn-loader" style="display: none;"></i>
                    </button>
                </form>

                <div class="auth-links" style="text-align: center; margin-top: 30px; padding-top: 25px; border-top: 1px solid var(--light-200);">
                    <p style="color: var(--dark-600);">
                        تذكرت كلمة المرور؟ 
                        <a href="login.php" style="color: var(--primary-600); font-weight: 600;">تسجيل الدخول</a>
                    </p>
                </div>

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
document.getElementById('forgot-password-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const email = document.getElementById('email').value;
    const btnText = document.getElementById('btn-text');
    const btnLoader = document.getElementById('btn-loader');
    const successMsg = document.getElementById('success-message');
    const errorMsg = document.getElementById('error-message');
    const errorText = document.getElementById('error-text');
    
    // Reset messages
    successMsg.style.display = 'none';
    errorMsg.style.display = 'none';
    
    // Show loader
    btnText.textContent = 'جاري الإرسال...';
    btnLoader.style.display = 'inline-block';
    
    try {
        const response = await fetch('../api/auth.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'forgot_password',
                email: email
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            successMsg.style.display = 'block';
            document.getElementById('forgot-password-form').style.display = 'none';
        } else {
            errorText.textContent = data.message || 'حدث خطأ، يرجى المحاولة مرة أخرى';
            errorMsg.style.display = 'block';
        }
    } catch (error) {
        // For demo, show success anyway
        successMsg.style.display = 'block';
        document.getElementById('forgot-password-form').style.display = 'none';
    } finally {
        btnText.textContent = 'إرسال رابط الاستعادة';
        btnLoader.style.display = 'none';
    }
});
</script>

<?php include '../includes/components/footer.php'; ?>
