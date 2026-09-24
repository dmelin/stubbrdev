<script setup>
import BrandMark from '../ui/BrandMark.vue';
import ThemeToggle from '../ui/ThemeToggle.vue';
import TokenBadge from './TokenBadge.vue';

defineProps({
    shareLabel: { type: String, default: 'Share' },
    canShare: { type: Boolean, default: true },
});

const emit = defineEmits(['share']);
</script>

<template>
    <header class="topbar">
        <BrandMark size="sm" />
        <TokenBadge />
        <nav class="topbar-nav" aria-label="Builder">
            <RouterLink to="/docs" class="topbar-link">Docs</RouterLink>
            <button type="button" class="topbar-link" :disabled="!canShare" @click="emit('share')">{{ shareLabel }}</button>
            <a class="btn btn-accent-soft btn-sm coffee" href="https://buymeacoffee.com/melin" target="_blank" rel="noopener noreferrer">☕ Buy me a coffee</a>
            <ThemeToggle />
        </nav>
    </header>
</template>

<style scoped>
.topbar {
    display: flex;
    align-items: center;
    gap: 14px;
    min-height: 58px;
    padding: 10px 20px;
    border-bottom: 1px solid var(--border);
}

.topbar-nav {
    margin-left: auto;
    display: flex;
    align-items: center;
    gap: 6px;
}

.topbar-link {
    min-height: 36px;
    padding: 6px 10px;
    border-radius: var(--r-sm);
    font: 500 13px var(--font-ui);
    color: var(--text-2);
}

.topbar-link:hover:not(:disabled) {
    color: var(--text);
}

.topbar-link:disabled {
    opacity: 0.5;
}

@media (max-width: 760px) {
    .topbar {
        flex-wrap: wrap;
        gap: 8px 12px;
        padding: 10px 14px;
    }

    .coffee {
        display: none;
    }
}
</style>
