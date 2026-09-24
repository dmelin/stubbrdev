<script setup>
import { computed } from 'vue';
import { formatBytes, formatMs } from '../../lib/builder';
import CodeWell from '../ui/CodeWell.vue';
import CopyButton from '../ui/CopyButton.vue';
import JsonView from '../ui/JsonView.vue';
import StatusBadge from '../ui/StatusBadge.vue';

const props = defineProps({
    result: { type: [Object, null], default: null },
    sending: { type: Boolean, default: false },
    countdown: { type: [Number, null], default: null },
    error: { type: String, default: '' },
    grow: { type: Boolean, default: true },
});

const meta = computed(() => {
    const result = props.result;
    if (!result) return '';
    const parts = [formatMs(result.ms), formatBytes(result.bytes)];
    if (result.page && result.totalPages) parts.push(`page ${result.page}/${result.totalPages}`);
    if (result.fromCache) parts.push('cached');
    if (result.flaky) parts.push(`flaky ${result.flaky}`);
    return parts.join(' · ');
});

const prettyRaw = () => {
    if (!props.result) return '';
    return props.result.isJson ? JSON.stringify(props.result.data, null, 2) : props.result.raw;
};
</script>

<template>
    <div class="response" :class="{ grow }">
        <div class="response-head">
            <StatusBadge :status="result?.status" />
            <span class="response-meta">{{ sending ? 'in flight…' : meta || 'no response yet' }}</span>
            <CopyButton v-if="result && !result.empty" class="response-copy" :text="prettyRaw" label="Copy" />
        </div>
        <CodeWell :grow="grow">
            <div v-if="sending" class="response-wait">
                <span class="spinner" aria-hidden="true"></span>
                <span v-if="countdown">waiting {{ (countdown / 1000).toFixed(1) }} s on purpose</span>
                <span v-else>sending…</span>
            </div>
            <pre v-else-if="error" class="wrap json-string">{{ error }}</pre>
            <div v-else-if="!result" class="json-dim">// Send a request to see the response here.</div>
            <div v-else-if="result.empty" class="json-dim wrap-text">
                // {{ result.status }} with an empty body.
            </div>
            <JsonView v-else-if="result.isJson" :value="result.data" />
            <pre v-else class="wrap">{{ result.raw }}</pre>
        </CodeWell>
    </div>
</template>

<style scoped>
.response {
    display: flex;
    flex-direction: column;
    gap: 12px;
    min-height: 0;
    min-width: 0;
}

.response.grow {
    flex: 1;
}

.response-head {
    display: flex;
    align-items: center;
    gap: 9px;
    min-height: 32px;
}

.response-meta {
    font: 500 12px var(--font-ui);
    color: var(--text-2);
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.response-copy {
    margin-left: auto;
    color: var(--accent-text);
}

.response-wait {
    display: flex;
    align-items: center;
    gap: 10px;
    color: var(--code-dim);
}

.wrap-text {
    white-space: normal;
}
</style>
