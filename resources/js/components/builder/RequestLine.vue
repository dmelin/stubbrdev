<script setup>
import { HTTP_METHODS, methodSendsBody, methodTone } from '../../lib/builder';
import UiSelect from '../ui/UiSelect.vue';

defineProps({
    endpoint: { type: Object, required: true },
    prefix: { type: String, default: '/api/' },
});
</script>

<template>
    <div class="request-block">
    <div class="request-line">
        <UiSelect
            v-model="endpoint.method"
            :options="HTTP_METHODS"
            label="HTTP method"
            :tone="methodTone(endpoint.method)"
        />
        <label class="field field-mono request-path">
            <span class="field-prefix">{{ prefix }}</span>
            <input
                v-model="endpoint.path"
                type="text"
                placeholder="your/endpoint"
                aria-label="Endpoint path"
                spellcheck="false"
                autocapitalize="off"
                autocomplete="off"
            >
        </label>
        <slot />
    </div>
    <p v-if="!methodSendsBody(endpoint.method)" class="request-hint">
        This builder will use POST for this request, as browsers do not send a JSON body with {{ endpoint.method }}. Stubbr replies the same way, and the code snippets keep {{ endpoint.method }}.
    </p>
    </div>
</template>

<style scoped>
.request-block {
    display: flex;
    flex-direction: column;
    gap: 8px;
    min-width: 0;
}

.request-hint {
    font: 400 12px/1.5 var(--font-ui);
    color: var(--text-2);
}

.request-line {
    display: flex;
    align-items: center;
    gap: 9px;
    min-width: 0;
}

.request-path {
    flex: 1;
    min-width: 0;
    gap: 0;
    padding-left: 12px;
}

.request-path input {
    color: var(--text);
    font-weight: 500;
}
</style>
