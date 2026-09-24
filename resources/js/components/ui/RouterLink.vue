<script setup>
import { onLinkClick } from '../../lib/router';

const props = defineProps({
    to: { type: String, required: true },
});

const onClick = (event) => onLinkClick(event, props.to);
</script>

<template>
    <a :href="to" @click="onClick"><slot /></a>
</template>
