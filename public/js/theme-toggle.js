// theme-toggle.js
// Apply saved theme before paint (also set by inline script in <head>)
(function() {
    const t = localStorage.getItem('theme') || 'light';
    document.documentElement.dataset.theme = t;
})();

// After DOM loads, sync the theme label in the dropdown (if present)
document.addEventListener('DOMContentLoaded', function () {
    const label = document.getElementById('themeLabel');
    if (label) {
        const current = document.documentElement.dataset.theme || 'light';
        label.textContent = current === 'dark' ? 'Mode Gelap' : 'Mode Terang';
    }
});
