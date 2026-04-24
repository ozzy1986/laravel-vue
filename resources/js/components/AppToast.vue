<script setup>
import { onMounted, onBeforeUnmount, ref, watch } from 'vue';

const props = defineProps({
    message: { type: String, default: '' },
    variant: { type: String, default: 'success' },
    duration: { type: Number, default: 3500 },
});

const emit = defineEmits(['close']);

const visible = ref(false);
let timer = null;

function startTimer() {
    clearTimeout(timer);
    if (props.duration > 0 && props.message) {
        timer = setTimeout(() => emit('close'), props.duration);
    }
}

watch(
    () => props.message,
    (value) => {
        visible.value = Boolean(value);
        if (value) startTimer();
    },
    { immediate: true }
);

onMounted(startTimer);
onBeforeUnmount(() => clearTimeout(timer));
</script>

<template>
    <Transition name="toast">
        <div
            v-if="visible"
            class="toast"
            :class="`toast--${variant}`"
            role="status"
            aria-live="polite"
        >
            <span class="toast__dot" aria-hidden="true"></span>
            <span class="toast__text">{{ message }}</span>
            <button class="toast__close" aria-label="Close notification" @click="$emit('close')">
                ×
            </button>
        </div>
    </Transition>
</template>

<style scoped>
.toast {
    position: fixed;
    left: 50%;
    bottom: 24px;
    transform: translateX(-50%);
    z-index: 100;
    display: inline-flex;
    align-items: center;
    gap: 12px;
    padding: 10px 14px 10px 16px;
    border-radius: 12px;
    background: var(--surface);
    border: 1px solid var(--border);
    box-shadow: 0 18px 40px -16px rgba(15, 23, 42, 0.35);
    max-width: min(92vw, 480px);
    color: var(--text);
    font-size: 0.94rem;
}

.toast__dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background: var(--accent);
    box-shadow: 0 0 0 4px color-mix(in srgb, var(--accent) 20%, transparent);
}

.toast--error .toast__dot {
    background: var(--danger);
    box-shadow: 0 0 0 4px color-mix(in srgb, var(--danger) 20%, transparent);
}

.toast__text {
    flex: 1 1 auto;
}

.toast__close {
    appearance: none;
    background: transparent;
    border: 0;
    color: var(--text-muted);
    font-size: 1.25rem;
    line-height: 1;
    cursor: pointer;
    padding: 4px 6px;
    border-radius: 6px;
}

.toast__close:hover {
    background: var(--surface-2);
    color: var(--text);
}

.toast-enter-from,
.toast-leave-to {
    opacity: 0;
    transform: translateX(-50%) translateY(12px);
}

.toast-enter-active,
.toast-leave-active {
    transition: opacity 0.22s ease, transform 0.22s ease;
}
</style>
