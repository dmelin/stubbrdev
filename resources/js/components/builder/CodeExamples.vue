<script setup>
import { computed, ref, watch } from 'vue';
import { SNIPPET_LANGS, buildSnippets, endpointUrl, highlightCode, safePayload } from '../../lib/builder';
import { storage } from '../../lib/storage';
import { token } from '../../lib/token';
import CodeWell from '../ui/CodeWell.vue';
import CopyButton from '../ui/CopyButton.vue';
import TabBar from '../ui/TabBar.vue';

const props = defineProps({
    endpoint: { type: Object, required: true },
});

const LANG_KEY = 'stubbr_snippet_lang';
const lang = ref(SNIPPET_LANGS.some((l) => l.id === storage.get(LANG_KEY)) ? storage.get(LANG_KEY) : 'curl');
watch(lang, (value) => storage.set(LANG_KEY, value));

const snippet = computed(() => {
    const { payload, error } = safePayload(props.endpoint);
    if (error) return `// ${error.message}`;
    const url = `${window.location.origin}${endpointUrl(props.endpoint)}`;
    const snippets = buildSnippets(payload, url, token.value || 'YOUR_API_TOKEN', props.endpoint.method);
    return snippets[lang.value] ?? snippets.curl;
});

const html = computed(() => highlightCode(snippet.value));
</script>

<template>
    <CodeWell class="code-examples">
        <template #header>
            <TabBar v-model="lang" :tabs="SNIPPET_LANGS" label="Code example language" />
            <CopyButton class="examples-copy" :text="() => snippet" label="copy" variant="secondary" mono />
        </template>
        <pre class="examples-pre" v-html="html"></pre>
    </CodeWell>
</template>

<style scoped>
.code-examples :deep(.code-well-header) {
    padding: 11px 13px 0;
    justify-content: space-between;
}

.examples-copy {
    margin-left: auto;
    color: var(--code-text-strong);
    border-color: var(--code-border-strong);
}

.examples-pre {
    font-size: 11.5px;
    line-height: 1.7;
}
</style>
