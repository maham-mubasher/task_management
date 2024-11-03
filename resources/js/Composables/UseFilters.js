
import { ref, computed } from 'vue';

export function useFilters() {
    const filters = ref({
        sortBy: 'created_at',
        sortOrder: 'asc',
        status: '',
        priority_id: null,
        due_date_from: '',
        due_date_to: '',
        searchTitle: ''
    });

    const applyFilters = (appliedFilters = {}) => {
        filters.value = { ...filters.value, ...appliedFilters };
    };

    const clearFilters = () => {
        filters.value = {
            sortBy: 'created_at',
            sortOrder: 'asc',
            status: '',
            priority_id: null,
            due_date_from: '',
            due_date_to: '',
            searchTitle: ''
        };
    };

    return {
        filters,
        applyFilters,
        clearFilters
    };
}
