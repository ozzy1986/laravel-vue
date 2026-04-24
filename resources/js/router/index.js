import { createRouter, createWebHistory } from 'vue-router';

const FormView = () => import('@/views/FormView.vue');
const ListView = () => import('@/views/ListView.vue');
const NotFoundView = () => import('@/views/NotFoundView.vue');

const routes = [
    {
        path: '/',
        redirect: { name: 'feedback.create' },
    },
    {
        path: '/new',
        name: 'feedback.create',
        component: FormView,
        meta: { title: 'Новое обращение', order: 1 },
    },
    {
        path: '/list',
        name: 'feedback.list',
        component: ListView,
        meta: { title: 'Отправленные обращения', order: 2 },
    },
    {
        path: '/:pathMatch(.*)*',
        name: 'not-found',
        component: NotFoundView,
        meta: { title: 'Не найдено' },
    },
];

const router = createRouter({
    history: createWebHistory('/'),
    routes,
    linkActiveClass: 'is-active',
    linkExactActiveClass: 'is-exact-active',
    scrollBehavior() {
        return { top: 0 };
    },
});

router.afterEach((to) => {
    const title = to.meta?.title;
    if (title) {
        document.title = `${title} · Форма обратной связи`;
    }
});

export default router;
