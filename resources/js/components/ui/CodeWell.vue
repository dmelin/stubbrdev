<script setup>
defineProps({
    title: { type: String, default: '' },
    grow: { type: Boolean, default: false },
});
</script>

<template>
    <div class="code-well" :class="{ 'code-well-grow': grow }">
        <div v-if="title || $slots.header" class="code-well-header">
            <span v-if="title">{{ title }}</span>
            <slot name="header" />
        </div>
        <div class="code-well-body"><slot /></div>
    </div>
</template>

<style>
.code-well-grow {
    flex: 1;
}
</style>
