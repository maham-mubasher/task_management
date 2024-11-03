<?php

namespace App\Observers;

use App\Models\Task;
use App\Events\TaskCreated;
use App\Events\TaskCompleted;
use App\Events\TaskArchived;

class TaskObserver
{
    /**
     * Handle the Task "created" event.
     */
    public function created(Task $task)
    {
        event(new TaskCreated($task));
    }

    /**
     * Handle the Task "updated" event.
     */
    public function updated(Task $task)
    {
        if ($task->isDirty('completed_at') && $task->completed_at !== null) {
            event(new TaskCompleted($task));
        }

        if ($task->isDirty('archived_at') && $task->archived_at !== null) {
            event(new TaskArchived($task));
        }
    }

    /**
     * Handle the Task "deleted" event.
     */
    public function deleted(Task $task)
    {
        $task->attachments()->delete();
    }

    /**
     * Handle the Task "restored" event.
     */
    public function restored(Task $task)
    {
        $task->archived_at = null;
        $task->save();
    }
    /**
     * Handle the Task "force deleted" event.
     */
    public function forceDeleted(Task $task): void
    {
        //
    }
}
