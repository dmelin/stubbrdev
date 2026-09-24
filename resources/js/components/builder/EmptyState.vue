<script setup>
import { RECIPES } from '../../lib/builder';
import RecipeCard from '../ui/RecipeCard.vue';

const emit = defineEmits(['pick', 'blank']);
</script>

<template>
    <section class="empty">
        <span class="empty-mark" aria-hidden="true">{&gt;}</span>
        <h2>Nothing stubbed yet</h2>
        <p class="empty-copy">Grab a recipe to get a working endpoint and a token in one click. Everything is editable afterwards.</p>
        <div class="empty-recipes">
            <RecipeCard
                v-for="(recipe, index) in RECIPES"
                :key="recipe.id"
                :recipe="recipe"
                :raised="index === 1"
                @pick="emit('pick', recipe)"
            />
        </div>
        <p class="empty-alt">or <button type="button" class="empty-link" @click="emit('blank')">start from an empty body</button></p>
    </section>
</template>

<style scoped>
.empty {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    padding: 64px 28px;
    overflow: auto;
}

.empty-mark {
    width: 46px;
    height: 46px;
    border-radius: 14px;
    background: var(--accent);
    color: var(--on-accent);
    display: grid;
    place-items: center;
    font: 700 18px var(--font-mono);
}

.empty h2 {
    margin-top: 20px;
    font-size: 36px;
    line-height: 1.15;
}

.empty-copy {
    margin-top: 12px;
    max-width: 46ch;
    font-size: 16px;
    line-height: 1.6;
    color: var(--text-2);
}

.empty-recipes {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 16px;
    width: min(860px, 100%);
    margin-top: 34px;
    text-align: left;
}

.empty-alt {
    margin-top: 28px;
    font: 500 13.5px var(--font-ui);
    color: var(--text-3);
}

.empty-link {
    color: var(--accent-text);
    font: inherit;
    font-weight: 700;
    min-height: 36px;
}

.empty-link:hover {
    text-decoration: underline;
    text-underline-offset: 3px;
}

@media (max-width: 860px) {
    .empty-recipes {
        grid-template-columns: 1fr;
    }

    .recipe-card.raised {
        transform: none;
    }

    .empty h2 {
        font-size: 30px;
    }
}
</style>
