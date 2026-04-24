<script setup>
import AppHeader from '@/components/AppHeader.vue';
</script>

<template>
    <div class="app">
        <AppHeader />
        <main class="main" role="main">
            <div class="main__inner">
                <router-view v-slot="{ Component }">
                    <Transition name="fade" mode="out-in">
                        <component :is="Component" />
                    </Transition>
                </router-view>
            </div>
        </main>
        <footer class="app-footer">
            <div class="app-footer__inner">
                <span>Built with Laravel, Vue 3, Vuex &amp; Vue Router</span>
                <a
                    href="https://github.com/ozzy1986/laravel-vue"
                    target="_blank"
                    rel="noopener"
                >
                    source
                </a>
            </div>
        </footer>
    </div>
</template>

<style scoped>
.app {
    min-height: 100dvh;
    display: grid;
    grid-template-rows: auto 1fr auto;
}

.main {
    padding: 32px 20px 48px;
}

.main__inner {
    max-width: var(--container);
    margin: 0 auto;
    width: 100%;
}

.app-footer {
    border-top: 1px solid var(--border);
    background: var(--surface);
}

.app-footer__inner {
    max-width: var(--container);
    margin: 0 auto;
    padding: 18px 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    color: var(--text-muted);
    font-size: 0.86rem;
}

.app-footer a {
    color: var(--text-muted);
    text-decoration: none;
    border-bottom: 1px dashed currentColor;
}

.app-footer a:hover {
    color: var(--accent);
    border-bottom-color: var(--accent);
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
    transform: translateY(6px);
}

.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.2s ease, transform 0.2s ease;
}

@media (max-width: 640px) {
    .main {
        padding: 24px 14px 36px;
    }

    .app-footer__inner {
        padding: 14px;
        flex-direction: column;
        align-items: flex-start;
    }
}
</style>
