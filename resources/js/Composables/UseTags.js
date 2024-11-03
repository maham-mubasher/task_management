
import { ref } from 'vue';
import api from '@/services/api';

export function useTags() {
    const allTags = ref([]);
    const newTag = ref('');

    const fetchAllTags = async () => {
        try {
            const response = await api.getAllTags();
            allTags.value = response.data.data;
        } catch (error) {
            console.error("Error fetching all tags:", error);
        }
    };

    const addTag = async (newTask) => {
        if (newTag.value.trim()) {
            const tagName = newTag.value.trim();
            const tag = allTags.value.find(tag => tag.name === tagName);

            if (tag) {
                const isTagIncluded = newTask.tags.some(existingTag => existingTag.id === tag.id);
                if (!isTagIncluded) {
                    newTask.tags.push({ id: tag.id, name: tag.name });
                }
                newTag.value = '';
            } else {
                try {
                    const response = await api.createTag({ name: tagName });
                    const newTagFromDb = response.data.data;
                    allTags.value.push(newTagFromDb);
                    newTask.tags.push(newTagFromDb);
                    newTag.value = '';
                } catch (error) {
                    console.error("Error creating new tag:", error);
                }
            }
        }
    };

    const removeTag = (newTask, index) => {
        newTask.tags.splice(index, 1);
    };

    const toggleTag = (newTask, tag) => {
        const tagIndex = newTask.tags.findIndex(existingTag => existingTag.id === tag.id);
        if (tagIndex === -1) {
            newTask.tags.push({ id: tag.id, name: tag.name });
        } else {
            newTask.tags.splice(tagIndex, 1);
        }
    };

    const isTagSelected = (newTask, tagId) => {
        return newTask.tags.some(tag => tag.id === tagId);
    };

    return {
        allTags,
        newTag,
        fetchAllTags,
        addTag,
        removeTag,
        toggleTag,
        isTagSelected
    };
}
