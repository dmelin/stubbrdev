<script setup>
import { ref } from 'vue';
import { claimOwnToken, shortToken, token, tokenError, tokenKind, tokenLoading } from '../../lib/token';
import CopyButton from '../ui/CopyButton.vue';
import Popover from '../ui/Popover.vue';

const open = ref(false);
const button = ref(null);
const email = ref('');

const claim = async () => {
    const value = email.value.trim();
    if (!value) return;
    try {
        await claimOwnToken(value);
        email.value = '';
    } catch (_error) {
        // tokenError is shown inline
    }
};
</script>

<template>
    <button
        ref="button"
        type="button"
        class="token-pill"
        :aria-expanded="open ? 'true' : 'false'"
        aria-haspopup="dialog"
        title="Your API token"
        @click="open = !open"
    >
        <span class="token-pill-label">token</span>
        <span class="token-pill-value">{{ tokenLoading && !token ? 'loading…' : (shortToken || 'none yet') }}</span>
    </button>
    <Popover :open="open" :anchor="button" :width="340" @close="open = false">
        <div class="token-pop">
            <div class="kicker">{{ tokenKind === 'own' ? 'Your token' : 'Shared demo token' }}</div>
            <code class="token-full">{{ token || '—' }}</code>
            <div class="token-actions">
                <CopyButton v-if="token" :text="token" label="Copy token" variant="secondary" size="sm" />
                <span v-if="tokenKind === 'demo'" class="text-3 token-note">Shared with everyone trying the demo.</span>
            </div>
            <form class="token-form" @submit.prevent="claim">
                <label class="kicker" for="own-token-email">Use your own</label>
                <div class="token-form-row">
                    <label class="field token-email">
                        <input id="own-token-email" v-model="email" type="email" placeholder="you@example.com" required autocomplete="email">
                    </label>
                    <button type="submit" class="btn btn-primary btn-sm" :disabled="tokenLoading">Get token</button>
                </div>
                <p class="text-3 token-note">One token per email. Asking again returns the same one. Idle tokens are removed after 30 days.</p>
                <p v-if="tokenError" class="error-text" role="alert">{{ tokenError }}</p>
            </form>
        </div>
    </Popover>
</template>

<style scoped>
.token-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    min-height: 32px;
    max-width: 240px;
    padding: 6px 12px;
    border-radius: var(--r-pill);
    background: var(--neutral-tint);
    border: 1px solid var(--border);
    font: 500 11.5px var(--font-mono);
    color: var(--ok-text);
}

.token-pill:hover {
    border-color: var(--border-strong);
}

.token-pill-label {
    color: var(--text-3);
}

.token-pill-value {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.token-pop {
    display: flex;
    flex-direction: column;
    gap: 10px;
    padding: 6px;
}

.token-full {
    display: block;
    padding: 10px 12px;
    border-radius: var(--r-sm);
    background: var(--surface-2);
    font: 500 12px/1.5 var(--font-mono);
    color: var(--text);
    word-break: break-all;
}

.token-actions {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
}

.token-form {
    display: flex;
    flex-direction: column;
    gap: 8px;
    padding-top: 8px;
    border-top: 1px solid var(--border);
}

.token-form-row {
    display: flex;
    gap: 8px;
}

.token-email {
    flex: 1;
    min-width: 0;
    min-height: 34px;
    padding: 6px 10px;
}

.token-note {
    font-size: 11.5px;
    line-height: 1.45;
}
</style>
