<script setup>
import { computed } from 'vue';
import { useStore } from 'vuex';

const store = useStore();
const items = computed(() => store.getters['feedback/all']);

function clearAll() {
    if (window.confirm('Clear all feedback from the local store?')) {
        store.dispatch('feedback/clear');
    }
}

const dateFormatter = new Intl.DateTimeFormat(undefined, {
    dateStyle: 'medium',
    timeStyle: 'short',
});

function formatDate(iso) {
    try {
        return dateFormatter.format(new Date(iso));
    } catch {
        return iso;
    }
}

function initials(name) {
    return name
        .split(/\s+/)
        .filter(Boolean)
        .slice(0, 2)
        .map((part) => part.charAt(0).toUpperCase())
        .join('');
}
</script>

<template>
    <section class="page">
        <header class="page__hero">
            <p class="eyebrow">Step 2 · List</p>
            <h1 class="page__title">Submitted feedback</h1>
            <p class="page__lead">
                This page renders everything stored in the Vuex feedback module. It does
                <strong>not</strong> query the backend — refreshing the browser will reset
                the list, as required by the task.
            </p>
        </header>

        <div v-if="items.length === 0" class="empty card">
            <div class="empty__art" aria-hidden="true">
                <svg viewBox="0 0 48 48" width="56" height="56">
                    <path
                        d="M10 10a4 4 0 0 1 4-4h20a4 4 0 0 1 4 4v18a4 4 0 0 1-4 4h-9l-7 6v-6h-4a4 4 0 0 1-4-4V10Z"
                        fill="currentColor"
                        opacity=".2"
                    />
                    <path
                        d="M16 16h16M16 22h10"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                    />
                </svg>
            </div>
            <h2 class="empty__title">Nothing here yet</h2>
            <p class="empty__text">
                Submit your first message on the
                <router-link :to="{ name: 'feedback.create' }">New feedback</router-link>
                page — it will appear here instantly.
            </p>
        </div>

        <div v-else class="list">
            <div class="list__toolbar">
                <p class="list__count">
                    <strong>{{ items.length }}</strong>
                    {{ items.length === 1 ? 'entry' : 'entries' }}
                </p>
                <button class="btn btn--ghost" type="button" @click="clearAll">
                    Clear list
                </button>
            </div>

            <TransitionGroup name="list" tag="ul" class="list__items">
                <li v-for="item in items" :key="item.id" class="card card--entry">
                    <div class="avatar" aria-hidden="true">{{ initials(item.name) }}</div>
                    <div class="entry__body">
                        <header class="entry__head">
                            <h3 class="entry__name">{{ item.name }}</h3>
                            <time class="entry__time" :datetime="item.createdAt">
                                {{ formatDate(item.createdAt) }}
                            </time>
                        </header>
                        <p class="entry__message">{{ item.message }}</p>
                        <footer class="entry__meta">
                            <span class="tag">
                                Saved via
                                <strong>{{ item.driver }}</strong>
                            </span>
                        </footer>
                    </div>
                </li>
            </TransitionGroup>
        </div>
    </section>
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

.empty {
    display: grid;
    gap: 10px;
    place-items: center;
    text-align: center;
    padding: 48px 24px;
    color: var(--text-muted);
}

.empty__art {
    color: var(--accent);
}

.empty__title {
    margin: 4px 0 0;
    color: var(--text);
    font-size: 1.1rem;
}

.empty__text a {
    color: var(--accent);
    text-decoration: none;
    font-weight: 600;
}

.empty__text a:hover {
    text-decoration: underline;
}

.list {
    display: grid;
    gap: 14px;
}

.list__toolbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
}

.list__count {
    margin: 0;
    color: var(--text-muted);
    font-size: 0.92rem;
}

.list__count strong {
    color: var(--text);
    font-weight: 700;
}

.list__items {
    list-style: none;
    padding: 0;
    margin: 0;
    display: grid;
    gap: 12px;
}

.card--entry {
    display: grid;
    grid-template-columns: 48px 1fr;
    gap: 14px;
    align-items: start;
    padding: 18px;
}

.avatar {
    width: 44px;
    height: 44px;
    border-radius: 14px;
    display: grid;
    place-items: center;
    background: linear-gradient(135deg, var(--accent) 0%, var(--accent-2) 100%);
    color: #fff;
    font-weight: 700;
    font-size: 0.95rem;
    letter-spacing: 0.02em;
    box-shadow: 0 8px 20px -10px var(--accent);
}

.entry__body {
    min-width: 0;
}

.entry__head {
    display: flex;
    align-items: baseline;
    justify-content: space-between;
    gap: 12px;
    flex-wrap: wrap;
}

.entry__name {
    margin: 0;
    font-size: 1rem;
    line-height: 1.2;
}

.entry__time {
    color: var(--text-muted);
    font-size: 0.82rem;
    font-variant-numeric: tabular-nums;
    white-space: nowrap;
}

.entry__message {
    margin: 8px 0 10px;
    color: var(--text);
    line-height: 1.55;
    white-space: pre-wrap;
    word-break: break-word;
}

.entry__meta {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.tag {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 10px;
    border-radius: 999px;
    background: var(--surface-2);
    border: 1px solid var(--border);
    font-size: 0.78rem;
    color: var(--text-muted);
}

.tag strong {
    color: var(--text);
    text-transform: uppercase;
    letter-spacing: 0.04em;
    font-weight: 600;
    font-size: 0.72rem;
}

.list-enter-from {
    opacity: 0;
    transform: translateY(-6px) scale(0.98);
}

.list-enter-active,
.list-leave-active {
    transition: opacity 0.25s ease, transform 0.25s ease;
}

.list-leave-to {
    opacity: 0;
    transform: translateY(4px);
}

@media (max-width: 520px) {
    .card--entry {
        grid-template-columns: 40px 1fr;
        gap: 12px;
        padding: 16px;
    }

    .avatar {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        font-size: 0.85rem;
    }
}
</style>
