<template>
    <div>
        <h2>Task List</h2>
        <button @click="createTask">Add Task</button>
        <ul>
            <li v-for="task in tasks" :key="task.id">
                <strong>{{ task.title }}</strong> - {{ task.description }}
                <button @click="toggleComplete(task)">Toggle Complete</button>
                <button @click="archiveTask(task)">Archive</button>
            </li>
        </ul>
    </div>
</template>

<script>
import api from '../../services/api';

export default {
    data() {
        return {
            tasks: []
        };
    },
    async created() {
        await this.fetchTasks();
    },
    methods: {
        async fetchTasks() {
            const response = await api.getTasks();
            this.tasks = response.data.data;
        },
        async toggleComplete(task) {
            await api.toggleComplete(task.id);
            await this.fetchTasks();
        },
        async archiveTask(task) {
            await api.archiveTask(task.id);
            await this.fetchTasks();
        },
        createTask() {
            this.$router.push({ name: 'TaskEditPage' });
        }
    }
};
</script>
