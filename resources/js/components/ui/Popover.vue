<script setup>
// Anchored popover rendered at the body level. Closes on outside click / Esc.
import { nextTick, onBeforeUnmount, ref, watch } from 'vue';

const props = defineProps({
    open: { type: Boolean, default: false },
    anchor: { type: Object, default: null },
    align: { type: String, default: 'start' },
    width: { type: Number, default: 0 },
});

const emit = defineEmits(['close']);

const panel = ref(null);
const style = ref({});

const place = () => {
    const anchorEl = props.anchor?.$el || props.anchor;
    if (!anchorEl || !panel.value) return;
    const rect = anchorEl.getBoundingClientRect();
    const panelRect = panel.value.getBoundingClientRect();
    const margin = 8;
    const width = props.width || panelRect.width;
    let left = props.align === 'end' ? rect.right - width : rect.left;
    left = Math.max(margin, Math.min(window.innerWidth - width - margin, left));
    const spaceBelow = window.innerHeight - rect.bottom - margin;
    const openAbove = spaceBelow < panelRect.height && rect.top > spaceBelow;
    const top = openAbove ? Math.max(margin, rect.top - panelRect.height - 6) : rect.bottom + 6;
    style.value = {
        top: `${top}px`,
        left: `${left}px`,
        width: props.width ? `${props.width}px` : undefined,
        maxHeight: `${Math.max(160, (openAbove ? rect.top : window.innerHeight - rect.bottom) - margin - 12)}px`,
        overflow: 'auto',
    };
};

const onDocumentClick = (event) => {
    const anchorEl = props.anchor?.$el || props.anchor;
    if (panel.value?.contains(event.target) || anchorEl?.contains?.(event.target)) return;
    emit('close');
};

const onKeydown = (event) => {
    if (event.key === 'Escape') emit('close');
};

const bind = () => {
    document.addEventListener('mousedown', onDocumentClick, true);
    document.addEventListener('keydown', onKeydown);
    window.addEventListener('resize', place);
    window.addEventListener('scroll', place, true);
};

const unbind = () => {
    document.removeEventListener('mousedown', onDocumentClick, true);
    document.removeEventListener('keydown', onKeydown);
    window.removeEventListener('resize', place);
    window.removeEventListener('scroll', place, true);
};

watch(() => props.open, async (isOpen) => {
    if (isOpen) {
        await nextTick();
        place();
        bind();
        const first = panel.value?.querySelector('button, input, [tabindex]');
        first?.focus({ preventScroll: true });
    } else {
        unbind();
    }
});

onBeforeUnmount(unbind);
</script>

<template>
    <Teleport to="body">
        <div v-if="open" ref="panel" class="popover" :style="style" role="dialog">
            <slot />
        </div>
    </Teleport>
</template>
