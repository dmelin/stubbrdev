// Saved endpoints live in localStorage only (there is no server-side list).
import { computed, reactive, watch } from 'vue';
import { storage } from './storage';
import { sanitizeEndpoint } from './builder';

const WORKSPACE_KEY = 'stubbr_workspace_v1';
const LEGACY_HISTORY_KEY = 'stubbr_builder_history_v1';

export const workspace = reactive({
    endpoints: [],
    activeId: null,
    loaded: false,
});

// The previous front-end kept an undo history; the newest entry becomes the
// first saved endpoint so nobody loses their work on upgrade.
const migrateLegacyHistory = () => {
    const history = storage.getJson(LEGACY_HISTORY_KEY);
    if (!Array.isArray(history) || !history.length) return [];
    const last = history[history.length - 1];
    if (!last || !Array.isArray(last.bodyRows)) return [];
    return [
        sanitizeEndpoint({
            name: 'Restored',
            method: last.requestMethod,
            path: last.endpointPath,
            payloadText: stripOuterBraces(last.filtersText),
            instructions: last.instructions,
            rows: last.bodyRows,
        }),
    ];
};

// Old payload fields were typed as a full object; the new editor takes the
// inner members so the surrounding braces can be drawn by the well.
function stripOuterBraces(text) {
    const trimmed = String(text || '').trim();
    if (trimmed.startsWith('{') && trimmed.endsWith('}')) {
        return trimmed.slice(1, -1).trim();
    }
    return trimmed;
}

let persistTimer = null;
const persist = () => {
    if (persistTimer) window.clearTimeout(persistTimer);
    persistTimer = window.setTimeout(() => {
        storage.setJson(WORKSPACE_KEY, {
            endpoints: workspace.endpoints,
            activeId: workspace.activeId,
        });
    }, 250);
};

export function loadWorkspace() {
    if (workspace.loaded) return;
    const saved = storage.getJson(WORKSPACE_KEY);
    if (saved && Array.isArray(saved.endpoints) && saved.endpoints.length) {
        workspace.endpoints = saved.endpoints.map(sanitizeEndpoint);
        workspace.activeId = workspace.endpoints.some((e) => e.id === saved.activeId)
            ? saved.activeId
            : workspace.endpoints[0].id;
    } else {
        const legacy = migrateLegacyHistory();
        workspace.endpoints = legacy;
        workspace.activeId = legacy[0]?.id ?? null;
        if (legacy.length) storage.remove(LEGACY_HISTORY_KEY);
    }
    workspace.loaded = true;
    watch(
        () => JSON.stringify([workspace.endpoints, workspace.activeId]),
        persist,
    );
}

export const activeEndpoint = computed(
    () => workspace.endpoints.find((endpoint) => endpoint.id === workspace.activeId) || null,
);

export function addEndpoint(endpoint, { activate = true } = {}) {
    workspace.endpoints.push(endpoint);
    if (activate) workspace.activeId = endpoint.id;
    return endpoint;
}

export function selectEndpoint(id) {
    if (workspace.endpoints.some((endpoint) => endpoint.id === id)) {
        workspace.activeId = id;
    }
}

export function removeEndpoint(id) {
    const index = workspace.endpoints.findIndex((endpoint) => endpoint.id === id);
    if (index === -1) return;
    workspace.endpoints.splice(index, 1);
    if (workspace.activeId === id) {
        const next = workspace.endpoints[index] || workspace.endpoints[index - 1] || null;
        workspace.activeId = next ? next.id : null;
    }
}
