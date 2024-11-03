<template>
    <div v-if="task">
        <h2>{{ task.title }}</h2>
        <p>{{ task.description }}</p>
        <p>Due Date: {{ task.due_date }}</p>
        <button @click="editTask">Edit</button>
        <button @click="deleteTask">Delete</button>
    </div>
</template>

<script>
import api from '../../services/api';

export default {
    props: ['id'],
    data() {
        return {
            task: null
        };
    },
    async created() {
        try {
            const response = await api.getTask(this.id);
            this.task = response.data.data;
        } catch (error) {
            console.error("Error fetching task:", error);
        }
    },
    methods: {
        editTask() {
            this.$router.push({ name: 'TaskEditPage', params: { id: this.task.id } });
        },
        async deleteTask() {
            try {
                await api.deleteTask(this.task.id);
                this.$router.push({ name: 'TaskPage' });
            } catch (error) {
                console.error("Error deleting task:", error);
            }
        }
    }
};
</script>
