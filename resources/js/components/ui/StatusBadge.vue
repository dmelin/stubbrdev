<script setup>
import { statusTone } from '../../lib/builder';

defineProps({
    status: { type: [Number, String, null], default: null },
    label: { type: String, default: '' },
});
</script>

<template>
    <span class="status-badge" :data-tone="statusTone(status)" :aria-label="status ? `HTTP ${status}` : 'No response yet'">
        {{ label || status || '—' }}
    </span>
</template>
