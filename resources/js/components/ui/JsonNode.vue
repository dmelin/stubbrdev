<script setup>
// Recursive JSON renderer with collapsible long arrays.
import { computed, ref } from 'vue';

const props = defineProps({
    value: { default: null },
    name: { type: [String, null], default: null },
    depth: { type: Number, default: 0 },
    isLast: { type: Boolean, default: true },
    collapseAt: { type: Number, default: 8 },
    showFirst: { type: Number, default: 4 },
});

const expanded = ref(false);

const kind = computed(() => {
    if (props.value === null) return 'null';
    if (Array.isArray(props.value)) return 'array';
    return typeof props.value;
});

const entries = computed(() => {
    if (kind.value === 'array') return props.value.map((item, index) => [null, item, index]);
    if (kind.value === 'object') return Object.entries(props.value).map(([key, item], index) => [key, item, index]);
    return [];
});

const visibleEntries = computed(() => {
    if (kind.value !== 'array' || expanded.value || entries.value.length <= props.collapseAt) return entries.value;
    return entries.value.slice(0, props.showFirst);
});

const hiddenCount = computed(() => entries.value.length - visibleEntries.value.length);

const indent = computed(() => ({ paddingLeft: `${props.depth * 14}px` }));
const open = computed(() => (kind.value === 'array' ? '[' : '{'));
const close = computed(() => (kind.value === 'array' ? ']' : '}'));
const primitiveClass = computed(() => {
    if (kind.value === 'string') return 'json-string';
    if (kind.value === 'number') return 'json-number';
    if (kind.value === 'boolean') return 'json-bool';
    return 'json-null';
});
const primitiveText = computed(() => (kind.value === 'string' ? JSON.stringify(props.value) : String(props.value)));
</script>

<template>
    <div v-if="kind === 'array' || kind === 'object'" class="json-block">
        <div class="code-line" :style="indent">
            <template v-if="name !== null"><span class="json-key">"{{ name }}"</span><span class="json-punct">: </span></template>
            <span class="json-punct">{{ open }}</span>
            <template v-if="!entries.length"><span class="json-punct">{{ close }}</span><span v-if="!isLast" class="json-punct">,</span></template>
        </div>
        <template v-if="entries.length">
            <JsonNode
                v-for="[key, item, index] in visibleEntries"
                :key="key ?? index"
                :value="item"
                :name="key"
                :depth="depth + 1"
                :is-last="index === entries.length - 1"
                :collapse-at="collapseAt"
                :show-first="showFirst"
            />
            <div v-if="hiddenCount > 0" class="code-line" :style="{ paddingLeft: `${(depth + 1) * 14}px` }">
                <button type="button" class="json-more" @click="expanded = true">… {{ hiddenCount }} more</button>
            </div>
            <div class="code-line" :style="indent">
                <span class="json-punct">{{ close }}</span><span v-if="!isLast" class="json-punct">,</span>
            </div>
        </template>
    </div>
    <div v-else class="code-line" :style="indent">
        <template v-if="name !== null"><span class="json-key">"{{ name }}"</span><span class="json-punct">: </span></template>
        <span :class="primitiveClass">{{ primitiveText }}</span><span v-if="!isLast" class="json-punct">,</span>
    </div>
</template>

<style>
.json-more {
    color: var(--code-dim);
    font: inherit;
    border-radius: 6px;
    padding: 0 6px;
    margin-left: -6px;
}

.json-more:hover {
    color: var(--code-text-strong);
    background: var(--code-input-bg);
}
</style>
