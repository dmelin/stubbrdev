// Tiny localStorage wrapper that never throws (private mode, quota, SSR).
export const storage = {
    get(key, fallback = null) {
        try {
            const raw = localStorage.getItem(key);
            return raw === null ? fallback : raw;
        } catch (_error) {
            return fallback;
        }
    },
    getJson(key, fallback = null) {
        try {
            const raw = localStorage.getItem(key);
            return raw === null ? fallback : JSON.parse(raw);
        } catch (_error) {
            return fallback;
        }
    },
    set(key, value) {
        try {
            localStorage.setItem(key, value);
            return true;
        } catch (_error) {
            return false;
        }
    },
    setJson(key, value) {
        return this.set(key, JSON.stringify(value));
    },
    remove(key) {
        try {
            localStorage.removeItem(key);
        } catch (_error) {
            // ignore
        }
    },
};
