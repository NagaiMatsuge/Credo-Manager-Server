<?php

namespace App\Traits\Tasks;

use App\Models\Task;
use Illuminate\Http\Request;

trait TaskTrait
{
    //* Show list of tasks to User
    public function showToUser(Request $request)
    {
        $userTasks = Task::getUserTasks($request->user()->id);

        $res = [
            'tasks' => [
                'active' => [],
                'inactive' => []
            ]
        ];
        foreach ($userTasks as $task) {
            $task_data = [
                'id' => $task->task_id,
                'project' => [
                    'id' => $task->project_id,
                    'title' => $task->project_title
                ],
                'title' => $task->task_title,
                'time' => $task->time,
                'type' => $task->type,
                'unread_count' => $task->unread_count,
                'deadline' => $task->deadline,
                'time_spent' => (int)$task->time_spent
            ];
            $res['tasks'][$task->active ? 'active' : 'inactive'][] = $task_data;
        }
        $res['active'] = true;
        $res['hide'] = true;

        return $this->successResponse($res);
    }
}
