import axios from 'axios';

const apiBase =
    document.querySelector('meta[name="api-base"]')?.getAttribute('content') ||
    '/api';

const csrfToken =
    document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

const http = axios.create({
    baseURL: apiBase,
    headers: {
        Accept: 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        ...(csrfToken ? { 'X-CSRF-TOKEN': csrfToken } : {}),
    },
    timeout: 15000,
});

export default http;
