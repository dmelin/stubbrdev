<script setup>
import { watch } from 'vue';
import BuilderPage from './pages/BuilderPage.vue';
import DocsPage from './pages/DocsPage.vue';
import LandingPage from './pages/LandingPage.vue';
import { useRoute } from './lib/router';
import { initTheme } from './lib/theme';
import { loadWorkspace } from './lib/workspace';

initTheme();
loadWorkspace();

const { page } = useRoute();

const TITLES = {
    landing: 'Stubbr — fake the API, break the API, keep it forever',
    builder: 'Builder · Stubbr',
    docs: 'Docs · Stubbr',
};

watch(page, (current) => {
    document.title = TITLES[current] || TITLES.landing;
}, { immediate: true });
</script>

<template>
    <LandingPage v-if="page === 'landing'" />
    <BuilderPage v-else-if="page === 'builder'" />
    <DocsPage v-else />
</template>
