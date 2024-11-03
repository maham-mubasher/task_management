<?php


namespace App\Http\Controllers;

use App\Models\Task;
use App\Http\Requests\TaskRequest;
use App\Http\Resources\TaskResource;
use App\Services\TaskService;
use App\Events\TaskCreated;
use App\Events\TaskCompleted;
use App\Events\TaskArchived;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\Attachment;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\AttachmentResource;

class TaskController extends Controller
{
    use AuthorizesRequests;
    protected $taskService;

    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', Task::class);
        $tasks = $this->taskService->getTasks($request->all());
        return TaskResource::collection($tasks);
    }

    public function store(TaskRequest $request)
    {
        $this->authorize('create', Task::class);

        $task = $this->taskService->createTask($request->validated());
        event(new TaskCreated($task));
        return new TaskResource($task);
    }

    public function show(Task $task)
    {
        $this->authorize('view', $task);
        return new TaskResource($task->load(['tags', 'attachments', 'priority']));
    }

    public function update(TaskRequest $request, Task $task)
    {
        $this->authorize('update', $task);
        $updatedTask = $this->taskService->updateTask($task, $request->validated());
        return new TaskResource($updatedTask);
    }

    public function destroy(Task $task)
    {
        $this->authorize('delete', $task);
        $this->taskService->deleteTask($task);
        return response()->json(['message' => 'Task deleted successfully'], 204);
    }

    public function markComplete(Task $task)
    {
        $this->taskService->markAsComplete($task);
        event(new TaskCompleted($task));
        return new TaskResource($task);
    }

    public function markInComplete(Task $task)
    {
        $this->taskService->markAsInComplete($task);
        return new TaskResource($task);
    }

    public function archive(Task $task)
    {
        $this->taskService->archiveTask($task);
        event(new TaskArchived($task));
        return new TaskResource($task);
    }

    public function getArchives(Request $request)
    {
        $tasks = $this->taskService->getArchives($request->all());
        return TaskResource::collection($tasks);
    }

    public function restore(Task $task)
    {
        $this->taskService->restoreTask($task);
        return new TaskResource($task);
    }

    public function uploadAttachments(Request $request, $taskId)
    {
        $task = Auth::user()->tasks()->find($taskId);

        if (!$task) {
            return response()->json(['message' => 'Task not found or access denied.'], 404);
        }

        $request->validate([
            'attachments.*' => 'required|file|mimes:svg,png,jpg,jpeg,mp4,csv,txt,doc,docx|max:10240',
        ]);

        $uploadedAttachments = [];

        foreach ($request->file('attachments') as $file) {
            $path = $file->store('attachments', 'public');

            $fileType = $this->determineFileType($file->getClientMimeType());

            $attachment = $task->attachments()->create([
                'file_path' => $path,
                'file_type' => $fileType,
            ]);

            $uploadedAttachments[] = new AttachmentResource($attachment);
        }

        return response()->json([
            'message' => 'Files uploaded successfully',
            'attachments' => $uploadedAttachments,
        ], 200);
    }

    private function determineFileType($mimeType)
    {
        if (in_array($mimeType, ['image/svg+xml', 'image/png', 'image/jpeg'])) {
            return 'image';
        } elseif ($mimeType === 'video/mp4') {
            return 'video';
        } elseif (in_array($mimeType, ['text/csv', 'text/plain', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'])) {
            return 'document';
        }
        return 'other';
    }


}
