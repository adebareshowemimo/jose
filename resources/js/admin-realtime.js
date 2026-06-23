import './bootstrap';

/**
 * Live admin notifications. Listens on the private `admin.notifications` channel and,
 * with no page reload: updates the topbar bell + all sidebar badges in place, pops a
 * toast (reusing window.showToast), and plays a short chime.
 *
 * Only runs on admin pages (guarded by the <meta name="admin-realtime"> flag) and only
 * once Echo is configured (VITE_REVERB_* set in .env and assets rebuilt).
 */
const enabled = document.querySelector('meta[name="admin-realtime"]');

if (enabled && window.Echo) {
    window.Echo.private('admin.notifications')
        .listen('.admin.notification', (e) => {
            updateBadges(e.counts || {});
            popToast(e);
            chime();
        });
}

function setBadge(el, value, max = 99) {
    if (!el) return;
    const n = Number(value) || 0;
    el.textContent = n > max ? `${max}+` : String(n);
    el.classList.toggle('hidden', n <= 0);
}

function updateBadges(counts) {
    // Topbar bell total (uses a 9+ cap to fit its small dot) and dropdown header.
    setBadge(document.querySelector('[data-badge="bellTotal"]'), counts.bellTotal, 9);
    const dropdownTotal = document.querySelector('[data-bell-total]');
    if (dropdownTotal) dropdownTotal.textContent = String(Number(counts.bellTotal) || 0);

    // Sidebar badges — keyed identically to AdminAlerts::sidebarCounts().
    const sidebar = counts.sidebar || {};
    Object.keys(sidebar).forEach((key) => {
        setBadge(document.querySelector(`[data-badge="${key}"]`), sidebar[key]);
    });

    // Bell dropdown per-category counts (status-based: jobs/payments/recruitment/contacts).
    (counts.categories || []).forEach((cat) => {
        const el = document.querySelector(`[data-bell-cat="${cat.key}"]`);
        if (!el) return;
        el.textContent = String(cat.count);
        const active = Number(cat.count) > 0;
        el.classList.toggle('bg-red-100', active);
        el.classList.toggle('text-red-600', active);
        el.classList.toggle('bg-gray-100', !active);
        el.classList.toggle('text-gray-400', !active);
    });
}

function popToast(e) {
    if (typeof window.showToast !== 'function') return;

    const label = escapeHtml(e.label || 'notification');
    const title = escapeHtml(e.title || 'New activity');
    const meta = e.meta
        ? `<span class="block text-xs text-color-muted">${escapeHtml(e.meta)}</span>`
        : '';
    const link = e.url
        ? `<a href="${encodeURI(e.url)}" class="mt-1 inline-block text-xs font-semibold text-[#1AAD94] hover:underline">View &rarr;</a>`
        : '';

    window.showToast(
        `<span class="block text-sm font-semibold text-color-dark">New ${label}</span>`
        + `<span class="block text-sm">${title}</span>${meta}${link}`,
        'info',
        7000
    );
}

let audioCtx;
function chime() {
    try {
        const Ctx = window.AudioContext || window.webkitAudioContext;
        if (!Ctx) return;
        audioCtx = audioCtx || new Ctx();
        if (audioCtx.state === 'suspended') audioCtx.resume();

        const osc = audioCtx.createOscillator();
        const gain = audioCtx.createGain();
        osc.connect(gain);
        gain.connect(audioCtx.destination);

        osc.type = 'sine';
        osc.frequency.setValueAtTime(880, audioCtx.currentTime);          // A5
        osc.frequency.setValueAtTime(1318.5, audioCtx.currentTime + 0.12); // E6
        gain.gain.setValueAtTime(0.0001, audioCtx.currentTime);
        gain.gain.exponentialRampToValueAtTime(0.22, audioCtx.currentTime + 0.02);
        gain.gain.exponentialRampToValueAtTime(0.0001, audioCtx.currentTime + 0.35);

        osc.start();
        osc.stop(audioCtx.currentTime + 0.36);
    } catch (_) {
        // Browsers block audio until the first user gesture — safe to ignore.
    }
}

function escapeHtml(str) {
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}
