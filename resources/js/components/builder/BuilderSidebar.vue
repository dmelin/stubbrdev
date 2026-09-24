<script setup>
import { ref } from 'vue';
import { endpointLabel } from '../../lib/builder';
import { removeEndpoint, selectEndpoint, workspace } from '../../lib/workspace';
import MethodTag from '../ui/MethodTag.vue';

const emit = defineEmits(['new']);

const open = ref(false);
const confirmingId = ref(null);
let confirmTimer = null;

const onRemove = (id) => {
    if (confirmingId.value !== id) {
        confirmingId.value = id;
        if (confirmTimer) window.clearTimeout(confirmTimer);
        confirmTimer = window.setTimeout(() => {
            confirmingId.value = null;
        }, 2200);
        return;
    }
    confirmingId.value = null;
    removeEndpoint(id);
};

const pick = (id) => {
    selectEndpoint(id);
    open.value = false;
};
</script>

<template>
    <aside class="sidebar" :class="{ open }">
        <button type="button" class="sidebar-sheet-toggle" :aria-expanded="open ? 'true' : 'false'" @click="open = !open">
            <span class="kicker">Workspace</span>
            <span class="sidebar-sheet-count">{{ workspace.endpoints.length }} endpoint{{ workspace.endpoints.length === 1 ? '' : 's' }} ▾</span>
        </button>
        <div class="sidebar-body">
            <div class="kicker sidebar-heading">Workspace</div>
            <ul class="sidebar-list">
                <li
                    v-for="endpoint in workspace.endpoints"
                    :key="endpoint.id"
                    class="endpoint-row"
                    :class="{ active: endpoint.id === workspace.activeId }"
                >
                    <button type="button" class="endpoint-row-main" :title="endpoint.name || endpointLabel(endpoint)" @click="pick(endpoint.id)">
                        <MethodTag :method="endpoint.method" />
                        <span class="endpoint-row-label">{{ endpointLabel(endpoint) }}</span>
                    </button>
                    <button
                        type="button"
                        class="endpoint-row-remove"
                        :class="{ confirming: confirmingId === endpoint.id }"
                        :aria-label="confirmingId === endpoint.id ? 'Click again to delete' : `Delete ${endpointLabel(endpoint)}`"
                        @click="onRemove(endpoint.id)"
                    >
                        {{ confirmingId === endpoint.id ? '?' : '×' }}
                    </button>
                </li>
            </ul>
            <button type="button" class="dashed-add dashed-add-row" @click="emit('new')">+ New endpoint</button>
        </div>
    </aside>
</template>

<style scoped>
.sidebar {
    display: flex;
    flex-direction: column;
    min-height: 0;
    border-right: 1px solid var(--border);
}

.sidebar-body {
    display: flex;
    flex-direction: column;
    gap: 6px;
    padding: 18px 14px;
    overflow: auto;
    flex: 1;
    min-height: 0;
}

.sidebar-heading {
    margin-bottom: 6px;
}

.sidebar-list {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.sidebar-sheet-toggle {
    display: none;
}

.endpoint-row-remove.confirming {
    opacity: 1;
    color: var(--warn-text);
    background: var(--warn-tint);
    font-weight: 700;
}


@media (max-width: 760px) {
    .sidebar {
        border-right: 0;
        border-bottom: 1px solid var(--border);
    }

    .sidebar-sheet-toggle {
        display: flex;
        align-items: center;
        justify-content: space-between;
        width: 100%;
        min-height: 44px;
        padding: 10px 16px;
    }

    .sidebar-sheet-count {
        font: 500 12px var(--font-ui);
        color: var(--text-2);
    }

    .sidebar-body {
        display: none;
        padding-top: 4px;
    }

    .sidebar.open .sidebar-body {
        display: flex;
    }

    .sidebar-heading {
        display: none;
    }

}
</style>
