<script setup>
import { methodTone } from '../../lib/builder';

defineProps({
    method: { type: String, required: true },
});
</script>

<template>
    <span class="method-tag" :data-tone="methodTone(method)">{{ method }}</span>
</template>
