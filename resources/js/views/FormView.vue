<script setup>
import { computed, reactive, ref } from 'vue';
import { useRouter } from 'vue-router';
import { useStore } from 'vuex';
import AppToast from '@/components/AppToast.vue';

const store = useStore();
const router = useRouter();

const form = reactive({
    name: '',
    message: '',
});

const errors = reactive({
    name: '',
    message: '',
});

const toast = reactive({ message: '', variant: 'success' });
const submitting = computed(() => store.getters['feedback/submitting']);

const nameInput = ref(null);

const rules = {
    name: (value) => {
        const v = value.trim();
        if (!v) return 'Please enter your name.';
        if (v.length < 2) return 'Name should be at least 2 characters.';
        if (v.length > 120) return 'Name should be at most 120 characters.';
        return '';
    },
    message: (value) => {
        const v = value.trim();
        if (!v) return 'Please share your feedback.';
        if (v.length < 2) return 'Feedback should be at least 2 characters.';
        if (v.length > 5000) return 'Feedback should be at most 5000 characters.';
        return '';
    },
};

function validate() {
    errors.name = rules.name(form.name);
    errors.message = rules.message(form.message);
    return !errors.name && !errors.message;
}

function onFieldInput(field) {
    if (errors[field]) {
        errors[field] = rules[field](form[field]);
    }
}

async function handleSubmit() {
    if (!validate()) {
        nameInput.value?.focus();
        return;
    }

    try {
        const feedback = await store.dispatch('feedback/submit', {
            name: form.name.trim(),
            message: form.message.trim(),
        });

        form.name = '';
        form.message = '';
        errors.name = '';
        errors.message = '';

        toast.variant = 'success';
        toast.message = `Thanks, ${feedback.name.split(' ')[0]}! Your feedback was saved.`;

        setTimeout(() => {
            if (toast.message) router.push({ name: 'feedback.list' });
        }, 900);
    } catch (err) {
        if (err.status === 422 && err.errors) {
            errors.name = err.errors.name?.[0] ?? '';
            errors.message = err.errors.message?.[0] ?? '';
            toast.variant = 'error';
            toast.message = 'Please double-check the form.';
        } else {
            toast.variant = 'error';
            toast.message = err.message || 'Something went wrong. Try again.';
        }
    }
}

const messageCount = computed(() => form.message.length);
</script>

<template>
    <section class="page">
        <header class="page__hero">
            <p class="eyebrow">Step 1 · Form</p>
            <h1 class="page__title">Send your feedback</h1>
            <p class="page__lead">
                Fill in the form below. The entry is sent to the backend and then stored in
                the Vuex store, which powers the list on the next page.
            </p>
        </header>

        <form class="card form" novalidate @submit.prevent="handleSubmit">
            <div class="field" :class="{ 'field--error': errors.name }">
                <label class="field__label" for="field-name">Your name</label>
                <input
                    id="field-name"
                    ref="nameInput"
                    v-model="form.name"
                    type="text"
                    class="field__input"
                    autocomplete="name"
                    maxlength="120"
                    :aria-invalid="errors.name ? 'true' : 'false'"
                    :aria-describedby="errors.name ? 'field-name-error' : undefined"
                    placeholder="e.g. Ivan Petrov"
                    @blur="errors.name = rules.name(form.name)"
                    @input="onFieldInput('name')"
                />
                <p v-if="errors.name" id="field-name-error" class="field__error">
                    {{ errors.name }}
                </p>
            </div>

            <div class="field" :class="{ 'field--error': errors.message }">
                <label class="field__label" for="field-message">
                    <span>Feedback</span>
                    <span class="field__counter" :data-limit="messageCount > 5000">
                        {{ messageCount }} / 5000
                    </span>
                </label>
                <textarea
                    id="field-message"
                    v-model="form.message"
                    rows="6"
                    class="field__input field__input--textarea"
                    maxlength="5000"
                    :aria-invalid="errors.message ? 'true' : 'false'"
                    :aria-describedby="errors.message ? 'field-message-error' : undefined"
                    placeholder="Tell us what's on your mind…"
                    @blur="errors.message = rules.message(form.message)"
                    @input="onFieldInput('message')"
                />
                <p v-if="errors.message" id="field-message-error" class="field__error">
                    {{ errors.message }}
                </p>
            </div>

            <div class="form__footer">
                <p class="form__hint">
                    Your data is stored in-memory in the Vuex store. It disappears on page reload —
                    that's part of the spec.
                </p>

                <button type="submit" class="btn btn--primary" :disabled="submitting">
                    <span v-if="!submitting">Send feedback</span>
                    <span v-else class="spinner" aria-hidden="true"></span>
                    <span v-if="submitting" class="sr-only">Sending…</span>
                </button>
            </div>
        </form>
    </section>

    <AppToast
        :message="toast.message"
        :variant="toast.variant"
        @close="toast.message = ''"
    />
</template>

<style scoped>
.page {
    display: grid;
    gap: 28px;
}

.page__hero {
    display: grid;
    gap: 10px;
    max-width: 640px;
}

.eyebrow {
    text-transform: uppercase;
    letter-spacing: 0.12em;
    font-size: 0.74rem;
    font-weight: 600;
    color: var(--accent);
    margin: 0;
}

.page__title {
    font-size: clamp(1.75rem, 3.2vw, 2.25rem);
    line-height: 1.15;
    margin: 0;
    letter-spacing: -0.02em;
}

.page__lead {
    color: var(--text-muted);
    margin: 0;
    max-width: 60ch;
    line-height: 1.55;
}

.form {
    display: grid;
    gap: 22px;
}

.field {
    display: grid;
    gap: 8px;
}

.field__label {
    display: flex;
    align-items: baseline;
    justify-content: space-between;
    font-size: 0.9rem;
    font-weight: 600;
    color: var(--text);
}

.field__counter {
    font-size: 0.78rem;
    font-weight: 500;
    color: var(--text-muted);
}

.field__counter[data-limit='true'] {
    color: var(--danger);
}

.field__input {
    appearance: none;
    width: 100%;
    font: inherit;
    color: var(--text);
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 12px;
    padding: 12px 14px;
    transition: border-color 0.15s ease, box-shadow 0.15s ease, background-color 0.15s ease;
}

.field__input::placeholder {
    color: var(--text-subtle);
}

.field__input:hover {
    border-color: var(--border-strong);
}

.field__input:focus {
    outline: none;
    border-color: var(--accent);
    box-shadow: 0 0 0 4px color-mix(in srgb, var(--accent) 15%, transparent);
    background: var(--surface);
}

.field__input--textarea {
    min-height: 150px;
    resize: vertical;
    line-height: 1.55;
}

.field--error .field__input {
    border-color: var(--danger);
    box-shadow: 0 0 0 4px color-mix(in srgb, var(--danger) 12%, transparent);
}

.field__error {
    margin: 0;
    color: var(--danger);
    font-size: 0.85rem;
}

.form__footer {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    padding-top: 8px;
    border-top: 1px dashed var(--border);
}

.form__hint {
    margin: 0;
    font-size: 0.85rem;
    color: var(--text-muted);
    max-width: 46ch;
}

.spinner {
    width: 18px;
    height: 18px;
    border-radius: 50%;
    border: 2px solid rgba(255, 255, 255, 0.35);
    border-top-color: #fff;
    animation: spin 0.7s linear infinite;
    display: inline-block;
}

@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}

@media (max-width: 640px) {
    .form__footer {
        flex-direction: column;
        align-items: stretch;
    }

    .form__footer .btn {
        width: 100%;
        justify-content: center;
    }
}
</style>
