<?php

namespace App\Traits\Tasks;

use App\Models\Task;
use Illuminate\Http\Request;

trait TaskTrait
{
    //* Show list of tasks to User
    public function showToUser(Request $request)
    {
        $userTasks = Task::getUserTasks($request->user()->id, $request->project_id ?? null);

        $hasPausedTask = false;
        if (count($userTasks) > 0) {
            $hasPausedTask = is_null($userTasks[0]->back_up_active_task_id) ? false : true;
        }
        $res = [
            'tasks' => [
                'user' => [
                    'has_paused_task' => $hasPausedTask
                ],
                'active' => [],
                'inactive' => []
            ]
        ];
        foreach ($userTasks as $task) {
            $task_data = [
                'id' => $task->id,
                'project' => [
                    'id' => $task->project_id,
                    'title' => $task->project_title
                ],
                'title' => $task->task_title,
                'time' => $task->time,
                'type' => $task->type,
                'unread_count' => $task->unread_count,
                'deadline' => $task->deadline,
                'time_spent' => (int)$task->time_spent,
                'last_time' => (int)$task->additional_time,
            ];
            $res['tasks'][($task->active_task_id === $task->id) || ($task->back_up_active_task_id === $task->id) ? 'active' : 'inactive'][] = $task_data;
        }
        $res['active'] = true;
        $res['hide'] = true;

        return $this->successResponse($res);
    }
}
