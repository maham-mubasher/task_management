<template>
    <div class="p-4">
        <h5 class="text-xl font-semibold mb-4">{{ title }}</h5>

        <PrimaryButton @click="openNewTaskModal" class="mb-4 mr-4">
            + New Task
        </PrimaryButton>

        <SecondaryButton @click="openArchivedTasksModal">
                Archived Tasks
        </SecondaryButton>

        <DialogModal :show="showNewTaskModal" @close="closeNewTaskModal">
            <template #title>
                New Task
            </template>

            <template #content>
                <form @submit.prevent="createTask">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Task Title</label>
                        <input v-model="newTask.title" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required />
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea v-model="newTask.description" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required></textarea>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Priority</label>
                        <select v-model="newTask.priority_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option v-for="priority in priorities" :key="priority.id" :value="priority.id">
                                {{ priority.name }}
                            </option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Due Date</label>
                        <input v-model="newTask.due_date" type="date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" />
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Add New Tag</label>
                        <div class="flex items-center space-x-2">
                            <input v-model="newTag" type="text" placeholder="Add tag" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" />
                            <SecondaryButton @click="addTag">Add</SecondaryButton>
                        </div>
                        <div class="flex flex-wrap mt-2 space-x-2">
                            <span v-for="(tag, index) in newTask.tags" :key="tag.id" class="inline-flex items-center bg-blue-100 text-blue-700 rounded-full px-3 py-1 text-sm">
                                {{ tag.name }}
                                <button @click="removeTag(index)" class="ml-2 text-red-500">&times;</button>
                            </span>
                        </div>

                        <label class="block text-sm font-medium text-gray-700 mt-4">Or Select Existing Tags</label>
                        <div class="grid grid-cols-4 gap-3">
                            <div v-for="tag in allTags" :key="tag.id" class="flex items-center">
                                <input type="checkbox" :value="tag.id" :checked="isTagSelected(tag.id)"  @change="toggleTag(tag)" class="mr-2">
                                <span>{{ tag.name }}</span>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Attach Files</label>
                        </div>
                    </div>
                </form>
            </template>

            <template #footer>
                <PrimaryButton @click="createTask" :disabled="isSubmitting">Save</PrimaryButton>
                <SecondaryButton @click="closeNewTaskModal">Cancel</SecondaryButton>
            </template>
        </DialogModal>

        <DialogModal :show="showArchivedTasksModal" @close="closeArchivedTasksModal">
            <template #title>
                Archived Tasks
            </template>

            <template #content>
                <div class="overflow-x-auto bg-white rounded-lg shadow-lg">
                    <table class="min-w-full border border-gray-200">
                        <thead class="bg-gray-100 text-gray-700">
                            <tr>
                                <th class="px-4 py-2 text-left">#</th>
                                <th class="px-4 py-2 text-left">Task</th>
                                <th class="px-4 py-2 text-left">Priority</th>
                                <th class="px-4 py-2 text-left">Due Date</th>
                                <th class="px-4 py-2 text-left">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(task, index) in archivedTasks" :key="task.id" class="border-b border-gray-200 hover:bg-gray-50">
                                <td class="px-4 py-3">{{ index + 1 }}</td>
                                <td class="px-4 py-3">{{ task.title }}</td>
                                <td class="px-4 py-3 capitalize">{{ task.priority.name || 'Normal' }}</td>
                                <td class="px-4 py-3">{{ task.due_date }}</td>
                                <td class="px-4 py-3 flex space-x-2">
                                    <button @click="restoreTask(task)" class="text-sm bg-blue-500 text-white px-2 py-1 rounded">Restore</button>
                                    <button @click="deleteTask(task.id)" class="text-sm bg-red-500 text-white px-2 py-1 rounded">Delete</button>
                                </td>
                            </tr>

                            <tr v-if="archivedTasks.length === 0">
                                <td colspan="5" class="text-center py-4 text-gray-500">No archived tasks available</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </template>

            <template #footer>
                <SecondaryButton @click="closeArchivedTasksModal">Close</SecondaryButton>
            </template>
        </DialogModal>

        <DialogModal :show="showTaskDetailModal" @close="closeTaskDetailModal">
            <template #title>
                {{ selectedTask?.title || "Task Details" }}
            </template>

            <template #content>
                <div v-if="selectedTask">
                    <p><strong>Description:</strong> {{ selectedTask.description }}</p>
                    <p><strong>Priority:</strong> {{ selectedTask.priority.name }}</p>
                    <p><strong>Due Date:</strong> {{ selectedTask.due_date }}</p>
                    <p><strong>Status:</strong> {{ selectedTask.completed_at ? 'Completed' : 'Pending' }}</p>
                    <p><strong>Tags:</strong>
                        <span v-for="tag in selectedTask.tags" :key="tag.id" class="inline-block bg-blue-100 text-blue-700 rounded-full px-2 py-1 text-xs mr-2">
                            {{ tag.name }}
                        </span>
                    </p>
                </div>
            </template>

            <template #footer>
                <SecondaryButton @click="closeTaskDetailModal">Close</SecondaryButton>
            </template>
        </DialogModal>

        <div class="mb-4 flex items-start justify-between space-x-4">
            <div class="flex items-start space-x-4 w-full">
                <div class="w-1/5">
                    <label for="filters" class="block text-sm font-medium text-gray-700">Filters</label>
                    <select v-model="filters.selectedFilter" @change="resetFilterInputs" id="filters" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option value="status">By Status</option>
                        <option value="priority">By Priority Level</option>
                        <option value="due_date">By Due Date</option>
                        <option value="title">Search Title</option>
                    </select>
                </div>

                <div v-if="filters.selectedFilter === 'status'" class="w-1/5">
                    <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                    <select v-model="filters.status" id="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option value="">Select Status</option>
                        <option value="completed">Completed</option>
                        <option value="pending">Pending</option>
                    </select>
                </div>

                <div v-if="filters.selectedFilter === 'priority'" class="w-1/5">
                    <label for="priority" class="block text-sm font-medium text-gray-700">Priority Level</label>
                    <select v-model="filters.priority" id="priority" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option value="">Select Priority</option>
                        <option value="1">Urgent</option>
                        <option value="2">High</option>
                        <option value="3">Normal</option>
                        <option value="4">Low</option>
                    </select>
                </div>

                <div v-if="filters.selectedFilter === 'due_date'" class="w-1/3 flex space-x-2">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Due Date From</label>
                        <input v-model="filters.dueDateFrom" type="date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Due Date To</label>
                        <input v-model="filters.dueDateTo" type="date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" />
                    </div>
                </div>

                <div v-if="filters.selectedFilter === 'title'" class="w-1/5">
                    <label for="titleSearch" class="block text-sm font-medium text-gray-700">Search Title</label>
                    <input v-model="filters.searchTitle" id="titleSearch" type="text" placeholder="Enter title" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" />
                </div>

                <div class="flex items-end space-x-2 mt-2">
                    <button @click="applyFilters" class="px-3 py-1 text-sm bg-blue-500 text-white rounded">Apply Filters</button>
                    <button @click="clearFilters" class="px-3 py-1 text-sm bg-red-500 text-white rounded">Clear Filters</button>
                </div>
            </div>

            <div class="flex items-center space-x-4 w-1/2 justify-end">
                <div>
                    <label for="sorting" class="block text-sm font-medium text-gray-700">Sort By</label>
                    <select v-model="filters.sortBy" @change="fetchTasks" id="sorting" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option value="created_at">Created Date</option>
                        <option value="completed_at">Completed Date</option>
                        <option value="priority">Priority</option>
                        <option value="due_date">Due Date</option>
                    </select>
                </div>

                <div>
                    <label for="order" class="block text-sm font-medium text-gray-700">Order</label>
                    <select v-model="filters.sortOrder" @change="fetchTasks" id="order" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option value="asc">Ascending</option>
                        <option value="desc">Descending</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto bg-white rounded-lg shadow-lg">
            <table class="min-w-full border border-gray-200">
                <thead class="bg-gray-100 text-gray-700">
                    <tr>
                        <th class="px-4 py-2 text-left">#</th>
                        <th class="px-4 py-2 text-left">Task</th>
                        <th class="px-4 py-2 text-left">Priority</th>
                        <th class="px-4 py-2 text-left">Due Date</th>
                        <th class="px-4 py-2 text-left">Status</th>
                        <th class="px-4 py-2 text-left">Tags</th>
                        <th class="px-4 py-2 text-left">Attachments</th>
                        <th class="px-4 py-2 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(task, index) in tasks" :key="task.id" class="border-b border-gray-200 hover:bg-gray-50">
                        <td class="px-4 py-3">{{ index + 1 }}</td>
                        <td class="px-4 py-3"><button @click="openTaskDetailModal(task)" class="text-blue-500 underline">{{ task.title }}</button></td>
                        <td class="px-4 py-3 capitalize">{{ task.priority.name || 'Normal' }}</td>
                        <td class="px-4 py-3">{{ task.due_date }}</td>
                        <td class="px-4 py-3">
                            <span :class="[ 'px-2 py-1 rounded text-sm font-medium', task.completed_at ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' ]">
                                {{ task.completed_at ? 'Completed' : 'Pending' }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <span v-for="tag in task.tags" :key="tag.id" class="inline-block bg-blue-100 text-blue-700 rounded-full px-2 py-1 text-xs mr-2">
                                {{ tag.name }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <ul>
                                <li v-for="attachment in task.attachments" :key="attachment.id">
                                    <a :href="attachment.file_url" target="_blank" class="text-blue-500 underline">{{ attachment.file_type }}</a>
                                </li>
                            </ul>
                        </td>
                        <td class="px-4 py-3 flex space-x-2">
                            <button @click="completeTask(task)" class="text-sm px-2 py-1 rounded" :class="task.completed_at ? 'bg-yellow-500 text-white' : 'bg-green-500 text-white'">
                                {{ task.completed_at ? 'Incomplete' : 'Complete' }}
                            </button>
                            <button @click="archiveTask(task)" class="text-sm bg-blue-500 text-white px-2 py-1 rounded">Archive</button>
                            <button @click="deleteTask(task.id)" class="text-sm bg-red-500 text-white px-2 py-1 rounded">Delete</button>
                        </td>
                    </tr>

                    <tr v-if="tasks.length === 0">
                        <td colspan="8" class="text-center py-4 text-gray-500">No tasks available</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="mt-4 flex items-center justify-center space-x-2">
            <button v-for="link in pagination.links" :key="link.label" @click="link.url ? fetchTasks(new URL(link.url).searchParams.get('page')) : null" :class="[ 'px-4 py-2 border rounded', { 'bg-blue-500 text-white': link.active, 'bg-gray-200': !link.active }]" :disabled="!link.url">
                <span v-html="link.label"></span>
            </button>
        </div>
    </div>
</template>

<script>
import PrimaryButton from '@/Components/PrimaryButton.vue';
import DialogModal from '@/Components/DialogModal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { ref, onMounted } from 'vue';
import api from '../../services/api';

export default {
    components: {
        PrimaryButton,
        DialogModal,
        SecondaryButton
    },

    setup() {
        const title = ref('All Tasks');
        const tasks = ref([]);
        const pagination = ref({});
        const newTag = ref('');
        const priorities = ref([]);
        const archivedTasks = ref([]);
        const allTags = ref([]);
        const filters = ref({
            sortBy: 'created_at',
            sortOrder: 'asc',
            status: ''
        });

        const showNewTaskModal = ref(false);
        const showTaskDetailModal = ref(false);
        const selectedTask = ref(null);
        const isSubmitting = ref(false);
        const showArchivedTasksModal = ref(false);
        const taskFiles = ref([]);

        const handleFileUpload = (event) => {
            taskFiles.value = Array.from(event.target.files);
        };
        const newTask = ref({
            title: '',
            description: '',
            priority: 'normal',
            due_date: '',
            tags: []
        });

        const fetchTasks = async (page = 1) => {
            try {
                const response = await api.getTasks(filters.value, page);
                tasks.value = response.data.data;
                pagination.value = response.data.meta;
            } catch (error) {
                console.error("Error fetching tasks:", error);
            }
        };
        const changePage = (page) => {
            currentPage.value = page;
            fetchTasks();
        };

        const openTaskDetailModal = (task) => {
            selectedTask.value = task;
            showTaskDetailModal.value = true;
        };

        const closeTaskDetailModal = () => {
            selectedTask.value = null;
            showTaskDetailModal.value = false;
        };

        const fetchAllTags = async () => {
            try {
                const response = await api.getAllTags();
                allTags.value = response.data.data;
            } catch (error) {
                console.error("Error fetching all tags:", error);
            }
        };

        const addTag = async () => {
            if (newTag.value.trim()) {
                const tagName = newTag.value.trim();
                const tag = allTags.value.find(tag => tag.name === tagName);

                if (tag) {
                    const isTagIncluded = newTask.value.tags.some(existingTag => existingTag.id === tag.id);
                    if (!isTagIncluded) {
                        newTask.value.tags.push({ id: tag.id, name: tag.name });
                    }
                    newTag.value = '';
                } else {
                    try {
                        const response = await api.createTag({ name: tagName });
                        const newTagFromDb = response.data;
                        allTags.value.push(newTagFromDb.data);
                        newTask.value.tags.push({ id: newTagFromDb.data.id, name: newTagFromDb.data.name });
                        newTag.value = '';
                    } catch (error) {
                        console.error("Error creating new tag:", error);
                    }
                }
            }
        };

        const removeTag = (index) => {
            newTask.value.tags.splice(index, 1);
        };
        const fetchPriorities = async () => {
            try {
                const response = await api.getPriorities();
                priorities.value = response.data.data;
            } catch (error) {
                console.error("Error fetching priorities:", error);
            }
        };

        const fetchArchivedTasks = async () => {
            try {
                const response = await api.getArchives();
                archivedTasks.value = response.data.data;
            } catch (error) {
                console.error("Error fetching Archives:", error);
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

        const openNewTaskModal = () => {
            newTask.value = {
                title: '',
                description: '',
                priority: 'normal',
                due_date: '',
                tags: []
            };
            showNewTaskModal.value = true;
        };

        const closeNewTaskModal = () => {
            showNewTaskModal.value = false;
        };

        const openArchivedTasksModal = () => {
            showArchivedTasksModal.value = true;
        };

        const closeArchivedTasksModal = () => {
            showArchivedTasksModal.value = false;
        };

        const createTask = async () => {
            if (isSubmitting.value) return;
            isSubmitting.value = true;
            try {
                const tagIds = (newTask.value.tags || []).map(tag => tag.id);
                const payload = new FormData();
                payload.append('title', newTask.value.title);
                payload.append('description', newTask.value.description);
                payload.append('priority_id', newTask.value.priority_id);
                payload.append('due_date', newTask.value.due_date);
                tagIds.forEach((tagId) => payload.append('tags[]', tagId));

                await api.createTask(payload);
                await fetchTasks();
                closeNewTaskModal();
            } catch (error) {
                console.error("Error creating task:", error);
            } finally {
                isSubmitting.value = false;
            }
        };

        const archiveTask = async (task) => {
            try {
                await api.archiveTask(task.id);
                task.archived_at = new Date().toISOString();
                fetchArchivedTasks();
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
                fetchArchivedTasks();
            } catch (error) {
                console.error("Error restoring task:", error);
            }
        };

        const deleteTask = async (taskId) => {
            try {
                await api.deleteTask(taskId);
                fetchTasks();
                fetchArchivedTasks();
            } catch (error) {
                console.error("Error deleting task:", error);
            }
        };

        const applyStatusFilter = (status) => {
            filters.value.status = status;
            fetchTasks();
        };

        const applyPriorityFilter = (priorityId) => {
            filters.value.priority_id = priorityId;
            fetchTasks();
        };

        const applyDueDateFilter = (dueDate) => {
            filters.value.due_date = dueDate;
            fetchTasks();
        };

        const toggleTag = (tag) => {
            const tagIndex = newTask.value.tags.findIndex(existingTag => existingTag.id === tag.id);
            if (tagIndex === -1) {
                newTask.value.tags.push({ id: tag.id, name: tag.name });
            } else {
                newTask.value.tags.splice(tagIndex, 1);
            }
        };

        const isTagSelected = (tagId) => {
            return newTask.value.tags.some(tag => tag.id === tagId);
        }

        const clearFilters = () => {
            filters.value = {
                sortBy: 'created_at',
                sortOrder: 'asc',
                status: '',
                priority_id: null,
                due_date: '',
                searchTitle: ''
            };
            fetchTasks();
        };

        const applyFilters = () => {
            const appliedFilters = {};

            if (filters.value.selectedFilter === 'status' && filters.value.status) {
                appliedFilters.status = filters.value.status === 'completed' ? 'completed' : 'pending';
            }

            if (filters.value.selectedFilter === 'priority' && filters.value.priority) {
                appliedFilters.priority_id = filters.value.priority;
            }

            if (filters.value.selectedFilter === 'due_date' && filters.value.dueDateFrom && filters.value.dueDateTo) {
                appliedFilters.due_date_from = filters.value.dueDateFrom;
                appliedFilters.due_date_to = filters.value.dueDateTo;
            }

            if (filters.value.selectedFilter === 'title' && filters.value.searchTitle) {
                appliedFilters.title = filters.value.searchTitle;
            }

            filters.value = { ...filters.value, ...appliedFilters };
            console.log(filters.value);
            fetchTasks();
        };

        onMounted(() => {
            fetchTasks();
            fetchPriorities();
            fetchArchivedTasks();
            fetchAllTags();
        });

        return {
            title,
            tasks,
            priorities,
            filters,
            archivedTasks,
            showNewTaskModal,
            isSubmitting,
            newTask,
            fetchTasks,
            pagination,
            openNewTaskModal,
            closeNewTaskModal,
            openArchivedTasksModal,
            closeArchivedTasksModal,
            showArchivedTasksModal,
            showTaskDetailModal,
            selectedTask,
            openTaskDetailModal,
            closeTaskDetailModal,
            createTask,
            completeTask,
            archiveTask,
            restoreTask,
            deleteTask,
            allTags,
            newTag,
            addTag,
            removeTag,
            toggleTag,
            isTagSelected,
            handleFileUpload,
            applyStatusFilter,
            applyPriorityFilter,
            applyDueDateFilter,
            applyFilters,
            clearFilters
        };
    }
};
</script>

<style scoped>
</style>
