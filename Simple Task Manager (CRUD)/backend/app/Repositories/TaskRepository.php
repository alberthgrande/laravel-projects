<?php

namespace App\Repositories;

use App\Models\Task;

class TaskRepository
{
    public function getUserTasks($userId, $paginate = 10)
    {
        return Task::where('user_id', $userId)->latest()->paginate($paginate);
    }

    public function getAllTasks($paginate = 10)
    {
        return Task::latest()->paginate($paginate);
    }

    public function findById($userId, $taskId)
    {
        return Task::where('user_id', $userId)->find($taskId);
    }

    public function findAnyTask($taskId)
    {
        return Task::find($taskId);
    }

    public function create($userId, array $data)
    {
        $data['user_id'] = $userId;
        return Task::create($data);
    }

    public function update(Task $task, array $data)
    {
        $task->update($data);
        return $task;
    }

    public function delete(Task $task)
    {
        return $task->delete();
    }

    public function searchUserTasks($userId, $perPage = 10, $search = null)
    {
        $query = Task::where('user_id', $userId)->latest();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        return $query->paginate($perPage);
    }

    public function searchAllTasks($perPage = 10, $search = null)
    {
        $query = Task::latest();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        return $query->paginate($perPage);
    }
}