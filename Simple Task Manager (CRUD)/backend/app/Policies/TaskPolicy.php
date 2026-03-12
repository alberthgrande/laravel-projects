<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Task;

class TaskPolicy
{
    public function view(User $user, Task $task)
    {
        // Admin can view any task
        if ($user->isAdmin()) return true;

        // User can view only their own tasks
        return $user->id === $task->user_id;
    }

    public function update(User $user, Task $task)
    {
        // Admin can update any task
        if ($user->isAdmin()) return true;

        // User can update only their own tasks
        return $user->id === $task->user_id;
    }

    public function delete(User $user, Task $task)
    {
        // Admin can delete any task
        if ($user->isAdmin()) return true;

        // User can delete only their own tasks
        return $user->id === $task->user_id;
    }
}