<script setup>
import { computed, ref } from 'vue';
import { FLAKY_DEFAULT_CODES, INSTRUCTIONS, flakyLabel } from '../../lib/builder';
import Popover from '../ui/Popover.vue';

const props = defineProps({
    instructions: { type: Object, required: true },
});

const menuOpen = ref(false);
const addButton = ref(null);

const enabled = computed(() => INSTRUCTIONS.filter((def) => props.instructions[def.key]?.enabled));
const remaining = computed(() => INSTRUCTIONS.filter((def) => !props.instructions[def.key]?.enabled));

const add = (def) => {
    const state = props.instructions[def.key];
    if (state.value === '' || state.value === undefined) state.value = def.defaultValue;
    state.enabled = true;
    menuOpen.value = false;
};

const remove = (def) => {
    props.instructions[def.key].enabled = false;
};

const toggleBoolean = (def) => {
    props.instructions[def.key].value = !props.instructions[def.key].value;
};

const widthFor = (value) => `${Math.max(String(value ?? '').length, 1) + 1.5}ch`;

const setFlakyMode = (def, mode) => {
    const value = props.instructions[def.key].value;
    value.every = mode === 'every' ? (value.every || '3') : '';
};

const flakyPlaceholder = FLAKY_DEFAULT_CODES.join(', ');
</script>

<template>
    <div class="chips">
        <TransitionGroup name="chip" tag="div" class="chips-list">
            <span
                v-for="def in enabled"
                :key="def.key"
                class="chip"
                :data-tone="def.tone"
                :title="def.hint"
            >
                <span class="chip-label">{{ def.key }}</span>
                <input
                    v-if="def.type === 'number'"
                    v-model="instructions[def.key].value"
                    class="chip-input"
                    type="number"
                    inputmode="numeric"
                    :style="{ width: widthFor(instructions[def.key].value) }"
                    :aria-label="`${def.key} value`"
                    @keydown.enter.prevent="$event.target.blur()"
                >
                <template v-else-if="def.type === 'flaky'">
                    <select
                        class="chip-select"
                        :value="Number(instructions[def.key].value.every) >= 1 ? 'every' : 'chance'"
                        aria-label="Flaky mode"
                        @change="setFlakyMode(def, $event.target.value)"
                    >
                        <option value="chance">50%</option>
                        <option value="every">every</option>
                    </select>
                    <input
                        v-if="Number(instructions[def.key].value.every) >= 1"
                        v-model="instructions[def.key].value.every"
                        class="chip-input"
                        type="number"
                        min="1"
                        inputmode="numeric"
                        :style="{ width: widthFor(instructions[def.key].value.every) }"
                        aria-label="Fail every nth call"
                        @keydown.enter.prevent="$event.target.blur()"
                    >
                    <input
                        v-model="instructions[def.key].value.codes"
                        class="chip-input chip-input-text"
                        type="text"
                        :placeholder="flakyPlaceholder"
                        :style="{ width: widthFor(instructions[def.key].value.codes || flakyPlaceholder) }"
                        aria-label="Failure status codes"
                        title="Status codes to fail with, 300–599"
                        spellcheck="false"
                        @keydown.enter.prevent="$event.target.blur()"
                    >
                </template>
                <button
                    v-else
                    type="button"
                    class="chip-toggle"
                    :aria-label="`Toggle ${def.key}`"
                    @click="toggleBoolean(def)"
                >
                    {{ instructions[def.key].value ? 'true' : 'false' }}
                </button>
                <button type="button" class="chip-remove" :aria-label="`Remove ${def.key}`" @click="remove(def)">×</button>
            </span>
        </TransitionGroup>
        <button
            v-if="remaining.length"
            ref="addButton"
            type="button"
            class="chip-add"
            aria-haspopup="menu"
            :aria-expanded="menuOpen ? 'true' : 'false'"
            @click="menuOpen = !menuOpen"
        >
            + add
        </button>
        <Popover :open="menuOpen" :anchor="addButton" :width="300" @close="menuOpen = false">
            <button
                v-for="def in remaining"
                :key="def.key"
                type="button"
                class="popover-menu-item"
                role="menuitem"
                @click="add(def)"
            >
                <span class="mono">{{ def.key }}<span v-if="def.unit" class="text-3"> · {{ def.unit }}</span></span>
                <small>{{ def.hint }}</small>
            </button>
        </Popover>
    </div>
</template>

<style scoped>
.chips {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 8px;
}

.chips-list {
    position: relative;
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 8px;
}

.chips-list:empty {
    display: none;
}

.chip-select {
    appearance: none;
    -webkit-appearance: none;
    padding: 2px 6px;
    border-radius: 6px;
    background: rgba(255, 255, 255, 0.06);
    color: inherit;
    font: inherit;
    cursor: pointer;
}

[data-theme='light'] .chip-select {
    background: rgba(0, 0, 0, 0.06);
}

.chip-input-text {
    min-width: 6ch;
    max-width: 22ch;
}</style>
