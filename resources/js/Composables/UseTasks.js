
import { ref } from 'vue';
import api from '@/services/api';

export function useTasks() {
    const tasks = ref([]);
    const pagination = ref({});
    const isSubmitting = ref(false);

    const fetchTasks = async (filters = {}, page = 1) => {
        try {
            const response = await api.getTasks(filters, page);
            tasks.value = response.data.data;
            pagination.value = response.data.meta;
        } catch (error) {
            console.error("Error fetching tasks:", error);
        }
    };

    const createTask = async (newTask, taskFiles) => {
        if (isSubmitting.value) return;
        isSubmitting.value = true;
        try {
            const tagIds = (newTask.tags || []).map(tag => tag.id);
            const payload = new FormData();
            payload.append('title', newTask.title);
            payload.append('description', newTask.description);
            payload.append('priority_id', newTask.priority_id);
            payload.append('due_date', newTask.due_date);
            tagIds.forEach(tagId => payload.append('tags[]', tagId));

            for (const file of taskFiles) {
                payload.append('attachments[]', file);
            }

            await api.createTask(payload);
            await fetchTasks();
        } catch (error) {
            console.error("Error creating task:", error);
        } finally {
            isSubmitting.value = false;
        }
    };

    const completeTask = async (task) => {
        try {
            if (task.completed_at) {
                await api.incompleteTask(task.id, { completed_at: null });
                task.completed_at = null;
            } else {
                await api.completeTask(task.id);
                task.completed_at = new Date().toISOString();
            }
            fetchTasks();
        } catch (error) {
            console.error("Error toggling task completion:", error);
        }
    };

    const archiveTask = async (task) => {
        try {
            await api.archiveTask(task.id);
            task.archived_at = new Date().toISOString();
            fetchTasks();
        } catch (error) {
            console.error("Error archiving task:", error);
        }
    };

    const restoreTask = async (task) => {
        try {
            await api.restoreTask(task.id);
            task.archived_at = null;
            fetchTasks();
        } catch (error) {
            console.error("Error restoring task:", error);
        }
    };

    const deleteTask = async (taskId) => {
        try {
            await api.deleteTask(taskId);
            fetchTasks();
        } catch (error) {
            console.error("Error deleting task:", error);
        }
    };

    return {
        tasks,
        pagination,
        isSubmitting,
        fetchTasks,
        createTask,
        completeTask,
        archiveTask,
        restoreTask,
        deleteTask
    };
}
