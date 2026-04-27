import './bootstrap';
import Alpine from 'alpinejs';
import { createIcons, icons } from 'lucide';

// Alpine.js
window.Alpine = Alpine;
Alpine.start();

// Lucide Icons — render all <i data-lucide="icon-name"></i>
createIcons({ icons });

// Re-render icons after Alpine updates DOM
document.addEventListener('alpine:initialized', () => {
    let pending = false;
    const observer = new MutationObserver(() => {
        if (pending) return;
        pending = true;
        requestAnimationFrame(() => {
            observer.disconnect();
            createIcons({ icons });
            observer.observe(document.body, { childList: true, subtree: true });
            pending = false;
        });
    });
    observer.observe(document.body, { childList: true, subtree: true });
});

// Fade-up on scroll
const fadeObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('visible');
            fadeObserver.unobserve(entry.target);
        }
    });
}, { threshold: 0.1 });

document.querySelectorAll('.fade-up').forEach(el => fadeObserver.observe(el));

// CSRF token for all fetch/AJAX requests
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
if (csrfToken) {
    window.csrfToken = csrfToken;
}

// Toast notification helper
window.showToast = function(message, type = 'success', duration = 5000) {
    let container = document.querySelector('.toast-container');
    if (!container) {
        container = document.createElement('div');
        container.className = 'toast-container';
        document.body.appendChild(container);
    }

    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.innerHTML = `
        <div class="flex items-center justify-between gap-3">
            <span class="text-sm">${message}</span>
            <button onclick="this.closest('.toast').remove()" class="text-color-muted hover:text-color-dark cursor-pointer">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>
    `;
    container.appendChild(toast);
    createIcons({ icons });

    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateX(100%)';
        toast.style.transition = 'all 0.3s ease';
        setTimeout(() => toast.remove(), 300);
    }, duration);
};
