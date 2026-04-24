<script setup>
// Static 404 view, no logic required.
</script>

<template>
    <section class="not-found">
        <div class="card not-found__card">
            <p class="eyebrow">404</p>
            <h1 class="not-found__title">Page not found</h1>
            <p class="not-found__text">
                The page you were looking for doesn't exist. Use the navigation above to
                get back on track.
            </p>
            <router-link :to="{ name: 'feedback.create' }" class="btn btn--primary">
                Back to form
            </router-link>
        </div>
    </section>
</template>

<style scoped>
.not-found {
    display: grid;
    place-items: center;
    padding-top: 20px;
}

.not-found__card {
    max-width: 520px;
    display: grid;
    gap: 10px;
    justify-items: start;
    padding: 32px;
}

.eyebrow {
    text-transform: uppercase;
    letter-spacing: 0.12em;
    font-size: 0.74rem;
    font-weight: 600;
    color: var(--accent);
    margin: 0;
}

.not-found__title {
    margin: 0;
    font-size: 1.6rem;
    letter-spacing: -0.01em;
}

.not-found__text {
    margin: 0 0 6px;
    color: var(--text-muted);
    line-height: 1.55;
}
</style>
