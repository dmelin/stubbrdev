import { ref } from 'vue';
import { storage } from './storage';

export const THEME_KEY = 'stubbr_theme_v2';
const LEGACY_THEME_KEY = 'stubbr_theme_v1';

export const theme = ref('dark');

const resolveInitialTheme = () => {
    const stored = storage.get(THEME_KEY);
    if (stored === 'dark' || stored === 'light') return stored;
    const legacy = storage.get(LEGACY_THEME_KEY);
    if (legacy === 'ice' || legacy === 'mustard') return 'light';
    return 'dark';
};

export function applyTheme(next) {
    theme.value = next === 'light' ? 'light' : 'dark';
    document.documentElement.dataset.theme = theme.value;
    storage.set(THEME_KEY, theme.value);
}

export function initTheme() {
    applyTheme(resolveInitialTheme());
}

export function toggleTheme() {
    applyTheme(theme.value === 'dark' ? 'light' : 'dark');
}
