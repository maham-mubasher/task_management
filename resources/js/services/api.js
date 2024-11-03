import axios from 'axios';

const apiClient = axios.create({
    baseURL: import.meta.env.VITE_APP_API_BASE_URL || '/api',
    withCredentials: true,
    headers: {
        Accept: 'application/json',
        'Content-Type': 'application/json',
    }
});

export default {

    getTasks(params) {
        return apiClient.get('/tasks', { params });
    },
    getPriorities() {
        return apiClient.get('/priorities');
    },
    getArchives() {
        return apiClient.get('/getArchives');
    },
    getTask(id) {
        return apiClient.get(`/tasks/${id}`);
    },
    createTask(data) {
        return apiClient.post('/tasks', data);
    },
    updateTask(id, data) {
        return apiClient.put(`/tasks/${id}`, data);
    },
    deleteTask(id) {
        return apiClient.delete(`/tasks/${id}`);
    },
    completeTask(id) {
        return apiClient.put(`/tasks/${id}/complete`);
    },
    incompleteTask(id) {
        return apiClient.put(`/tasks/${id}/incomplete`);
    },
    archiveTask(id) {
        return apiClient.put(`/tasks/${id}/archive`);
    },
    restoreTask(id) {
        return apiClient.put(`/tasks/${id}/restore`);
    },
    getAllTags() {
        return apiClient.get('/tags');
    },
    createTag(data) {
        return apiClient.post('/tags', data);
    },


};
