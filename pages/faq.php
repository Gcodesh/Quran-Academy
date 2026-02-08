<?php include '../includes/components/header.php'; ?>

<main class="faq-page">
    <!-- Hero Section -->
    <section class="faq-hero" style="background: var(--gradient-hero); color: white; padding: 80px 0; text-align: center;">
        <div class="container">
            <h1 style="font-size: 3rem; margin-bottom: 20px;" data-aos="fade-up">الأسئلة الشائعة</h1>
            <p style="font-size: 1.3rem; opacity: 0.9; max-width: 600px; margin: 0 auto;" data-aos="fade-up" data-aos-delay="100">
                إجابات على أكثر الأسئلة شيوعاً حول منصتنا ودوراتنا
            </p>
        </div>
    </section>

    <!-- FAQ Content -->
    <section class="faq-content" style="padding: 80px 0; background: var(--light-100);">
        <div class="container" style="max-width: 900px;">
            
            <!-- Category: Registration -->
            <div class="faq-category" style="margin-bottom: 50px;" data-aos="fade-up">
                <h2 style="font-size: 1.8rem; color: var(--primary-700); margin-bottom: 25px; display: flex; align-items: center; gap: 15px;">
                    <i class="fas fa-user-plus" style="background: var(--primary-100); padding: 15px; border-radius: 12px;"></i>
                    التسجيل والحساب
                </h2>
                
                <div class="faq-accordion">
                    <div class="faq-item" style="background: white; border-radius: 16px; margin-bottom: 15px; overflow: hidden; box-shadow: var(--shadow-sm);">
                        <button class="faq-question" onclick="toggleFaq(this)" style="width: 100%; padding: 25px; display: flex; justify-content: space-between; align-items: center; border: none; background: none; cursor: pointer; text-align: right;">
                            <span style="font-size: 1.1rem; font-weight: 600; color: var(--dark-800);">كيف أقوم بإنشاء حساب جديد؟</span>
                            <i class="fas fa-chevron-down" style="color: var(--primary-600); transition: transform 0.3s;"></i>
                        </button>
                        <div class="faq-answer" style="max-height: 0; overflow: hidden; transition: max-height 0.3s ease;">
                            <p style="padding: 0 25px 25px; color: var(--dark-600); line-height: 1.8;">
                                يمكنك إنشاء حساب جديد بالضغط على زر "انضم إلينا" في أعلى الصفحة، ثم إدخال بياناتك الأساسية (الاسم، البريد الإلكتروني، كلمة المرور). ستصلك رسالة تأكيد على بريدك الإلكتروني لتفعيل الحساب.
                            </p>
                        </div>
                    </div>
                    
                    <div class="faq-item" style="background: white; border-radius: 16px; margin-bottom: 15px; overflow: hidden; box-shadow: var(--shadow-sm);">
                        <button class="faq-question" onclick="toggleFaq(this)" style="width: 100%; padding: 25px; display: flex; justify-content: space-between; align-items: center; border: none; background: none; cursor: pointer; text-align: right;">
                            <span style="font-size: 1.1rem; font-weight: 600; color: var(--dark-800);">نسيت كلمة المرور، ماذا أفعل؟</span>
                            <i class="fas fa-chevron-down" style="color: var(--primary-600); transition: transform 0.3s;"></i>
                        </button>
                        <div class="faq-answer" style="max-height: 0; overflow: hidden; transition: max-height 0.3s ease;">
                            <p style="padding: 0 25px 25px; color: var(--dark-600); line-height: 1.8;">
                                اضغط على رابط "نسيت كلمة المرور" في صفحة تسجيل الدخول، ثم أدخل بريدك الإلكتروني المسجل. سنرسل لك رابطاً لإعادة تعيين كلمة المرور.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Category: Courses -->
            <div class="faq-category" style="margin-bottom: 50px;" data-aos="fade-up">
                <h2 style="font-size: 1.8rem; color: var(--primary-700); margin-bottom: 25px; display: flex; align-items: center; gap: 15px;">
                    <i class="fas fa-book-open" style="background: var(--primary-100); padding: 15px; border-radius: 12px;"></i>
                    الدورات والتعلم
                </h2>
                
                <div class="faq-accordion">
                    <div class="faq-item" style="background: white; border-radius: 16px; margin-bottom: 15px; overflow: hidden; box-shadow: var(--shadow-sm);">
                        <button class="faq-question" onclick="toggleFaq(this)" style="width: 100%; padding: 25px; display: flex; justify-content: space-between; align-items: center; border: none; background: none; cursor: pointer; text-align: right;">
                            <span style="font-size: 1.1rem; font-weight: 600; color: var(--dark-800);">كيف أسجل في دورة؟</span>
                            <i class="fas fa-chevron-down" style="color: var(--primary-600); transition: transform 0.3s;"></i>
                        </button>
                        <div class="faq-answer" style="max-height: 0; overflow: hidden; transition: max-height 0.3s ease;">
                            <p style="padding: 0 25px 25px; color: var(--dark-600); line-height: 1.8;">
                                بعد تسجيل الدخول، تصفح الدورات المتاحة واختر الدورة التي تناسبك. اضغط على زر "سجل الآن" واتبع خطوات الدفع (إن كانت مدفوعة) أو التسجيل المباشر (للدورات المجانية).
                            </p>
                        </div>
                    </div>
                    
                    <div class="faq-item" style="background: white; border-radius: 16px; margin-bottom: 15px; overflow: hidden; box-shadow: var(--shadow-sm);">
                        <button class="faq-question" onclick="toggleFaq(this)" style="width: 100%; padding: 25px; display: flex; justify-content: space-between; align-items: center; border: none; background: none; cursor: pointer; text-align: right;">
                            <span style="font-size: 1.1rem; font-weight: 600; color: var(--dark-800);">هل الدورات متاحة مدى الحياة؟</span>
                            <i class="fas fa-chevron-down" style="color: var(--primary-600); transition: transform 0.3s;"></i>
                        </button>
                        <div class="faq-answer" style="max-height: 0; overflow: hidden; transition: max-height 0.3s ease;">
                            <p style="padding: 0 25px 25px; color: var(--dark-600); line-height: 1.8;">
                                نعم! بمجرد التسجيل في أي دورة، تحصل على وصول مدى الحياة لجميع المحتوى والتحديثات المستقبلية للدورة.
                            </p>
                        </div>
                    </div>
                    
                    <div class="faq-item" style="background: white; border-radius: 16px; margin-bottom: 15px; overflow: hidden; box-shadow: var(--shadow-sm);">
                        <button class="faq-question" onclick="toggleFaq(this)" style="width: 100%; padding: 25px; display: flex; justify-content: space-between; align-items: center; border: none; background: none; cursor: pointer; text-align: right;">
                            <span style="font-size: 1.1rem; font-weight: 600; color: var(--dark-800);">هل يمكنني الحصول على شهادة؟</span>
                            <i class="fas fa-chevron-down" style="color: var(--primary-600); transition: transform 0.3s;"></i>
                        </button>
                        <div class="faq-answer" style="max-height: 0; overflow: hidden; transition: max-height 0.3s ease;">
                            <p style="padding: 0 25px 25px; color: var(--dark-600); line-height: 1.8;">
                                نعم، عند إتمام أي دورة بنجاح واجتياز الاختبارات المطلوبة، ستحصل على شهادة إتمام معتمدة يمكنك تحميلها ومشاركتها.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Category: Payment -->
            <div class="faq-category" style="margin-bottom: 50px;" data-aos="fade-up">
                <h2 style="font-size: 1.8rem; color: var(--primary-700); margin-bottom: 25px; display: flex; align-items: center; gap: 15px;">
                    <i class="fas fa-credit-card" style="background: var(--gold-300); padding: 15px; border-radius: 12px; color: var(--gold-600);"></i>
                    الدفع والاشتراكات
                </h2>
                
                <div class="faq-accordion">
                    <div class="faq-item" style="background: white; border-radius: 16px; margin-bottom: 15px; overflow: hidden; box-shadow: var(--shadow-sm);">
                        <button class="faq-question" onclick="toggleFaq(this)" style="width: 100%; padding: 25px; display: flex; justify-content: space-between; align-items: center; border: none; background: none; cursor: pointer; text-align: right;">
                            <span style="font-size: 1.1rem; font-weight: 600; color: var(--dark-800);">ما هي طرق الدفع المتاحة؟</span>
                            <i class="fas fa-chevron-down" style="color: var(--primary-600); transition: transform 0.3s;"></i>
                        </button>
                        <div class="faq-answer" style="max-height: 0; overflow: hidden; transition: max-height 0.3s ease;">
                            <p style="padding: 0 25px 25px; color: var(--dark-600); line-height: 1.8;">
                                نقبل الدفع عبر: البطاقات الائتمانية (Visa, Mastercard)، Apple Pay، Google Pay، والتحويل البنكي. جميع عمليات الدفع مشفرة وآمنة 100%.
                            </p>
                        </div>
                    </div>
                    
                    <div class="faq-item" style="background: white; border-radius: 16px; margin-bottom: 15px; overflow: hidden; box-shadow: var(--shadow-sm);">
                        <button class="faq-question" onclick="toggleFaq(this)" style="width: 100%; padding: 25px; display: flex; justify-content: space-between; align-items: center; border: none; background: none; cursor: pointer; text-align: right;">
                            <span style="font-size: 1.1rem; font-weight: 600; color: var(--dark-800);">هل يمكنني استرداد المبلغ؟</span>
                            <i class="fas fa-chevron-down" style="color: var(--primary-600); transition: transform 0.3s;"></i>
                        </button>
                        <div class="faq-answer" style="max-height: 0; overflow: hidden; transition: max-height 0.3s ease;">
                            <p style="padding: 0 25px 25px; color: var(--dark-600); line-height: 1.8;">
                                نعم، نوفر ضمان استرداد المبلغ خلال 14 يوماً من تاريخ الشراء إذا لم تكن راضياً عن الدورة، بشرط عدم إتمام أكثر من 30% من المحتوى.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Category: Technical -->
            <div class="faq-category" data-aos="fade-up">
                <h2 style="font-size: 1.8rem; color: var(--primary-700); margin-bottom: 25px; display: flex; align-items: center; gap: 15px;">
                    <i class="fas fa-cog" style="background: var(--primary-100); padding: 15px; border-radius: 12px;"></i>
                    الدعم الفني
                </h2>
                
                <div class="faq-accordion">
                    <div class="faq-item" style="background: white; border-radius: 16px; margin-bottom: 15px; overflow: hidden; box-shadow: var(--shadow-sm);">
                        <button class="faq-question" onclick="toggleFaq(this)" style="width: 100%; padding: 25px; display: flex; justify-content: space-between; align-items: center; border: none; background: none; cursor: pointer; text-align: right;">
                            <span style="font-size: 1.1rem; font-weight: 600; color: var(--dark-800);">كيف أتواصل مع الدعم الفني؟</span>
                            <i class="fas fa-chevron-down" style="color: var(--primary-600); transition: transform 0.3s;"></i>
                        </button>
                        <div class="faq-answer" style="max-height: 0; overflow: hidden; transition: max-height 0.3s ease;">
                            <p style="padding: 0 25px 25px; color: var(--dark-600); line-height: 1.8;">
                                يمكنك التواصل معنا عبر البريد الإلكتروني: support@islamicedu.com أو من خلال نموذج الاتصال في صفحة "اتصل بنا". نرد عادة خلال 24 ساعة.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <!-- Still Have Questions -->
    <section class="faq-cta" style="padding: 80px 0; background: var(--white); text-align: center;">
        <div class="container" data-aos="fade-up">
            <h2 style="font-size: 2rem; color: var(--dark-800); margin-bottom: 15px;">لم تجد إجابتك؟</h2>
            <p style="font-size: 1.1rem; color: var(--dark-600); margin-bottom: 30px;">تواصل معنا مباشرة وسنسعد بمساعدتك</p>
            <a href="contact.php" class="btn-primary-glow" style="display: inline-flex; align-items: center; gap: 10px; padding: 16px 40px;">
                <i class="fas fa-envelope"></i>
                تواصل معنا
            </a>
        </div>
    </section>
</main>

<style>
    .faq-item.active .faq-answer {
        max-height: 300px !important;
    }
    .faq-item.active .fa-chevron-down {
        transform: rotate(180deg);
    }
    .faq-question:hover {
        background: var(--light-100);
    }
</style>

<script>
function toggleFaq(button) {
    const item = button.parentElement;
    const allItems = document.querySelectorAll('.faq-item');
    
    allItems.forEach(i => {
        if (i !== item) {
            i.classList.remove('active');
        }
    });
    
    item.classList.toggle('active');
}
</script>

<?php include '../includes/components/footer.php'; ?>
