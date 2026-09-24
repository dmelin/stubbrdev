// Shared token state. The landing hero and the builder use the same token so
// the demo only ever requests one token per browser.
import { computed, ref } from 'vue';
import { storage } from './storage';

const TOKEN_KEY = 'stubbr_token';
const KIND_KEY = 'stubbr_token_kind';

// Kept identical to the previous front-end so returning visitors keep working.
export const DEMO_EMAIL = 'somerandomstring@stubbr.dev';

export const token = ref(storage.get(TOKEN_KEY, '') || '');
export const tokenKind = ref(storage.get(KIND_KEY, 'demo') === 'own' ? 'own' : 'demo');
export const tokenLoading = ref(false);
export const tokenError = ref('');

export const shortToken = computed(() => (token.value ? `${token.value.slice(0, 13)}…` : ''));

const fetchJson = async (url) => {
    const response = await fetch(url);
    let data = {};
    try {
        data = await response.json();
    } catch (_error) {
        data = {};
    }
    return { response, data };
};

export async function requestTokenByEmail(email) {
    let { response, data } = await fetchJson(`/api/__token/request?email=${encodeURIComponent(email)}`);
    if (response.status === 409) {
        ({ response, data } = await fetchJson(`/api/__token/recover?email=${encodeURIComponent(email)}`));
    }
    if (!data?.token) {
        throw new Error(data?.message || data?.error || 'Could not get a token right now.');
    }
    return data.token;
}

const setToken = (value, kind) => {
    token.value = value;
    tokenKind.value = kind;
    storage.set(TOKEN_KEY, value);
    storage.set(KIND_KEY, kind);
};

let pending = null;

export function ensureToken({ force = false } = {}) {
    if (token.value && !force) return Promise.resolve(token.value);
    if (pending) return pending;
    tokenLoading.value = true;
    tokenError.value = '';
    pending = requestTokenByEmail(DEMO_EMAIL)
        .then((fresh) => {
            setToken(fresh, 'demo');
            return fresh;
        })
        .catch((error) => {
            tokenError.value = error.message || String(error);
            throw error;
        })
        .finally(() => {
            tokenLoading.value = false;
            pending = null;
        });
    return pending;
}

export async function claimOwnToken(email) {
    tokenLoading.value = true;
    tokenError.value = '';
    try {
        const fresh = await requestTokenByEmail(email);
        setToken(fresh, 'own');
        return fresh;
    } catch (error) {
        tokenError.value = error.message || String(error);
        throw error;
    } finally {
        tokenLoading.value = false;
    }
}

export async function copyText(text) {
    try {
        await navigator.clipboard.writeText(text);
        return true;
    } catch (_error) {
        return false;
    }
}
