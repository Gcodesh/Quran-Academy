/**
 * Premium Theme Toggle System
 * Handles light/dark mode transitions and persistence
 */

document.addEventListener('DOMContentLoaded', () => {
    const themeToggleBtns = document.querySelectorAll('#themeToggle');
    const htmlElement = document.documentElement;

    // 1. Initial Theme Load
    const savedTheme = localStorage.getItem('theme');
    const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

    const initialTheme = savedTheme || (systemPrefersDark ? 'dark' : 'light');
    applyTheme(initialTheme);

    // 2. Toggle Event Handlers
    themeToggleBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            const currentTheme = htmlElement.getAttribute('data-theme') || 'light';
            const newTheme = currentTheme === 'light' ? 'dark' : 'light';

            applyTheme(newTheme);
            localStorage.setItem('theme', newTheme);

            // Add momentary animation class
            btn.classList.add('switching');
            setTimeout(() => btn.classList.remove('switching'), 300);
        });
    });

    /**
     * Applies the theme to the document and handles side effects
     * @param {string} theme - 'light' or 'dark'
     */
    function applyTheme(theme) {
        htmlElement.setAttribute('data-theme', theme);

        // Update Chart.js defaults if they exist in the dashboard
        if (typeof Chart !== 'undefined') {
            updateChartTheme(theme);
        }
    }

    /**
     * Optional: Update dashboard charts to match theme
     */
    function updateChartTheme(theme) {
        const isDark = theme === 'dark';
        Chart.defaults.color = isDark ? '#94a3b8' : '#64748b';
        Chart.defaults.borderColor = isDark ? '#1e293b' : '#e2e8f0';

        // Refresh visible charts
        Object.values(Chart.instances).forEach(chart => {
            chart.options.scales.x.grid.color = isDark ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.05)';
            chart.options.scales.y.grid.color = isDark ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.05)';
            chart.update();
        });
    }
});
