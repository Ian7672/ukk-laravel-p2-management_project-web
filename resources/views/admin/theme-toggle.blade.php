{{-- File: resources/views/components/theme-toggle.blade.php --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const themeToggle = document.querySelector('[data-theme-toggle]');
    const themeIcon = themeToggle?.querySelector('.theme-toggle-icon');
    const themeLabel = themeToggle?.querySelector('.theme-toggle-label');
    
    // Check for saved theme or prefer-color-scheme
    const getPreferredTheme = () => {
        const savedTheme = localStorage.getItem('theme');
        if (savedTheme) return savedTheme;
        return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
    };
    
    // Set theme
    const setTheme = (theme) => {
        document.documentElement.setAttribute('data-theme', theme);
        localStorage.setItem('theme', theme);
        
        if (themeIcon && themeLabel) {
            if (theme === 'light') {
                themeIcon.className = 'theme-toggle-icon bi bi-moon-fill me-2';
                themeLabel.textContent = 'Mode Gelap';
            } else {
                themeIcon.className = 'theme-toggle-icon bi bi-sun-fill me-2';
                themeLabel.textContent = 'Mode Terang';
            }
        }
    };
    
    // Initialize theme
    setTheme(getPreferredTheme());
    
    // Toggle theme on button click
    if (themeToggle) {
        themeToggle.addEventListener('click', () => {
            const currentTheme = document.documentElement.getAttribute('data-theme');
            const newTheme = currentTheme === 'light' ? 'dark' : 'light';
            setTheme(newTheme);
        });
    }
    
    // Listen for system theme changes
    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
        if (!localStorage.getItem('theme')) {
            setTheme(e.matches ? 'dark' : 'light');
        }
    });
});
</script>