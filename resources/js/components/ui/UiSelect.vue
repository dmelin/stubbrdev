<script setup>
import { computed } from 'vue';

const props = defineProps({
    modelValue: { type: [String, Number], default: '' },
    options: { type: Array, required: true },
    label: { type: String, required: true },
    tone: { type: String, default: '' },
    size: { type: String, default: 'md' },
    disabled: { type: Boolean, default: false },
});

const emit = defineEmits(['update:modelValue', 'change']);

const normalized = computed(() => props.options.map((option) => (
    typeof option === 'object' && option !== null
        ? option
        : { value: option, label: String(option) }
)));

const onChange = (event) => {
    emit('update:modelValue', event.target.value);
    emit('change', event.target.value);
};
</script>

<template>
    <span class="select-wrap" :class="{ 'select-wrap-sm': size === 'sm' }">
        <select
            class="select"
            :class="{ 'select-sm': size === 'sm' }"
            :data-tone="tone || null"
            :value="modelValue"
            :aria-label="label"
            :disabled="disabled"
            @change="onChange"
        >
            <option v-for="option in normalized" :key="option.value" :value="option.value">
                {{ option.label }}
            </option>
        </select>
        <span class="select-caret" aria-hidden="true">▾</span>
    </span>
</template>
