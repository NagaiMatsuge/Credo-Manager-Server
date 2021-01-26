<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\ResponseTrait;
use App\Traits\Tasks\TaskTrait;

class MainController extends Controller
{
    use ResponseTrait, TaskTrait;

    //* Show users to admin main page
    private function showUsersToAdmin(Request $request)
    {
        $users = DB::table('users as t1')
            ->leftJoin('roles as t2', 't2.id', '=', 't1.role_id')
            ->leftJoin('tasks as t3', 't3.id', '=', 't1.active_task_id')
            ->leftJoin('steps as t4', 't4.id', '=', 't3.step_id')
            ->leftJoin('projects as t5', 't5.id', '=', 't4.project_id')
            ->select(
                't1.name as user_name',
                't1.photo as user_photo',
                't1.color as user_color',
                't2.name as user_role',
                't1.work_start_time',
                't1.work_end_time',
                't1.pause_start_time',
                't1.pause_end_time',
                't5.title as project_title',
                't3.time as given_time',
                't3.type as task_type',
                't3.deadline as deadline',
                DB::raw('(select max(t6.stopped_at) FROM task_watchers t6 WHERE t6.stopped_at is not null and t6.task_user_id=t1.active_task_id) as last_pause'),
                DB::raw('(select SUM(TIMESTAMPDIFF(MINUTE, t8.created_at, t8.stopped_at)) from task_watchers as t8 where t8.task_user_id=t1.active_task_id) as time_spent'),
                DB::raw('(select TIMESTAMPDIFF(MINUTE, (select max(t10.created_at) from task_watchers as t10 where t10.task_user_id=t1.active_task_id and t10.stopped_at IS NULL), CURRENT_TIMESTAMP)) as additional_time')
            )->whereNotIn('t2.name', ['Admin', 'Manager'])->get();
        return $this->successResponse($users);
    }

    //* Show Projects only to admin or managers
    private function showProjectsToAdmin(Request $request)
    {
        $projects = DB::table('projects')->where('archived', false)->get();
        $users = DB::table('task_user as t1')->leftJoin('users as t4', 't4.id', '=', 't1.user_id')->select('t1.task_id', 't1.user_id', DB::raw('(SELECT t2.project_id FROM steps t2 WHERE t2.id=(SELECT t3.step_id FROM tasks t3 WHERE t3.id=t1.task_id)) AS project_iid'), 't4.photo', 't4.color')->get();
        $res = [];
        $count = 0;
        foreach ($projects as $project) {
            $projectAdd = [
                'title' => $project->title,
                'created_at' => $project->created_at,
                'deadline' => $project->deadline,
                'photo' => $project->photo,
                'participants' => []
            ];
            foreach ($users as $user) {
                if ($user->project_iid == $project->id && !in_array($user->user_id, array_column($projectAdd['participants'], 'user_id')))
                    $projectAdd['participants'][] = $user;
            }
            $res[$count] = $projectAdd;
            $count++;
        }
        return $this->successResponse($res);
    }

    //* Show unread messages for users
    private function showUnreadMessages(Request $request)
    {
        $user = $request->user();
        $unreadMessages = Message::from('messages as t1')->rightJoin('unread_messages as t2', 't1.id', '=', 't2.message_id')->leftJoin('users as t3', 't1.user_id', '=', 't3.id')->select(
            't1.text',
            't1.created_at as sent_at',
            't3.photo as user_photo',
            't3.color as user_color',
            't3.name as user_name',
            DB::raw('(select t5.title from projects as t5 where t5.id=(select t6.project_id from steps as t6 where t6.id=(select t7.step_id from tasks as t7 where t7.id=t1.task_id))) as project_title'),
            DB::raw('(select t4.title from tasks t4 where t4.id=t1.task_id) as task_title')
        )->where('t2.user_id', $user->id)->with('files')->get();
        return $this->successResponse($unreadMessages);
    }

    //* Middle section of the main page
    public function mid(Request $request)
    {
        if ($request->user()->hasRole(['Admin', 'Manager'])) {
            return $this->showUsersToAdmin($request);
        } else {
            return $this->showToUser($request);
        }
    }

    //* Last section of the main page
    public function last(Request $request)
    {
        if ($request->user()->hasRole(['Admin', 'Manager'])) {
            return $this->showProjectsToAdmin($request);
        } else {
            return $this->showUnreadMessages($request);
        }
    }
}
