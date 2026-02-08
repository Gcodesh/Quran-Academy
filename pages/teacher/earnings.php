<?php
require_once '../../pages/dashboard/layout.php';

$page_title = 'الأرباح';

render_dashboard_layout(function() {
?>
<div class="dash-card">
    <div style="text-align: center; padding: 60px 20px;">
        <i class="fas fa-wallet fa-4x" style="color: var(--primary-300); margin-bottom: 20px;"></i>
        <h2 style="margin-bottom: 10px;">لوحة الأرباح والمدفوعات</h2>
        <p style="color: var(--dark-500); font-size: 1.1rem;">هذه الميزة ستكون متاحة قريباً!</p>
        <span style="background: var(--primary-100); color: var(--primary-700); padding: 5px 15px; border-radius: 20px; font-weight: 600; margin-top: 15px; display: inline-block;">قريباً</span>
    </div>
</div>
<?php
});
?>
