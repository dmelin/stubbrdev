import { onUnmounted, ref } from 'vue';
import { BuilderError, expectedDelay } from './builder';
import { sendEndpoint } from './request';

// Per-builder send state: result, inline error, and the delay countdown.
export function useSend() {
    const sending = ref(false);
    const result = ref(null);
    const error = ref('');
    const countdown = ref(null);
    let timer = null;

    const stopCountdown = () => {
        if (timer) window.clearInterval(timer);
        timer = null;
        countdown.value = null;
    };

    const startCountdown = (plannedMs) => {
        stopCountdown();
        if (!plannedMs || plannedMs < 400) return;
        const startedAt = performance.now();
        countdown.value = plannedMs;
        timer = window.setInterval(() => {
            const remaining = plannedMs - (performance.now() - startedAt);
            countdown.value = remaining > 0 ? remaining : null;
        }, 100);
    };

    const send = async (endpoint) => {
        if (sending.value) return;
        error.value = '';
        sending.value = true;
        startCountdown(expectedDelay(endpoint));
        try {
            result.value = await sendEndpoint(endpoint);
        } catch (caught) {
            result.value = null;
            error.value = caught instanceof BuilderError
                ? caught.message
                : `Request failed: ${caught.message || caught}`;
        } finally {
            stopCountdown();
            sending.value = false;
        }
    };

    const reset = () => {
        result.value = null;
        error.value = '';
    };

    onUnmounted(stopCountdown);

    return { sending, result, error, countdown, send, reset };
}
