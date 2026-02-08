<?php include '../includes/components/header.php'; ?>

<!-- 1. Hero Modern Section -->
<section class="hero-modern" role="banner" aria-labelledby="hero-heading">
    <div class="hero-gradient"></div>
    <div class="container hero-grid-wrapper" style="display: grid; grid-template-columns: 1.2fr 1fr; align-items: center; gap: 40px;">
        <div class="hero-content">
            <div class="hero-badge" data-aos="fade-up">
                <span>✨ منصة تعليمية متكاملة تجمع بين الأصالة والحداثة</span>
            </div>
            <h1 id="hero-heading" data-aos="fade-up" data-aos-delay="100">
                نور العلم
                <span class="highlight">الإسلامي</span>
            </h1>
            <p class="hero-text" data-aos="fade-up" data-aos-delay="200">
                منصة تعليمية شاملة تقدم دورات متميزة في العلوم الإسلامية والقرآن الكريم والتجويد
            </p>
            <div class="hero-cta" data-aos="fade-up" data-aos-delay="300">
                <a href="courses.php" class="btn-primary"><i class="fas fa-book-open"></i> تصفح الدورات</a>
                <a href="register.php" class="btn-outline"><i class="fas fa-user-plus"></i> سجل مجاناً</a>
            </div>
            <div class="hero-features" data-aos="fade-up" data-aos-delay="400">
                <div class="feature-item">
                    <i class="fas fa-check-circle"></i>
                    <span>+50 دورة متنوعة</span>
                </div>
                <div class="feature-item">
                    <i class="fas fa-check-circle"></i>
                    <span>معلمون متميزون</span>
                </div>
                <div class="feature-item">
                    <i class="fas fa-check-circle"></i>
                    <span>شهادات معتمدة</span>
                </div>
            </div>
        </div>

        <!-- Floating Course Cards -->
        <div class="hero-floating-cards" data-aos="fade-left">
            <div class="floating-card card-1">
                <img src="https://images.unsplash.com/photo-1609599006353-e629aaabfeae?auto=format&fit=crop&w=150" alt="Course">
                <span>القرآن الكريم</span>
            </div>
            <div class="floating-card card-2">
                <img src="https://images.unsplash.com/photo-1585036156171-384164a8c675?auto=format&fit=crop&w=150" alt="Course">
                <span>التجويد</span>
            </div>
            <div class="floating-card card-3">
                <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=150" alt="Course">
                <span>الفقه</span>
            </div>
            <div class="floating-card card-4">
                <img src="https://images.unsplash.com/photo-1573497019940-1c28c88b4f3e?auto=format&fit=crop&w=150" alt="Teacher">
                <span>العقيدة</span>
            </div>
            <div class="floating-card card-5">
                <img src="https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&w=150" alt="Course">
                <span>السيرة</span>
            </div>
            <div class="floating-card card-6">
                <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?auto=format&fit=crop&w=150" alt="Course">
                <span>الحديث</span>
            </div>
        </div>
    </div>
</section>

<!-- 2. Stats Section -->
<section class="stats-section" style="padding: 60px 20px; background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);">
    <div class="container">
        <div class="stats-grid" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 30px; text-align: center;">
            <div class="stat-item" data-aos="fade-up">
                <div class="stat-icon" style="width: 70px; height: 70px; background: linear-gradient(135deg, var(--primary-color), var(--accent-color)); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px;">
                    <i class="fas fa-users" style="font-size: 1.8rem; color: #fff;"></i>
                </div>
                <h3 class="stat-number" data-count="5000">5,000+</h3>
                <p>طالب مسجل</p>
            </div>
            <div class="stat-item" data-aos="fade-up" data-aos-delay="100">
                <div class="stat-icon" style="width: 70px; height: 70px; background: linear-gradient(135deg, var(--secondary-color), #fbbf24); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px;">
                    <i class="fas fa-book" style="font-size: 1.8rem; color: #fff;"></i>
                </div>
                <h3 class="stat-number" data-count="50">50+</h3>
                <p>دورة متنوعة</p>
            </div>
            <div class="stat-item" data-aos="fade-up" data-aos-delay="200">
                <div class="stat-icon" style="width: 70px; height: 70px; background: linear-gradient(135deg, #10b981, #34d399); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px;">
                    <i class="fas fa-clock" style="font-size: 1.8rem; color: #fff;"></i>
                </div>
                <h3 class="stat-number" data-count="1000">1,000+</h3>
                <p>ساعة تعليمية</p>
            </div>
            <div class="stat-item" data-aos="fade-up" data-aos-delay="300">
                <div class="stat-icon" style="width: 70px; height: 70px; background: linear-gradient(135deg, #8b5cf6, #a78bfa); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px;">
                    <i class="fas fa-star" style="font-size: 1.8rem; color: #fff;"></i>
                </div>
                <h3 class="stat-number">4.9</h3>
                <p>تقييم المنصة</p>
            </div>
        </div>
    </div>
</section>

<!-- 3. Featured Courses Section -->
<section class="featured-courses" style="padding: 80px 20px;">
    <div class="container">
        <div class="section-header" style="text-align: center; margin-bottom: 50px;">
            <span class="section-badge" style="background: rgba(16, 185, 129, 0.1); color: #10b981; padding: 8px 20px; border-radius: 20px; font-size: 0.9rem;">الأكثر طلباً</span>
            <h2 style="font-size: 2.5rem; margin: 15px 0;">الدورات المميزة</h2>
            <p style="color: var(--muted-text); max-width: 600px; margin: 0 auto;">اكتشف أفضل الدورات التعليمية التي يختارها طلابنا</p>
        </div>

        <div class="courses-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 30px;">
            <!-- Course 1 -->
            <div class="course-card" data-aos="fade-up">
                <div class="course-image">
                    <img src="https://images.unsplash.com/photo-1609599006353-e629aaabfeae?auto=format&fit=crop&w=600" alt="تعليم القرآن">
                    <span class="badge">الأكثر مبيعاً</span>
                    <button class="wishlist-btn"><i class="far fa-heart"></i></button>
                </div>
                <div class="course-content">
                    <div class="course-meta">
                        <span><i class="fas fa-user"></i> 250 طالب</span>
                        <span><i class="fas fa-clock"></i> 20 ساعة</span>
                    </div>
                    <h3>دورة تعليم القرآن الكريم للمبتدئين</h3>
                    <p>تعلم قراءة القرآن الكريم من الصفر مع أفضل المعلمين المتخصصين</p>
                    <div class="course-footer">
                        <div class="course-rating">
                            <span class="stars">★★★★★</span>
                            <span>(4.9)</span>
                        </div>
                        <span class="course-price">مجاني</span>
                    </div>
                    <a href="course-details.php?id=1" class="btn-primary" style="width: 100%; display: block; text-align: center; margin-top: 15px;">ابدأ التعلم</a>
                </div>
            </div>

            <!-- Course 2 -->
            <div class="course-card" data-aos="fade-up" data-aos-delay="100">
                <div class="course-image">
                    <img src="https://images.unsplash.com/photo-1585036156171-384164a8c675?auto=format&fit=crop&w=600" alt="أحكام التجويد">
                    <span class="badge" style="background: var(--accent-color);">مميز</span>
                    <button class="wishlist-btn"><i class="far fa-heart"></i></button>
                </div>
                <div class="course-content">
                    <div class="course-meta">
                        <span><i class="fas fa-user"></i> 180 طالب</span>
                        <span><i class="fas fa-clock"></i> 15 ساعة</span>
                    </div>
                    <h3>أحكام التجويد المتقدمة</h3>
                    <p>تعلم أحكام التجويد بالتفصيل مع التطبيق العملي والاختبارات</p>
                    <div class="course-footer">
                        <div class="course-rating">
                            <span class="stars">★★★★★</span>
                            <span>(4.8)</span>
                        </div>
                        <span class="course-price">$29</span>
                    </div>
                    <a href="course-details.php?id=2" class="btn-primary" style="width: 100%; display: block; text-align: center; margin-top: 15px;">ابدأ التعلم</a>
                </div>
            </div>
            
             <!-- Course 3 -->
            <div class="course-card" data-aos="fade-up" data-aos-delay="200">
                <div class="course-image">
                    <img src="https://images.unsplash.com/photo-1542816417-0983c9c9ad53?auto=format&fit=crop&w=600" alt="أحكام التجويد">
                    <span class="badge" style="background: var(--accent-color);">مميز</span>
                    <button class="wishlist-btn"><i class="far fa-heart"></i></button>
                </div>
                <div class="course-content">
                    <div class="course-meta">
                        <span><i class="fas fa-user"></i> 180 طالب</span>
                        <span><i class="fas fa-clock"></i> 15 ساعة</span>
                    </div>
                    <h3>أحكام التجويد المتقدمة</h3>
                    <p>تعلم أحكام التجويد بالتفصيل مع التطبيق العملي والاختبارات</p>
                     <div class="course-footer">
                        <div class="course-rating">
                            <span class="stars">★★★★★</span>
                            <span>(4.8)</span>
                        </div>
                        <span class="course-price">$29</span>
                    </div>
                    <a href="course-details.php?id=3" class="btn-primary" style="width: 100%; display: block; text-align: center; margin-top: 15px;">ابدأ التعلم</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 4. Why Choose Us Section -->
<section class="features-section" style="padding: 80px 20px; background: #2c3e50; color: #fff;">
    <div class="container">
        <div class="section-header" style="text-align: center; margin-bottom: 50px;">
           <h2 style="font-size: 2.5rem; margin: 15px 0;">لماذا تختار منصتنا؟</h2>
        </div>
        <div class="features-grid" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 30px;">
             <div class="feature-card" data-aos="fade-up" style="background: rgba(255,255,255,0.1); backdrop-filter: blur(10px); padding: 40px 30px; border-radius: 20px; text-align: center; color: #fff; transition: transform 0.3s;">
                <div class="feature-icon" style="width: 80px; height: 80px; background: rgba(255,255,255,0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                    <i class="fas fa-laptop-house" style="font-size: 2rem;"></i>
                </div>
                <h3 style="margin-bottom: 15px;">تعلم من أي مكان</h3>
                <p style="opacity: 0.9;">ادرس في أي وقت ومن أي مكان باستخدام الهاتف أو الحاسوب</p>
            </div>

            <div class="feature-card" data-aos="fade-up" data-aos-delay="200" style="background: rgba(255,255,255,0.1); backdrop-filter: blur(10px); padding: 40px 30px; border-radius: 20px; text-align: center; color: #fff; transition: transform 0.3s;">
                <div class="feature-icon" style="width: 80px; height: 80px; background: rgba(255,255,255,0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                    <i class="fas fa-certificate" style="font-size: 2rem;"></i>
                </div>
                <h3 style="margin-bottom: 15px;">شهادات معتمدة</h3>
                <p style="opacity: 0.9;">احصل على شهادات إتمام معتمدة لكل دورة تنهيها بنجاح</p>
            </div>
             <div class="feature-card" data-aos="fade-up" data-aos-delay="200" style="background: rgba(255,255,255,0.1); backdrop-filter: blur(10px); padding: 40px 30px; border-radius: 20px; text-align: center; color: #fff; transition: transform 0.3s;">
                <div class="feature-icon" style="width: 80px; height: 80px; background: rgba(255,255,255,0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                    <i class="fas fa-users" style="font-size: 2rem;"></i>
                </div>
                <h3 style="margin-bottom: 15px;">نخبة من العلماء</h3>
                <p style="opacity: 0.9;">نخبة من العلماء والدعاة لتدريس العلوم الشرعية</p>
            </div>
        </div>
    </div>
</section>

<!-- 5. Teachers Section -->
<section class="teachers-section" style="padding: 80px 20px;">
    <div class="container">
        <div class="section-header" style="text-align: center; margin-bottom: 50px;">
            <span class="section-badge" style="background: rgba(139, 92, 246, 0.1); color: #8b5cf6; padding: 8px 20px; border-radius: 20px; font-size: 0.9rem;">فريق المعلمين</span>
            <h2 style="font-size: 2.5rem; margin: 15px 0;">معلمون متميزون</h2>
            <p style="color: var(--muted-text); max-width: 600px; margin: 0 auto;">تعرف على معلمينا المتخصصين في مختلف العلوم الإسلامية</p>
        </div>

        <div class="teachers-slider" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 25px;">
            <!-- Teacher 1 -->
            <div class="teacher-card" data-aos="fade-up" style="background: #fff; border-radius: 20px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.08); transition: all 0.3s;">
                <div style="height: 200px; overflow: hidden;">
                    <img src="https://images.unsplash.com/photo-1573497019940-1c28c88b4f3e?auto=format&fit=crop&w=300" alt="Teacher" style="width: 100%; height: 100%; object-fit: cover;">
                </div>
                <div style="padding: 20px; text-align: center;">
                    <h4 style="margin-bottom: 5px;">الأستاذة سارة أحمد</h4>
                    <p style="color: var(--accent-color); font-size: 0.9rem; margin-bottom: 10px;">متخصص في القرآن الكريم</p>
                    <div style="display: flex; justify-content: center; gap: 15px; color: var(--muted-text); font-size: 0.85rem;">
                        <span><i class="fas fa-book"></i> 12 دورة</span>
                        <span><i class="fas fa-users"></i> 500+ طالب</span>
                    </div>
                </div>
            </div>

            <!-- Teacher 2 -->
            <div class="teacher-card" data-aos="fade-up" data-aos-delay="100" style="background: #fff; border-radius: 20px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.08); transition: all 0.3s;">
                <div style="height: 200px; overflow: hidden;">
                    <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=300" alt="Teacher" style="width: 100%; height: 100%; object-fit: cover;">
                </div>
                <div style="padding: 20px; text-align: center;">
                    <h4 style="margin-bottom: 5px;">الأستاذ محمد علي</h4>
                    <p style="color: var(--accent-color); font-size: 0.9rem; margin-bottom: 10px;">متخصص في الفقه والعقيدة</p>
                    <div style="display: flex; justify-content: center; gap: 15px; color: var(--muted-text); font-size: 0.85rem;">
                        <span><i class="fas fa-book"></i> 8 دورات</span>
                        <span><i class="fas fa-users"></i> 350+ طالب</span>
                    </div>
                </div>
            </div>

            <!-- Teacher 3 -->
            <div class="teacher-card" data-aos="fade-up" data-aos-delay="200" style="background: #fff; border-radius: 20px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.08); transition: all 0.3s;">
                <div style="height: 200px; overflow: hidden;">
                    <img src="https://images.unsplash.com/photo-1580489944761-15a19d654956?auto=format&fit=crop&w=300" alt="Teacher" style="width: 100%; height: 100%; object-fit: cover;">
                </div>
                <div style="padding: 20px; text-align: center;">
                    <h4 style="margin-bottom: 5px;">الأستاذة نور الهدى</h4>
                    <p style="color: var(--accent-color); font-size: 0.9rem; margin-bottom: 10px;">متخصص في اللغة العربية</p>
                    <div style="display: flex; justify-content: center; gap: 15px; color: var(--muted-text); font-size: 0.85rem;">
                        <span><i class="fas fa-book"></i> 6 دورات</span>
                        <span><i class="fas fa-users"></i> 280+ طالب</span>
                    </div>
                </div>
            </div>

            <!-- Teacher 4 -->
            <div class="teacher-card" data-aos="fade-up" data-aos-delay="300" style="background: #fff; border-radius: 20px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.08); transition: all 0.3s;">
                <div style="height: 200px; overflow: hidden;">
                    <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?auto=format&fit=crop&w=300" alt="Teacher" style="width: 100%; height: 100%; object-fit: cover;">
                </div>
                <div style="padding: 20px; text-align: center;">
                    <h4 style="margin-bottom: 5px;">الشيخ عبدالله الحسيني</h4>
                    <p style="color: var(--accent-color); font-size: 0.9rem; margin-bottom: 10px;">متخصص في الحديث والسيرة</p>
                    <div style="display: flex; justify-content: center; gap: 15px; color: var(--muted-text); font-size: 0.85rem;">
                        <span><i class="fas fa-book"></i> 10 دورات</span>
                        <span><i class="fas fa-users"></i> 600+ طالب</span>
                    </div>
                </div>
            </div>
        </div>

        <div style="text-align: center; margin-top: 40px;">
            <a href="teachers.php" class="btn-outline" style="padding: 15px 40px;"><i class="fas fa-users"></i> عرض جميع المعلمين</a>
        </div>
    </div>
</section>

<!-- 6. Testimonials Section -->
<section class="testimonials" style="padding: 80px 20px; background: #f8f9fa;">
    <div class="container">
        <div class="section-header" style="text-align: center; margin-bottom: 50px;">
            <span class="section-badge" style="background: rgba(16, 185, 129, 0.1); color: #10b981; padding: 8px 20px; border-radius: 20px; font-size: 0.9rem;">آراء طلابنا</span>
            <h2 style="font-size: 2.5rem; margin: 15px 0;">ماذا يقول طلابنا؟</h2>
            <p style="color: var(--muted-text); max-width: 600px; margin: 0 auto;">اقرأ تجارب طلابنا المميزة مع منصتنا</p>
        </div>

        <div class="testimonials-grid" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 30px;">
            <!-- Testimonial 1 -->
            <div class="testimonial-card" data-aos="fade-up" style="background: #fff; padding: 30px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.08);">
                <div style="display: flex; align-items: center; margin-bottom: 20px;">
                    <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=100" alt="Student" style="width: 60px; height: 60px; border-radius: 50%; object-fit: cover; margin-left: 15px;">
                    <div>
                        <h4 style="margin: 0;">أم محمد</h4>
                        <div style="color: #fbbf24;">★★★★★</div>
                    </div>
                </div>
                <p style="color: var(--text-color); line-height: 1.8;">"منصة رائعة ساعدت أطفالي على تعلم القرآن الكريم بطريقة ممتعة ومشوقة. المعلمون متميزون والمحتوى متنوع."</p>
            </div>

            <!-- Testimonial 2 -->
            <div class="testimonial-card" data-aos="fade-up" data-aos-delay="100" style="background: #fff; padding: 30px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.08);">
                <div style="display: flex; align-items: center; margin-bottom: 20px;">
                    <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=100" alt="Student" style="width: 60px; height: 60px; border-radius: 50%; object-fit: cover; margin-left: 15px;">
                    <div>
                        <h4 style="margin: 0;">أحمد يوسف</h4>
                        <div style="color: #fbbf24;">★★★★★</div>
                    </div>
                </div>
                <p style="color: var(--text-color); line-height: 1.8;">"استفدت كثيراً من دورات الفقه والعقيدة. الشرح واضح ومبسط والمعلم متمكن من مادته."</p>
            </div>

            <!-- Testimonial 3 -->
            <div class="testimonial-card" data-aos="fade-up" data-aos-delay="200" style="background: #fff; padding: 30px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.08);">
                <div style="display: flex; align-items: center; margin-bottom: 20px;">
                    <img src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?auto=format&fit=crop&w=100" alt="Student" style="width: 60px; height: 60px; border-radius: 50%; object-fit: cover; margin-left: 15px;">
                    <div>
                        <h4 style="margin: 0;">فاطمة خالد</h4>
                        <div style="color: #fbbf24;">★★★★★</div>
                    </div>
                </div>
                <p style="color: var(--text-color); line-height: 1.8;">"أفضل منصة لتعلم التجويد والقراءة الصحيحة. أنصح الجميع بالتسجيل فيها."</p>
            </div>
        </div>
    </div>
</section>

<!-- 7. CTA Section -->
<section class="cta-section" style="padding: 100px 20px; background: linear-gradient(135deg, var(--primary-color), var(--accent-color)); text-align: center; color: #fff;">
    <div class="container">
        <h2 data-aos="fade-up" style="font-size: 2.5rem; margin-bottom: 20px;">ابدأ رحلتك التعليمية اليوم</h2>
        <p data-aos="fade-up" data-aos-delay="100" style="font-size: 1.2rem; max-width: 600px; margin: 0 auto 30px; opacity: 0.9;">انضم إلى آلاف الطلاب الذين يتعلمون معنا واستفد من دوراتنا المتميزة</p>
        <div data-aos="fade-up" data-aos-delay="200">
            <a href="register.php" class="btn-secondary" style="background: #fff; color: var(--primary-color); padding: 18px 50px; font-size: 1.1rem; margin-left: 15px;"><i class="fas fa-user-plus"></i> سجل الآن مجاناً</a>
            <a href="courses.php" class="btn-outline" style="border-color: #fff; color: #fff; padding: 18px 50px; font-size: 1.1rem;"><i class="fas fa-book-open"></i> تصفح الدورات</a>
        </div>
    </div>
</section>

<?php include '../includes/components/footer.php'; ?>
