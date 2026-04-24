import { createFeedback } from '@/api/feedback';

let localCounter = 0;
const STORAGE_KEY = 'feedback.items.v1';

function loadInitialItems() {
    if (typeof window === 'undefined') {
        return [];
    }

    try {
        const raw = window.sessionStorage.getItem(STORAGE_KEY);
        if (!raw) {
            return [];
        }

        const parsed = JSON.parse(raw);
        return Array.isArray(parsed) ? parsed : [];
    } catch {
        return [];
    }
}

function persistItems(items) {
    if (typeof window === 'undefined') {
        return;
    }

    try {
        window.sessionStorage.setItem(STORAGE_KEY, JSON.stringify(items));
    } catch {
        // Ignore storage quota/privacy mode errors.
    }
}

function makeClientId() {
    localCounter += 1;
    return `fb-${Date.now()}-${localCounter}`;
}

const state = () => ({
    items: loadInitialItems(),
    submitting: false,
    lastError: null,
});

const getters = {
    all: (state) => state.items,
    count: (state) => state.items.length,
    submitting: (state) => state.submitting,
    lastError: (state) => state.lastError,
};

const mutations = {
    ADD(state, feedback) {
        state.items.unshift(feedback);
        persistItems(state.items);
    },
    RESET(state) {
        state.items = [];
        persistItems(state.items);
    },
    SET_SUBMITTING(state, value) {
        state.submitting = Boolean(value);
    },
    SET_LAST_ERROR(state, error) {
        state.lastError = error;
    },
};

const actions = {
    /**
     * Sends the payload to the backend and, if accepted, stores the
     * feedback in Vuex. Per task spec, the List page reads exclusively
     * from the store, so we keep the store as the single source of truth.
     */
    async submit({ commit }, payload) {
        commit('SET_SUBMITTING', true);
        commit('SET_LAST_ERROR', null);

        try {
            const accepted = await createFeedback(payload);

            const feedback = {
                id: makeClientId(),
                name: accepted.name,
                message: accepted.message,
                driver: accepted.driver,
                createdAt: accepted.accepted_at,
            };

            commit('ADD', feedback);

            return feedback;
        } catch (error) {
            commit('SET_LAST_ERROR', {
                message: error.message,
                status: error.status ?? null,
                errors: error.errors ?? null,
            });
            throw error;
        } finally {
            commit('SET_SUBMITTING', false);
        }
    },

    clear({ commit }) {
        commit('RESET');
    },
};

export default {
    namespaced: true,
    state,
    getters,
    mutations,
    actions,
};
