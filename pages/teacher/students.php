<?php
require_once '../../pages/dashboard/layout.php';
require_once '../../includes/config/database.php';
require_once '../../includes/classes/Database.php';

$page_title = 'طلابي';

render_dashboard_layout(function() {
?>
<div class="dash-card">
    <div style="text-align: center; padding: 60px 20px;">
        <div style="background: var(--light-100); width: 80px; height: 80px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; color: var(--dark-400); font-size: 2rem;">
            <i class="fas fa-users-slash"></i>
        </div>
        <h3 style="margin-bottom: 10px;">لا يوجد طلاب حتى الآن</h3>
        <p style="color: var(--dark-500); max-width: 400px; margin: 0 auto 20px;">بمجرد نشر دوراتك وبدء الطلاب في التسجيل، سيظهرون هنا.</p>
    </div>
</div>
<?php
});
?>
