<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Services\TaskService;
use App\Http\Resources\TaskResource;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use Illuminate\Http\Request;
use function Composer\Autoload\includeFile;

class TaskController extends Controller
{

    protected $taskService;

    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }

    public function index(Request $request)
    {

        $tasks = $this->taskService->getUserTasks(auth()->user(), $request);

        return TaskResource::collection($tasks);
    }

    public function store(StoreTaskRequest $request)
    {
        $task = $this->taskService->createTask(auth()->user(), $request->validated());

        return new TaskResource($task);
    }

    public function show(Task $task)
    {

        if ($task->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return new TaskResource($task);
    }

    public function update(UpdateTaskRequest $request, Task $task)
    {

        if ($task->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $task = $this->taskService->updateTask($task,$request->validated());

        return new TaskResource($task);
    }


    public function destroy(Task $task)
    {

        if ($task->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $task = $this->taskService->deleteTask($task);

        return response()->json([
            'message'=>'Task deleted'
        ]);
    }
}
