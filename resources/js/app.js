import './bootstrap';
import './theme';


function toggleTheme() {
    const html = document.documentElement

    if (html.classList.contains("dark")) {
        html.classList.remove("dark")
        localStorage.setItem("theme", "light")
    } else {
        html.classList.add("dark")
        localStorage.setItem("theme", "dark")
    }
}

(function () {
    const STORAGE_KEY = 'theme'
    const root = document.documentElement

    // aplica tema inicial
    const savedTheme = localStorage.getItem(STORAGE_KEY)

    if (
        savedTheme === 'dark' ||
        (!savedTheme && window.matchMedia('(prefers-color-scheme: dark)').matches)
    ) {
        root.classList.add('dark')
    } else {
        root.classList.remove('dark')
    }

    // função global
    window.toggleTheme = function () {
        const isDark = root.classList.toggle('dark')
        localStorage.setItem(STORAGE_KEY, isDark ? 'dark' : 'light')
    }
})();