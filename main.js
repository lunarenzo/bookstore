document.addEventListener('DOMContentLoaded', () => {
    const themeToggle = document.querySelector('.theme-toggle');
    const html = document.documentElement;
    const themeIcon = themeToggle.querySelector('i');
    const themeText = themeToggle.querySelector('.theme-toggle-text');

    const savedTheme = localStorage.getItem('theme') || 'dark';
    html.setAttribute('data-theme', savedTheme);
    updateThemeUI(savedTheme);

    themeToggle.addEventListener('click', () => {
        const currentTheme = html.getAttribute('data-theme');
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        
        html.setAttribute('data-theme', newTheme);
        localStorage.setItem('theme', newTheme);
        updateThemeUI(newTheme);
    });

    function updateThemeUI(theme) {
        // Update icon
        themeIcon.className = theme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
        
        // Update text
        themeText.textContent = theme === 'dark' ? 'Switch to Light Mode' : 'Switch to Dark Mode';
    }
});