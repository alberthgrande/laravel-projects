<?php

namespace App\Services;

use App\Repositories\TaskRepository;

class TaskService
{
    protected $tasks;

    public function __construct(TaskRepository $tasks)
    {
        $this->tasks = $tasks;
    }

    public function listTask($user, $perPage = 10, $search = null)
    {
        if ($user->isAdmin()) {
            // Admin can see all tasks
            return $search
                ? $this->tasks->searchAllTasks($perPage, $search)
                : $this->tasks->getAllTasks($perPage);
        }

        // Normal user can see only their own tasks
        return $search
            ? $this->tasks->searchUserTasks($user->id, $perPage, $search)
            : $this->tasks->getUserTasks($user->id, $perPage);
    }

    public function getTask($user, $taskId)
    {
        if ($user->isAdmin()) {
            return $this->tasks->findAnyTask($taskId);
        }

        return $this->tasks->findById($user->id, $taskId);
    }

    public function createTask($user, array $data)
    {
        if (empty($data['due_date'])) {
            $data['due_date'] = now()->addWeek();
        }

        return $this->tasks->create($user->id, $data);
    }

    public function updateTask($task, array $data)
    {
        return $this->tasks->update($task, $data);
    }

    public function deleteTask($task)
    {
        return $this->tasks->delete($task);
    }
}