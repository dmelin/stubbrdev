<script setup>
import JsonNode from './JsonNode.vue';

defineProps({
    value: { default: null },
});
</script>

<template>
    <div class="json-view"><JsonNode :value="value" :depth="0" :is-last="true" /></div>
</template>
