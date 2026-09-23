import axios from 'axios';

import './lib/apiClient';

declare global {
    interface Window {
        axios: typeof axios;
    }
}

window.axios = axios;
