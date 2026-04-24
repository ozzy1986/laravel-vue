<script setup>
import { computed } from 'vue';
import { useStore } from 'vuex';

const store = useStore();
const count = computed(() => store.getters['feedback/count']);
</script>

<template>
    <header class="app-header" role="banner">
        <div class="app-header__inner">
            <router-link :to="{ name: 'feedback.create' }" class="brand" aria-label="Главная Feedback SPA">
                <span class="brand__mark" aria-hidden="true">
                    <svg viewBox="0 0 24 24" width="22" height="22">
                        <path
                            d="M4 5.5A2.5 2.5 0 0 1 6.5 3h11A2.5 2.5 0 0 1 20 5.5v8A2.5 2.5 0 0 1 17.5 16H13l-4 4v-4H6.5A2.5 2.5 0 0 1 4 13.5v-8Z"
                            fill="currentColor"
                        />
                    </svg>
                </span>
                <span class="brand__text">Feedback<span>.</span>SPA</span>
            </router-link>

            <nav class="app-nav" aria-label="Основная навигация">
                <router-link :to="{ name: 'feedback.create' }" class="app-nav__link">
                    <span>Новая</span>
                </router-link>
                <router-link :to="{ name: 'feedback.list' }" class="app-nav__link">
                    <span>Отправленные</span>
                    <span class="pill" :data-empty="count === 0">{{ count }}</span>
                </router-link>
            </nav>
        </div>
    </header>
</template>

<style scoped>
.app-header {
    position: sticky;
    top: 0;
    z-index: 30;
    backdrop-filter: saturate(1.2) blur(14px);
    -webkit-backdrop-filter: saturate(1.2) blur(14px);
    background: color-mix(in srgb, var(--surface) 82%, transparent);
    border-bottom: 1px solid var(--border);
}

.app-header__inner {
    max-width: var(--container);
    margin: 0 auto;
    padding: 14px 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
}

.brand {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    font-weight: 700;
    letter-spacing: -0.01em;
    color: var(--text);
    text-decoration: none;
}

.brand__mark {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 34px;
    height: 34px;
    border-radius: 10px;
    background: linear-gradient(135deg, var(--accent) 0%, var(--accent-2) 100%);
    color: #fff;
    box-shadow: 0 6px 18px -8px var(--accent);
}

.brand__text {
    font-size: 1.05rem;
}

.brand__text span {
    color: var(--accent);
}

.app-nav {
    display: inline-flex;
    gap: 4px;
    padding: 4px;
    border-radius: 999px;
    background: var(--surface-2);
    border: 1px solid var(--border);
}

.app-nav__link {
    position: relative;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 14px;
    border-radius: 999px;
    font-weight: 500;
    font-size: 0.92rem;
    color: var(--text-muted);
    text-decoration: none;
    transition: color 0.15s ease, background-color 0.15s ease;
}

.app-nav__link:hover {
    color: var(--text);
}

.app-nav__link.is-active {
    color: var(--text);
    background: var(--surface);
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04), 0 4px 12px -6px rgba(0, 0, 0, 0.12);
}

.pill {
    min-width: 22px;
    height: 22px;
    padding: 0 7px;
    border-radius: 999px;
    background: var(--accent);
    color: #fff;
    font-size: 0.72rem;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    line-height: 1;
}

.pill[data-empty='true'] {
    background: var(--surface-3);
    color: var(--text-muted);
}

@media (max-width: 480px) {
    .app-header__inner {
        padding: 12px 14px;
    }

    .brand__text {
        display: none;
    }

    .app-nav__link span:not(.pill) {
        font-size: 0.88rem;
    }
}
</style>
