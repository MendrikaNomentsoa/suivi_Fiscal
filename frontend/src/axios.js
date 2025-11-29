import axios from 'axios';

// Configuration de base
axios.defaults.baseURL = 'http://localhost:8000/api';
axios.defaults.headers.common['Content-Type'] = 'application/json';
axios.defaults.headers.common['Accept'] = 'application/json';

// Intercepteur pour les erreurs
axios.interceptors.response.use(
  response => response,
  error => {
    console.error('❌ Erreur API:', error.response?.data || error.message);
    return Promise.reject(error);
  }
);

export default axios;