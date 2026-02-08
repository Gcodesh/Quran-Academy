-- Seed Data for Islamic Education Platform
-- This file contains default data for the platform

USE islamic_education;

-- =============================================
-- 1. Default Users
-- =============================================

-- Admin User (password: admin123)
INSERT INTO users (name, email, password, role, status) VALUES
('مدير النظام', 'admin@islamic-edu.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'active');

-- Default Teachers (password: teacher123)
INSERT INTO users (name, email, password, role, status) VALUES
('الأستاذة سارة أحمد', 'sara@islamic-edu.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'teacher', 'active'),
('الأستاذ محمد علي', 'mohamed@islamic-edu.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'teacher', 'active'),
('الأستاذة فاطمة محمود', 'fatima@islamic-edu.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'teacher', 'active'),
('الأستاذ خالد أحمد', 'khaled@islamic-edu.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'teacher', 'active');

-- Default Student (password: student123)
INSERT INTO users (name, email, password, role, status) VALUES
('طالب تجريبي', 'student@islamic-edu.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', 'active');

-- =============================================
-- 2. Default Courses (8 courses matching static page)
-- =============================================

-- Course 1: تعليم القرآن للأطفال (Teacher: سارة)
INSERT INTO courses (title, description, teacher_id, image, status) VALUES
('تعليم القرآن للأطفال', 'دورة شاملة لتعليم الأطفال القرآن بطريقة ممتعة وتفاعلية مع أنشطة تعليمية متنوعة.', 2, 'https://images.unsplash.com/photo-1609599006353-e629aaabfeae?auto=format&fit=crop&w=600', 'published');

-- Course 2: فقه الصلاة (Teacher: محمد)
INSERT INTO courses (title, description, teacher_id, image, status) VALUES
('فقه الصلاة', 'تعلم أساسيات الفقه بطريقة مبسطة وعملية مع تمارين تطبيقية وأمثلة من الحياة اليومية.', 3, NULL, 'published');

-- Course 3: تجويد القرآن (Teacher: فاطمة)
INSERT INTO courses (title, description, teacher_id, image, status) VALUES
('تجويد القرآن', 'دورة متقدمة لتعلم أحكام التجويد والتلاوة الصحيحة مع الأمثلة العملية والتطبيقات الصوتية.', 4, 'https://images.unsplash.com/photo-1585036156171-384164a8c675?auto=format&fit=crop&w=600', 'published');

-- Course 4: شرح صحيح البخاري (Teacher: خالد)
INSERT INTO courses (title, description, teacher_id, image, status) VALUES
('شرح صحيح البخاري', 'دورة متخصصة في شرح أحاديث صحيح البخاري مع فهم المعاني والأحكام المستنبطة منها.', 5, 'https://images.unsplash.com/photo-1592329347814-2f22c54cb2c0?auto=format&fit=crop&w=600', 'published');

-- Course 5: النحو والصرف للمبتدئين (Teacher: سارة)
INSERT INTO courses (title, description, teacher_id, image, status) VALUES
('النحو والصرف للمبتدئين', 'تعلم أساسيات النحو والصرف في اللغة العربية بطريقة سهلة ومبسطة مع أمثلة تطبيقية.', 2, NULL, 'published');

-- Course 6: العقيدة الإسلامية (Teacher: محمد)
INSERT INTO courses (title, description, teacher_id, image, status) VALUES
('العقيدة الإسلامية', 'دراسة شاملة لأركان الإيمان وأصول العقيدة الإسلامية مع الأدلة من القرآن والسنة.', 3, NULL, 'published');

-- Course 7: حفظ القرآن الكريم (Teacher: فاطمة)
INSERT INTO courses (title, description, teacher_id, image, status) VALUES
('حفظ القرآن الكريم', 'برنامج منهجي لحفظ القرآن الكريم مع تقنيات الحفظ المتقدمة وطرق المراجعة الفعالة.', 4, NULL, 'published');

-- Course 8: فقه الزكاة والصيام (Teacher: خالد)
INSERT INTO courses (title, description, teacher_id, image, status) VALUES
('فقه الزكاة والصيام', 'تعلم أحكام الزكاة والصيام بالتفصيل مع التطبيقات العملية والأمثلة المعاصرة.', 5, NULL, 'published');

-- =============================================
-- 3. Default Lessons for each course
-- =============================================

-- Lessons for Course 1 (تعليم القرآن للأطفال)
INSERT INTO lessons (course_id, title, content) VALUES
(1, 'مقدمة عن القرآن الكريم', 'التعريف بالقرآن الكريم وأهمية تعلمه للأطفال'),
(1, 'الحروف الهجائية', 'تعلم نطق الحروف العربية بشكل صحيح'),
(1, 'الحركات الأساسية', 'الفتحة والضمة والكسرة والسكون'),
(1, 'قراءة الكلمات', 'الجمع بين الحروف والحركات لقراءة الكلمات');

-- Lessons for Course 2 (فقه الصلاة)
INSERT INTO lessons (course_id, title, content) VALUES
(2, 'شروط الصلاة', 'تعلم الشروط الواجب توفرها لصحة الصلاة'),
(2, 'أركان الصلاة', 'الأركان التي لا تصح الصلاة بدونها'),
(2, 'واجبات الصلاة', 'ما يجب على المصلي فعله أثناء الصلاة'),
(2, 'سنن الصلاة', 'السنن التي تُكمِّل الصلاة وتزيد أجرها');

-- Lessons for Course 3 (تجويد القرآن)
INSERT INTO lessons (course_id, title, content) VALUES
(3, 'أحكام النون الساكنة', 'الإظهار والإدغام والإقلاب والإخفاء'),
(3, 'أحكام الميم الساكنة', 'الإخفاء الشفوي والإدغام الشفوي والإظهار'),
(3, 'المدود', 'أنواع المدود وأحكامها'),
(3, 'التفخيم والترقيق', 'الحروف المفخمة والمرققة');

-- Lessons for Course 4 (شرح صحيح البخاري)
INSERT INTO lessons (course_id, title, content) VALUES
(4, 'مقدمة عن الإمام البخاري', 'حياة الإمام البخاري ومنهجه في التأليف'),
(4, 'كتاب بدء الوحي', 'شرح أحاديث بدء الوحي'),
(4, 'كتاب الإيمان', 'شرح أحاديث الإيمان'),
(4, 'كتاب العلم', 'شرح أحاديث فضل العلم');

-- Lessons for Course 5 (النحو والصرف)
INSERT INTO lessons (course_id, title, content) VALUES
(5, 'الكلمة وأقسامها', 'الاسم والفعل والحرف'),
(5, 'المبتدأ والخبر', 'تعريف المبتدأ والخبر وإعرابهما'),
(5, 'الفعل الماضي والمضارع', 'أنواع الأفعال وكيفية التفريق بينها'),
(5, 'الفاعل والمفعول به', 'إعراب الفاعل والمفعول به');

-- Lessons for Course 6 (العقيدة الإسلامية)
INSERT INTO lessons (course_id, title, content) VALUES
(6, 'الإيمان بالله', 'توحيد الربوبية والألوهية والأسماء والصفات'),
(6, 'الإيمان بالملائكة', 'التعريف بالملائكة وصفاتهم ووظائفهم'),
(6, 'الإيمان بالكتب', 'الإيمان بالكتب السماوية والقرآن خاتمها'),
(6, 'الإيمان بالرسل', 'الإيمان بالأنبياء والرسل عليهم السلام');

-- Lessons for Course 7 (حفظ القرآن)
INSERT INTO lessons (course_id, title, content) VALUES
(7, 'منهجية الحفظ', 'كيفية وضع خطة للحفظ'),
(7, 'تقنيات الحفظ', 'طرق تساعد على الحفظ السريع'),
(7, 'طرق المراجعة', 'كيفية مراجعة المحفوظ وتثبيته'),
(7, 'التسميع والاختبار', 'أهمية التسميع والاختبار الذاتي');

-- Lessons for Course 8 (فقه الزكاة والصيام)
INSERT INTO lessons (course_id, title, content) VALUES
(8, 'أحكام الزكاة', 'شروط وجوب الزكاة ومقاديرها'),
(8, 'مصارف الزكاة', 'الأصناف الثمانية المستحقة للزكاة'),
(8, 'أحكام الصيام', 'شروط صحة الصيام وما يفسده'),
(8, 'صيام التطوع', 'فضائل صيام التطوع وأنواعه');

-- =============================================
-- 4. Sample Enrollments
-- =============================================

INSERT INTO enrollments (student_id, course_id, progress_percentage) VALUES
(6, 1, 25),
(6, 2, 50),
(6, 3, 10);

-- Done! Default data has been inserted.
SELECT 'Seed data inserted successfully!' as message;
