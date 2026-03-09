<?php

namespace App\Services;

use App\Models\Task;
use App\Filters\TaskFilter;

class TaskService
{
    public function getUserTasks($user, $request)
    {
        $query = $user->tasks();

        $query = TaskFilter::apply($query, $request);

        return $query->paginate(10);
    }

    public function createTask($user, $data)
    {
        $data['user_id'] = $user->id;

        return Task::create($data);
    }

    public function updateTask($task, $data)
    {
        $task->update($data);

        return $task;
    }

    public function deleteTask($task)
    {
        return $task->delete();
    }
}
