<script setup>
import { methodTone } from '../../lib/builder';

defineProps({
    recipe: { type: Object, required: true },
    raised: { type: Boolean, default: false },
});

const emit = defineEmits(['pick']);
</script>

<template>
    <button type="button" class="recipe-card" :class="{ raised }" @click="emit('pick', recipe)">
        <span class="tag" :data-tone="methodTone(recipe.method)">{{ recipe.method }} /{{ recipe.path }}</span>
        <h4>{{ recipe.title }}</h4>
        <p>{{ recipe.description }}</p>
    </button>
</template>
