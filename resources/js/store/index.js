import { createStore } from 'vuex';
import feedback from './modules/feedback';

export default createStore({
    strict: import.meta.env.DEV,
    modules: {
        feedback,
    },
});
