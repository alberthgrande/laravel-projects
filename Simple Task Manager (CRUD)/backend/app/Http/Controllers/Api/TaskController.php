<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Services\TaskService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    protected $taskService;

    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
        $this->middleware('auth:sanctum');
    }

    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 10);
        $search = $request->query('search');

        $tasks = $this->taskService->listTask(Auth::user(), $perPage, $search);

        return response()->json([
            'message' => 'Tasks retrieved successfully!',
            'data' => TaskResource::collection($tasks),
            'meta' => [
                'current_page' => $tasks->currentPage(),
                'last_page' => $tasks->lastPage(),
                'per_page' => $tasks->perPage(),
                'total' => $tasks->total(),
            ],
        ]);
    }

    public function store(StoreTaskRequest $request)
    {
        try {
            $task = $this->taskService->createTask(Auth::user(), $request->validated());

            return response()->json([
                'message' => 'Task created successfully!',
                'data' => new TaskResource($task)
            ], 201);

        } catch (\Exception $e) {
            Log::error('Task creation failed: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to create task'], 500);
        }
    }

    public function show($id)
    {
        $task = $this->taskService->getTask(Auth::user(), $id);

        if (!$task) {
            return response()->json(['message' => 'Task not found'], 404);
        }
        
        $this->authorize('view', $task);

        return response()->json([
            'message' => 'Task retrieved successfully!',
            'data' => new TaskResource($task)
        ]);
    }

    public function update(UpdateTaskRequest $request, $id)
    {
        $task = $this->taskService->getTask(Auth::user(), $id);

        if (!$task) {
            return response()->json(['message' => 'Task not found'], 404);
        }

        $this->authorize('update', $task);

        try {
            $updated = $this->taskService->updateTask($task, $request->validated());

            return response()->json([
                'message' => 'Task updated successfully!',
                'data' => new TaskResource($updated)
            ]);

        } catch (\Exception $e) {
            Log::error('Task update failed: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to update task'], 500);
        }
    }

    public function destroy($id)
    {
        $task = $this->taskService->getTask(Auth::user(), $id);

        if (!$task) {
            return response()->json(['message' => 'Task not found'], 404);
        }

        $this->authorize('delete', $task);

        try {
            $this->taskService->deleteTask($task);

            return response()->json([
                'message' => 'Task deleted successfully!'
            ]);

        } catch (\Exception $e) {
            Log::error('Task deletion failed: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to delete task'], 500);
        }
    }
}