<?php

namespace App\Services;

use App\Models\Task;
use App\Models\Tag;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class TaskService
{
    protected const DEFAULT_SORT_FIELD = 'created_at';
    protected const DEFAULT_SORT_DIRECTION = 'asc';
    protected const PAGINATION_LIMIT = 10;

    /**
     * Retrieve tasks with filtering and sorting options.
     *
     * @param array $filters
     * @return LengthAwarePaginator
     */
    public function getTasks(array $filters): LengthAwarePaginator
{
    $query = Task::with(['priority', 'tags'])
                 ->where('user_id', Auth::id())
                 ->whereNull('archived_at');

    if (isset($filters['status'])) {
        if ($filters['status'] === 'completed') {
            $query->whereNotNull('completed_at');
        } elseif ($filters['status'] === 'pending') {
            $query->whereNull('completed_at');
        }
    }

    if (isset($filters['priority_id'])) {
        $query->where('priority_id', $filters['priority_id']);
    }

    if (isset($filters['due_date_from']) && isset($filters['due_date_to'])) {
        $query->whereBetween('due_date', [$filters['due_date_from'], $filters['due_date_to']]);
    } elseif (isset($filters['due_date_from'])) {
        $query->where('due_date', '>=', $filters['due_date_from']);
    } elseif (isset($filters['due_date_to'])) {
        $query->where('due_date', '<=', $filters['due_date_to']);
    }

    if (isset($filters['archived_date_from']) && isset($filters['archived_date_to'])) {
        $query->whereBetween('archived_at', [$filters['archived_date_from'], $filters['archived_date_to']]);
    } elseif (isset($filters['archived_date_from'])) {
        $query->where('archived_at', '>=', $filters['archived_date_from']);
    } elseif (isset($filters['archived_date_to'])) {
        $query->where('archived_at', '<=', $filters['archived_date_to']);
    }

    if (isset($filters['title'])) {
        $query->where(function($query) use ($filters) {
            $query->where('title', 'like', '%' . $filters['title'] . '%');
        });
    }

    $sortBy = $filters['sortBy'] ?? self::DEFAULT_SORT_FIELD;
    $sortOrder = strtolower($filters['sortOrder'] ?? self::DEFAULT_SORT_DIRECTION);

    $allowedSortFields = ['title', 'description', 'due_date', 'created_at', 'completed_at', 'priority'];
    $allowedSortOrder = ['asc', 'desc'];

    if (in_array($sortBy, $allowedSortFields) && in_array($sortOrder, $allowedSortOrder)) {
        $query->orderBy($sortBy === 'priority' ? 'priority_id' : $sortBy, $sortOrder);
    } else {
        $query->orderBy(self::DEFAULT_SORT_FIELD, self::DEFAULT_SORT_DIRECTION);
    }

    $page = $filters['page'] ?? 1;
    $perPage = self::PAGINATION_LIMIT;

    return $query->paginate($perPage, ['*'], 'page', $page);
}


    public function getArchives(array $filters): LengthAwarePaginator
    {
        $query = Task::with('priority')
                ->where('user_id', Auth::id())
                ->whereNotNull('archived_at')
                ->whereNull('deleted_at');

        return $query->paginate(self::PAGINATION_LIMIT);
    }

    /**
     * Create a new task for the authenticated user.
     *
     * @param array $data
     * @return Task
     */
    public function createTask(array $data): Task
{

    return DB::transaction(function () use ($data) {
        $task = Auth::user()->tasks()->create($data);

        if (isset($data['tags']) && is_array($data['tags'])) {
            $task->tags()->sync($data['tags']);
        }


        if (isset($data['attachments']) && is_array($data['attachments'])) {
            foreach ($data['attachments'] as $file) {
                $filePath = $file->store('attachments', 'public');

                $fileType = $this->getFileType($file->getClientOriginalExtension());

                $task->attachments()->create([
                    'file_path' => $filePath,
                    'file_type' => $fileType,
                ]);
            }
        }

        return $task;
    });
}

// Helper function to determine the file type
private function getFileType(string $extension): string
{
    $imageExtensions = ['svg', 'png', 'jpg', 'jpeg'];
    $videoExtensions = ['mp4'];
    $documentExtensions = ['csv', 'txt', 'doc', 'docx'];

    if (in_array($extension, $imageExtensions)) {
        return 'image';
    } elseif (in_array($extension, $videoExtensions)) {
        return 'video';
    } elseif (in_array($extension, $documentExtensions)) {
        return 'document';
    }

    return 'other';
}
    /**
     * Update an existing task.
     *
     * @param Task $task
     * @param array $data
     * @return Task
     */
    public function updateTask(Task $task, array $data): Task
    {
        DB::transaction(function () use ($task, $data) {
            $task->update($data);
        });

        return $task->refresh();
    }

    /**
     * Delete a task.
     *
     * @param Task $task
     * @return void
     */
    public function deleteTask(Task $task): void
    {
        DB::transaction(function () use ($task) {
            $task->delete();
        });
    }

    /**
     * Mark a task as completed by setting the completed_at timestamp.
     *
     * @param Task $task
     * @return Task
     */
    public function markAsComplete(Task $task): Task
    {
        DB::transaction(function () use ($task) {
            $task->update(['completed_at' => now()]);
        });

        return $task->refresh();
    }

    public function markAsInComplete(Task $task): Task
    {
        DB::transaction(function () use ($task) {
            $task->update(['completed_at' => null]);
        });

        return $task->refresh();
    }

    /**
     * Archive a task by setting the archived_at timestamp.
     *
     * @param Task $task
     * @return Task
     */
    public function archiveTask(Task $task): Task
    {
        DB::transaction(function () use ($task) {
            $task->update(['archived_at' => now()]);
        });

        return $task->refresh();
    }

    public function restoreTask(Task $task): Task
    {
        DB::transaction(function () use ($task) {
            $task->update(['archived_at' => null]);
        });

        return $task->refresh();
    }

}
