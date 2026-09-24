<script setup>
import { onMounted, ref, watch } from 'vue';
import BodyEditor from '../components/builder/BodyEditor.vue';
import BuilderSidebar from '../components/builder/BuilderSidebar.vue';
import CodeExamples from '../components/builder/CodeExamples.vue';
import EmptyState from '../components/builder/EmptyState.vue';
import InstructionChips from '../components/builder/InstructionChips.vue';
import RequestLine from '../components/builder/RequestLine.vue';
import ResponsePanel from '../components/builder/ResponsePanel.vue';
import SendButton from '../components/builder/SendButton.vue';
import TopBar from '../components/builder/TopBar.vue';
import { decodeShare, encodeShare, makeEndpoint, makeField } from '../lib/builder';
import { replaceHash, useRoute } from '../lib/router';
import { copyText, ensureToken } from '../lib/token';
import { useSend } from '../lib/useSend';
import { activeEndpoint, addEndpoint, workspace } from '../lib/workspace';

const { hash } = useRoute();
const { sending, result, error, countdown, send, reset } = useSend();
const shareLabel = ref('Share');
let shareTimer = null;

const importShareFromHash = () => {
    if (!hash.value.startsWith('#s=')) return;
    const shared = decodeShare(hash.value.slice(3));
    replaceHash('');
    if (shared) {
        shared.name = shared.name || 'Shared';
        addEndpoint(shared);
    }
};

onMounted(() => {
    importShareFromHash();
    ensureToken().catch(() => {});
});

watch(hash, importShareFromHash);
watch(() => workspace.activeId, reset);

const onSend = () => {
    if (!activeEndpoint.value) return;
    send(activeEndpoint.value);
};

const newEndpoint = () => {
    addEndpoint(makeEndpoint({
        path: `endpoint-${workspace.endpoints.length + 1}`,
        rows: [
            makeField({ key: 'id', type: 'number', random: true, placeholder: '?counter' }),
            makeField({ key: 'name', random: true, placeholder: '?name' }),
        ],
    }));
};

const blankEndpoint = () => {
    addEndpoint(makeEndpoint({ path: 'demo', rows: [] }));
};

const pickRecipe = (recipe) => {
    addEndpoint(recipe.build());
    ensureToken().catch(() => {});
};

const share = async () => {
    if (!activeEndpoint.value) return;
    const url = `${window.location.origin}/builder#s=${encodeShare(activeEndpoint.value)}`;
    if (!(await copyText(url))) return;
    shareLabel.value = 'link copied';
    if (shareTimer) window.clearTimeout(shareTimer);
    shareTimer = window.setTimeout(() => {
        shareLabel.value = 'Share';
    }, 1200);
};
</script>

<template>
    <div class="builder-page">
        <TopBar :share-label="shareLabel" :can-share="Boolean(activeEndpoint)" @share="share" />

        <EmptyState v-if="!workspace.endpoints.length" @pick="pickRecipe" @blank="blankEndpoint" />

        <div v-else class="builder-grid">
            <BuilderSidebar class="area-side" @new="newEndpoint" />

            <section v-if="activeEndpoint" :key="activeEndpoint.id" class="area-editor editor" aria-label="Endpoint editor">
                <RequestLine :endpoint="activeEndpoint" />
                <InstructionChips :instructions="activeEndpoint.instructions" />
                <BodyEditor :endpoint="activeEndpoint" grow />
                <SendButton :sending="sending" :countdown="countdown" @click="onSend" />
            </section>

            <section class="area-response response-col" aria-label="Response">
                <ResponsePanel :result="result" :sending="sending" :countdown="countdown" :error="error" grow />
                <CodeExamples v-if="activeEndpoint" :endpoint="activeEndpoint" class="examples" />
            </section>
        </div>
    </div>
</template>

<style scoped>
.builder-page {
    height: 100dvh;
    display: flex;
    flex-direction: column;
}

.builder-grid {
    flex: 1;
    min-height: 0;
    display: grid;
    grid-template-columns: 200px minmax(0, 1fr) minmax(0, 1fr);
    grid-template-areas: 'side editor response';
}

.area-side {
    grid-area: side;
}

.area-editor {
    grid-area: editor;
}

.area-response {
    grid-area: response;
}

.editor {
    display: flex;
    flex-direction: column;
    gap: 12px;
    padding: 18px;
    min-width: 0;
    min-height: 0;
    border-right: 1px solid var(--border);
    overflow: auto;
}

.response-col {
    display: flex;
    flex-direction: column;
    gap: 14px;
    padding: 18px;
    min-width: 0;
    min-height: 0;
    overflow: auto;
}

.examples {
    flex: none;
}

.examples :deep(.code-well-body) {
    max-height: 200px;
}

@media (max-width: 1100px) {
    .builder-page {
        height: auto;
        min-height: 100dvh;
    }

    .builder-grid {
        grid-template-columns: 200px minmax(0, 1fr);
        grid-template-areas:
            'side editor'
            'side response';
        grid-template-rows: auto auto;
    }

    .editor {
        border-right: 0;
        border-bottom: 1px solid var(--border);
        overflow: visible;
    }

    .response-col {
        overflow: visible;
    }

    .response-col :deep(.code-well-body) {
        max-height: 60dvh;
    }
}

@media (max-width: 760px) {
    .builder-grid {
        grid-template-columns: 1fr;
        grid-template-areas:
            'side'
            'editor'
            'response';
        grid-template-rows: auto auto auto;
    }

    .editor,
    .response-col {
        padding: 14px;
    }
}
</style>
