<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;
use function Composer\Autoload\includeFile;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $query = auth()->user()->tasks();

        // filter status
        if($request->has('status')){
            $query->where('status', $request->status);
        }

        // search by title
        if($request->has('search')){
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        // sorting (safe column only)
        $allowedSorts = ['title', 'created_at', 'updated_at'];
        if($request->has('sort') && in_array($request->sort, $allowedSorts)){
            $query->orderBy($request->sort);
        }
        else {
            $query->latest();
        }

        // pagination
        $paginatedTasks = $query->paginate();

        return response()->json($paginatedTasks);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
        ]);

        $task = Task::create([
            'user_id' => auth()->id(),
            'title' => $request->title,
            'description' => $request->description,
            'due_date' => $request->due_date,
        ]);

        return response()->json($task);
    }

    public function show($id)
    {
        $task = Task::findOrFail($id);

        if ($task->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($task);
    }

    public function update(Request $request, $id)
    {
        $task = Task::findOrFail($id);

        if ($task->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'status' => 'nullable|string'
        ]);

        $task->update($request->all());

        return response()->json($task);
    }


    public function destroy($id)
    {
        $task = Task::findOrFail($id);

        if ($task->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $task->delete();

        return response()->json([
            'message'=>'Task deleted'
        ]);
    }
}
