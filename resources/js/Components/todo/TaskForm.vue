<template>
    <form @submit.prevent="submitForm">
        <label>Title</label>
        <input v-model="formData.title" type="text" required />

        <label>Description</label>
        <textarea v-model="formData.description" required></textarea>

        <label>Due Date</label>
        <input v-model="formData.due_date" type="date" />

        <button type="submit">{{ isEdit ? 'Update' : 'Create' }} Task</button>
    </form>
</template>

<script>
import api from '../../services/api';

export default {
    props: {
        task: { type: Object, default: null }
    },
    data() {
        return {
            formData: {
                title: '',
                description: '',
                due_date: ''
            }
        };
    },
    computed: {
        isEdit() {
            return !!this.task;
        }
    },
    created() {
        if (this.isEdit) {
            this.formData = { ...this.task };
        }
    },
    methods: {
        async submitForm() {
            if (this.isEdit) {
                await api.updateTask(this.task.id, this.formData);
            } else {
                await api.createTask(this.formData);
            }
            this.$emit('task-saved');
        }
    }
};
</script>
