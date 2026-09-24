<script setup>
import { computed, nextTick, onMounted, ref, watch } from 'vue';
import { MAX_DEPTH, enabledInstructions, instructionValue, makeField, makeGroup } from '../../lib/builder';
import CodeWell from '../ui/CodeWell.vue';
import BuilderRows from './BuilderRows.vue';

const props = defineProps({
    endpoint: { type: Object, required: true },
    error: { type: String, default: '' },
    grow: { type: Boolean, default: false },
});

const payloadField = ref(null);

const hasPayload = computed(() => String(props.endpoint.payloadText || '').trim() !== '');

const chipSummary = computed(() => enabledInstructions(props.endpoint)
    .map((def) => {
        let value;
        try {
            value = JSON.stringify(instructionValue(def, props.endpoint.instructions[def.key]));
        } catch (_error) {
            value = '…';
        }
        return `"${def.key}": ${value}`;
    })
    .join(', '));

const autogrow = () => {
    const el = payloadField.value;
    if (!el) return;
    el.style.height = 'auto';
    el.style.height = `${el.scrollHeight}px`;
};

onMounted(autogrow);
watch(() => [props.endpoint.id, props.endpoint.payloadText], () => nextTick(autogrow));
</script>

<template>
    <div class="body-editor" :class="{ grow }">
        <CodeWell :grow="grow">
            <div class="code-line json-punct">{</div>
            <div class="payload-line">
                <textarea
                    ref="payloadField"
                    v-model="endpoint.payloadText"
                    class="payload-input"
                    rows="1"
                    spellcheck="false"
                    aria-label="Request payload fields"
                    placeholder='// your real payload, e.g. "page": 1'
                    @input="autogrow"
                />
                <span v-if="hasPayload" class="json-punct payload-comma">,</span>
            </div>
            <div class="code-line indent-1">
                <span class="json-key">"__instructions"</span><span class="json-punct">: {</span>
            </div>
            <div v-if="chipSummary" class="code-line indent-2 json-dim" title="Edit these with the chips above">
                {{ chipSummary }}<span v-if="endpoint.rows.length">,</span>
            </div>
            <template v-if="endpoint.rows.length">
                <div class="code-line indent-2">
                    <span class="json-key">"body"</span><span class="json-punct">: {</span>
                </div>
                <BuilderRows :rows="endpoint.rows" :depth="0" :indent-base="42" />
                <div class="brow-add indent-3">
                    <button type="button" class="dashed-add" @click="endpoint.rows.push(makeField())">+ Add field</button>
                    <button v-if="MAX_DEPTH > 0" type="button" class="dashed-add" @click="endpoint.rows.push(makeGroup())">+ group</button>
                </div>
                <div class="code-line indent-2 json-punct">}</div>
            </template>
            <template v-else>
                <div class="code-line indent-2 json-dim">// no body template, so the response mirrors your payload</div>
                <div class="brow-add indent-2">
                    <button type="button" class="dashed-add" @click="endpoint.rows.push(makeField({ key: 'id', type: 'number', random: true, placeholder: '?counter' }))">+ Add field</button>
                    <button type="button" class="dashed-add" @click="endpoint.rows.push(makeGroup({ key: 'item', repeatEnabled: true, repeat: '3', rows: [makeField({ key: 'id', random: true, placeholder: '?uuid' })] }))">+ Add group</button>
                </div>
            </template>
            <div class="code-line indent-1 json-punct">}</div>
            <div class="code-line json-punct">}</div>
        </CodeWell>
        <p v-if="error" class="error-text body-error" role="alert">{{ error }}</p>
    </div>
</template>

<style scoped>
.body-editor {
    display: flex;
    flex-direction: column;
    gap: 8px;
    min-height: 0;
}

.body-editor.grow {
    flex: 1;
}

.indent-1 {
    padding-left: 14px;
}

.indent-2 {
    padding-left: 28px;
}

.indent-3 {
    padding-left: 42px;
}

.payload-line {
    display: flex;
    align-items: flex-end;
    gap: 2px;
    padding-left: 14px;
}

.payload-input {
    flex: 1;
    min-width: 0;
    padding: 2px 6px;
    margin-left: -6px;
    border-radius: 6px;
    background: transparent;
    color: var(--code-text-strong);
    font: inherit;
    line-height: 1.8;
    overflow: hidden;
    border: 1px solid transparent;
}

.payload-input:focus {
    outline: none;
    background: var(--code-input-bg);
}

.payload-input::placeholder {
    color: var(--code-dim);
}

.payload-comma {
    line-height: 1.8;
    padding-bottom: 3px;
}

.body-error {
    margin: 0;
}
</style>
