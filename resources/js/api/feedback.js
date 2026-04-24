import http from './http';

/**
 * POST /api/feedbacks
 *
 * Returns the accepted feedback payload echoed by the backend (with driver
 * and accepted_at metadata). Throws an error with `errors` attached when
 * the server responds with a 422 validation failure.
 *
 * @param {{ name: string, message: string }} payload
 * @returns {Promise<{ name: string, message: string, driver: string, accepted_at: string }>}
 */
export async function createFeedback(payload) {
    try {
        const { data } = await http.post('/feedbacks', payload);
        return data.data;
    } catch (error) {
        if (error.response?.status === 422) {
            const wrapped = new Error('Ошибка валидации.');
            wrapped.errors = error.response.data?.errors ?? {};
            wrapped.status = 422;
            throw wrapped;
        }

        const wrapped = new Error(
            error.response?.data?.message ||
                error.message ||
                'Непредвиденная ошибка сети.'
        );
        wrapped.status = error.response?.status;
        throw wrapped;
    }
}
