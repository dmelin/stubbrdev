<script setup>
// The landing hero's builder. Real requests, demo token, own local state.
import { onMounted, reactive } from 'vue';

import { formatMs, heroEndpoint } from '../../lib/builder';
import { ensureToken, tokenLoading } from '../../lib/token';
import { useSend } from '../../lib/useSend';
import StatusBadge from '../ui/StatusBadge.vue';
import BodyEditor from './BodyEditor.vue';
import InstructionChips from './InstructionChips.vue';
import RequestLine from './RequestLine.vue';
import ResponsePanel from './ResponsePanel.vue';
import SendButton from './SendButton.vue';

const endpoint = reactive(heroEndpoint());
const prefix = `${window.location.host}/api/`;
const { sending, result, error, countdown, send } = useSend();

onMounted(() => {
    ensureToken().catch(() => {});
});

defineExpose({ endpoint });
</script>

<template>
    <div class="live">
        <span class="live-sticker" aria-hidden="true">try it, it's live</span>
        <div class="live-card">
            <RequestLine :endpoint="endpoint" :prefix="prefix" />
            <InstructionChips :instructions="endpoint.instructions" />
            <BodyEditor :endpoint="endpoint" :error="error" />
            <div class="live-foot">
                <SendButton class="live-send" :sending="sending" :countdown="countdown" @click="send(endpoint)" />
                <StatusBadge :status="result?.status" />
                <span class="live-elapsed">
                    <template v-if="result">{{ formatMs(result.ms) }}</template>
                    <template v-else-if="tokenLoading">getting a token…</template>
                    <template v-else>—</template>
                </span>
            </div>
            <ResponsePanel v-if="result" class="live-response" :result="result" :sending="sending" :countdown="countdown" :grow="false" />
        </div>
    </div>
</template>

<style scoped>
.live {
    position: relative;
}

.live-sticker {
    position: absolute;
    top: -14px;
    right: 14px;
    z-index: 1;
    padding: 6px 12px;
    border-radius: var(--r-pill);
    background: var(--accent);
    color: var(--on-accent);
    font: 700 11px var(--font-mono);
    transform: rotate(4deg);
}

.live-card {
    display: flex;
    flex-direction: column;
    gap: 12px;
    padding: 18px;
    border-radius: var(--r-xl);
    background: var(--surface);
    border: 1px solid var(--border);
    box-shadow: var(--shadow-float);
}

.live-foot {
    display: flex;
    align-items: center;
    gap: 10px;
}

.live-send {
    flex: 1;
}

.live-elapsed {
    font: 500 11.5px var(--font-mono);
    color: var(--text-2);
    min-width: 44px;
}

.live-response :deep(.code-well-body) {
    max-height: 240px;
}
</style>
