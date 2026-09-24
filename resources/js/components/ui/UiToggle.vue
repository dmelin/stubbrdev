<script setup>
defineProps({
    modelValue: { type: Boolean, default: false },
    label: { type: String, required: true },
    showState: { type: Boolean, default: true },
});

const emit = defineEmits(['update:modelValue']);
</script>

<template>
    <button
        type="button"
        role="switch"
        class="toggle"
        :aria-checked="modelValue ? 'true' : 'false'"
        :aria-label="label"
        @click="emit('update:modelValue', !modelValue)"
    >
        <span class="toggle-track"><span class="toggle-knob"></span></span>
        <span v-if="showState">{{ modelValue ? 'on' : 'off' }}</span>
    </button>
</template>
