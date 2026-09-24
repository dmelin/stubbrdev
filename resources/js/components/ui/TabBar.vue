<script setup>
defineProps({
    tabs: { type: Array, required: true },
    modelValue: { type: String, required: true },
    label: { type: String, default: 'Tabs' },
});

const emit = defineEmits(['update:modelValue']);
</script>

<template>
    <div class="tabbar" role="tablist" :aria-label="label">
        <button
            v-for="tab in tabs"
            :key="tab.id"
            type="button"
            role="tab"
            class="tab"
            :aria-selected="modelValue === tab.id ? 'true' : 'false'"
            @click="emit('update:modelValue', tab.id)"
        >
            {{ tab.label }}
        </button>
    </div>
</template>
