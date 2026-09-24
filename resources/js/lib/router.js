// Minimal history router: three pages, no dependency.
import { computed, ref } from 'vue';

const normalize = (pathname) => {
    const trimmed = String(pathname || '/').replace(/\/+$/, '');
    return trimmed === '' ? '/' : trimmed;
};

const path = ref(normalize(window.location.pathname));
const hash = ref(window.location.hash);

const sync = () => {
    path.value = normalize(window.location.pathname);
    hash.value = window.location.hash;
};

window.addEventListener('popstate', sync);
window.addEventListener('hashchange', sync);

export const page = computed(() => {
    if (path.value === '/builder') return 'builder';
    if (path.value === '/docs' || path.value.startsWith('/docs/')) return 'docs';
    return 'landing';
});

const scrollToHash = (fragment) => {
    const id = fragment.replace(/^#/, '');
    if (!id) return;
    requestAnimationFrame(() => {
        const el = document.getElementById(id);
        if (el) el.scrollIntoView({ block: 'start' });
    });
};

export function navigate(to, { replace = false } = {}) {
    const url = new URL(to, window.location.origin);
    if (url.origin !== window.location.origin) {
        window.location.assign(to);
        return;
    }
    const target = url.pathname + url.search + url.hash;
    const current = window.location.pathname + window.location.search + window.location.hash;
    if (target !== current) {
        window.history[replace ? 'replaceState' : 'pushState']({}, '', target);
    }
    sync();
    if (url.hash) {
        scrollToHash(url.hash);
    } else if (target !== current) {
        window.scrollTo(0, 0);
    }
}

export function replaceHash(nextHash) {
    const url = new URL(window.location.href);
    url.hash = nextHash || '';
    window.history.replaceState({}, '', url.pathname + url.search + url.hash);
    sync();
}

export function isInternalHref(href) {
    if (!href) return false;
    if (href.startsWith('#')) return true;
    try {
        return new URL(href, window.location.origin).origin === window.location.origin;
    } catch (_error) {
        return false;
    }
}

export function onLinkClick(event, href) {
    if (!isInternalHref(href)) return;
    if (event.defaultPrevented || event.button !== 0) return;
    if (event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) return;
    event.preventDefault();
    navigate(href);
}

export function useRoute() {
    return { path, hash, page };
}
