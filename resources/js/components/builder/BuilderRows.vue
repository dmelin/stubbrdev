<script setup>
// One editable line per field or group. Recursive for groups.
import { computed, reactive } from 'vue';
import {
    MAX_DEPTH,
    TYPE_OPTIONS,
    makeField,
    makeGroup,
    onRowTypeChange,
    placeholdersFor,
    randomAllowed,
    setRowKind,
    valuePlaceholderFor,
} from '../../lib/builder';
import UiSelect from '../ui/UiSelect.vue';

const props = defineProps({
    rows: { type: Array, required: true },
    depth: { type: Number, default: 0 },
    indentBase: { type: Number, default: 28 },
    indentStep: { type: Number, default: 22 },
});

// Shared across every instance so drag state survives recursion.
const drag = reactive({ list: null, index: -1, id: null, overId: null });

const kindOptions = computed(() => (props.depth < MAX_DEPTH ? ['field', 'group'] : ['field']));
const canNestGroup = computed(() => props.depth + 1 < MAX_DEPTH);

const indentFor = (depth) => ({ paddingLeft: `${props.indentBase + depth * props.indentStep}px` });
const keySize = (value) => Math.min(Math.max(String(value || '').length, 4), 28);

const remove = (index) => {
    props.rows.splice(index, 1);
};

const onDragStart = (row, index, event) => {
    drag.list = props.rows;
    drag.index = index;
    drag.id = row.id;
    drag.overId = null;
    if (event.dataTransfer) {
        event.dataTransfer.effectAllowed = 'move';
        event.dataTransfer.setData('text/plain', row.id);
    }
};

const onDragOver = (row, event) => {
    if (drag.list !== props.rows || drag.id === row.id) return;
    drag.overId = row.id;
    if (event.dataTransfer) event.dataTransfer.dropEffect = 'move';
};

const onDrop = (targetIndex) => {
    if (drag.list !== props.rows || drag.index < 0 || drag.index === targetIndex) {
        onDragEnd();
        return;
    }
    const [moved] = props.rows.splice(drag.index, 1);
    const insertAt = drag.index < targetIndex ? targetIndex - 1 : targetIndex;
    props.rows.splice(insertAt, 0, moved);
    onDragEnd();
};

const onDragEnd = () => {
    drag.list = null;
    drag.index = -1;
    drag.id = null;
    drag.overId = null;
};
</script>

<template>
    <template v-for="(row, index) in rows" :key="row.id">
        <div
            class="brow-wrap"
            :class="{ dragging: drag.id === row.id, over: drag.overId === row.id }"
            draggable="true"
            @dragstart="onDragStart(row, index, $event)"
            @dragover.prevent="onDragOver(row, $event)"
            @drop.prevent="onDrop(index)"
            @dragend="onDragEnd"
        >
            <div class="brow" :style="indentFor(depth)">
                <span class="brow-grip" aria-hidden="true" title="Drag to reorder">
                    <svg viewBox="0 0 24 24"><circle cx="9" cy="6" r="1.4" /><circle cx="15" cy="6" r="1.4" /><circle cx="9" cy="12" r="1.4" /><circle cx="15" cy="12" r="1.4" /><circle cx="9" cy="18" r="1.4" /><circle cx="15" cy="18" r="1.4" /></svg>
                </span>
                <UiSelect
                    size="sm"
                    class="brow-kind"
                    :class="row.kind === 'group' ? 'brow-kind-group' : ''"
                    :model-value="row.kind"
                    :options="kindOptions"
                    label="Row kind"
                    @update:model-value="(value) => setRowKind(row, value)"
                />
                <input
                    v-model="row.key"
                    class="brow-key"
                    type="text"
                    :size="keySize(row.key)"
                    :placeholder="row.kind === 'group' ? 'group' : 'key'"
                    aria-label="Key"
                    spellcheck="false"
                    autocomplete="off"
                >
                <template v-if="row.kind === 'field'">
                    <UiSelect
                        v-model="row.type"
                        size="sm"
                        class="brow-type"
                        :options="TYPE_OPTIONS"
                        label="Value type"
                        @change="onRowTypeChange(row)"
                    />
                    <button
                        type="button"
                        class="brow-die"
                        :class="{ on: row.random }"
                        :disabled="!randomAllowed(row.type)"
                        :aria-pressed="row.random ? 'true' : 'false'"
                        :title="randomAllowed(row.type) ? 'Generate a random value' : 'No generators for this type'"
                        aria-label="Generate a random value"
                        @click="row.random = !row.random"
                    >
                        <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3.5" y="3.5" width="17" height="17" rx="4" /><circle cx="8.5" cy="8.5" r="1.3" /><circle cx="15.5" cy="8.5" r="1.3" /><circle cx="12" cy="12" r="1.3" /><circle cx="8.5" cy="15.5" r="1.3" /><circle cx="15.5" cy="15.5" r="1.3" /></svg>
                    </button>
                    <UiSelect
                        v-if="row.random"
                        v-model="row.placeholder"
                        size="sm"
                        class="brow-gen"
                        :options="placeholdersFor(row.type)"
                        label="Generator"
                    />
                    <input
                        v-else-if="row.type !== 'boolean' && row.type !== 'null'"
                        v-model="row.value"
                        class="brow-value"
                        type="text"
                        :placeholder="valuePlaceholderFor(row.type)"
                        aria-label="Value"
                        spellcheck="false"
                        autocomplete="off"
                    >
                    <UiSelect
                        v-else-if="row.type === 'boolean'"
                        v-model="row.value"
                        size="sm"
                        :options="['true', 'false']"
                        label="Boolean value"
                    />
                    <span v-else class="brow-null">null</span>
                </template>
                <template v-else>
                    <span class="json-punct">{</span>
                    <span class="brow-repeat" :class="{ on: row.repeatEnabled }">
                        <button
                            type="button"
                            class="brow-repeat-toggle"
                            :aria-pressed="row.repeatEnabled ? 'true' : 'false'"
                            title="Repeat this group as an array (__repeat)"
                            @click="row.repeatEnabled = !row.repeatEnabled"
                        >
                            ×
                        </button>
                        <input
                            v-if="row.repeatEnabled"
                            v-model="row.repeat"
                            class="brow-repeat-count"
                            type="number"
                            min="0"
                            max="20"
                            inputmode="numeric"
                            aria-label="Repeat count"
                        >
                    </span>
                </template>
                <span class="brow-actions">
                    <button type="button" class="brow-remove" :aria-label="row.kind === 'group' ? 'Remove group' : 'Remove row'" @click="remove(index)">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 7h16M10 11v6M14 11v6M6 7l1 13h10l1-13M9 7V4h6v3" /></svg>
                    </button>
                </span>
                <span v-if="row.kind === 'field' && index !== rows.length - 1" class="json-punct brow-comma">,</span>
            </div>

            <template v-if="row.kind === 'group'">
                <BuilderRows :rows="row.rows" :depth="depth + 1" :indent-base="indentBase" :indent-step="indentStep" />
                <div class="brow-add" :style="indentFor(depth + 1)">
                    <button type="button" class="dashed-add" @click="row.rows.push(makeField())">+ Add field</button>
                    <button v-if="canNestGroup" type="button" class="dashed-add" @click="row.rows.push(makeGroup())">+ group</button>
                </div>
                <div class="code-line" :style="indentFor(depth)">
                    <span class="json-punct">}</span><span v-if="index !== rows.length - 1" class="json-punct">,</span>
                </div>
            </template>
        </div>
    </template>
</template>

<style>
.brow-wrap {
    border-radius: var(--r-md);
}

.brow-wrap.dragging {
    opacity: 0.55;
}

.brow-wrap.over > .brow {
    box-shadow: inset 0 2px 0 var(--code-border-strong);
}

.brow {
    position: relative;
    display: flex;
    align-items: center;
    flex-wrap: nowrap;
    gap: 6px;
    padding-block: 3px;
    padding-right: 34px;
    width: max-content;
    min-width: 100%;
    min-height: 38px;
    border-radius: var(--r-md);
    transition: background-color 120ms ease;
}

.brow:hover {
    background: rgba(255, 255, 255, 0.025);
}

.brow-grip {
    position: absolute;
    left: 2px;
    width: 18px;
    height: 24px;
    display: grid;
    place-items: center;
    color: var(--code-dim);
    opacity: 0;
    cursor: grab;
    transition: opacity 120ms ease;
}

.brow-grip svg {
    width: 14px;
    height: 14px;
    fill: currentColor;
}

.brow:hover .brow-grip {
    opacity: 0.9;
}

.brow .select {
    background: var(--code-input-bg-2);
    color: var(--code-bool);
    border-color: transparent;
    padding-left: 9px;
}

.brow .select-caret {
    color: var(--code-dim);
}

.brow-kind-group .select {
    color: var(--code-bool);
    font-weight: 700;
}

.brow-type .select,
.brow-gen .select {
    background: var(--code-input-bg);
    color: var(--code-dim);
    font-weight: 500;
}

.brow-gen .select {
    background: #33202b;
    color: var(--code-string);
}

.brow-key,
.brow-value {
    min-height: 28px;
    padding: 4px 10px;
    border-radius: var(--r-sm);
    background: var(--code-input-bg);
    color: var(--code-key);
    font: 500 11.5px var(--font-mono);
    border: 1px solid transparent;
    transition: border-color 120ms ease;
}

.brow-key:focus,
.brow-value:focus,
.brow-repeat-count:focus {
    outline: none;
    border-color: var(--code-border-strong);
}

.brow-key::placeholder,
.brow-value::placeholder {
    color: var(--code-dim);
}

.brow-value {
    color: var(--code-text-strong);
    min-width: 120px;
    flex: 1 1 120px;
    max-width: 320px;
}

.brow-die {
    width: 28px;
    height: 28px;
    border-radius: var(--r-sm);
    display: grid;
    place-items: center;
    color: var(--code-dim);
    background: var(--code-input-bg);
}

.brow-die svg {
    width: 15px;
    height: 15px;
    stroke: currentColor;
    stroke-width: 1.6;
    fill: none;
}

.brow-die svg circle {
    fill: currentColor;
    stroke: none;
}

.brow-die.on {
    color: var(--code-string);
    background: #33202b;
}

.brow-die:disabled {
    opacity: 0.35;
}

.brow-null {
    color: var(--code-null);
    font-style: italic;
    padding: 0 6px;
}

.brow-repeat {
    display: inline-flex;
    align-items: center;
    height: 28px;
    border-radius: var(--r-sm);
    background: var(--code-input-bg);
    color: var(--code-dim);
    font: 500 11px var(--font-mono);
    overflow: hidden;
}

.brow-repeat.on {
    color: var(--code-number);
}

.brow-repeat-toggle {
    height: 28px;
    padding: 0 9px;
    color: inherit;
    font: inherit;
}

.brow-repeat-count {
    width: 4ch;
    height: 28px;
    padding: 0 6px 0 0;
    color: inherit;
    font: inherit;
    border: 1px solid transparent;
    -moz-appearance: textfield;
    appearance: textfield;
}

.brow-repeat-count::-webkit-outer-spin-button,
.brow-repeat-count::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

.brow-actions {
    position: absolute;
    right: 4px;
    top: 50%;
    transform: translateY(-50%);
    display: inline-flex;
    gap: 2px;
    opacity: 0;
    transition: opacity 120ms ease;
}

.brow:hover .brow-actions,
.brow:focus-within .brow-actions {
    opacity: 1;
}

.brow-remove {
    width: 28px;
    height: 28px;
    border-radius: var(--r-sm);
    display: grid;
    place-items: center;
    color: var(--code-dim);
}

.brow-remove svg {
    width: 14px;
    height: 14px;
    stroke: currentColor;
    stroke-width: 1.8;
    stroke-linecap: round;
    stroke-linejoin: round;
    fill: none;
}

.brow-remove:hover {
    color: var(--code-string);
    background: #33202b;
}

.brow-comma {
    margin-left: -2px;
}

.brow-add {
    display: flex;
    gap: 8px;
    padding: 4px 0 6px;
}

.brow-add .dashed-add {
    min-height: 28px;
    font-size: 11px;
    border-color: var(--code-border-strong);
    color: var(--code-dim);
}

.brow-add .dashed-add:hover {
    color: var(--code-text-strong);
}
</style>
