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
        $users = DB::table('users as t1')->leftJoin('model_has_roles as t2', 't1.id', '=', 't2.model_uuid')->leftJoin('roles as t3', 't3.id', '=', 't2.role_id')->select(
            't1.name as user_name',
            't1.photo as user_photo',
            't1.color as user_color',
            't3.name as user_role',
            't1.work_start_time',
            't1.work_end_time',
            't1.pause_start_time',
            't1.pause_end_time',
            DB::raw('(select t7.title from projects as t7 where t7.id=(select t6.project_id from steps as t6 where t6.id=(select t5.step_id from tasks as t5 where t5.id=(select t4.task_id from task_user as t4 where t4.active=1 and t4.user_id=t1.id limit 1)))) as project_title'),
            DB::raw('(select SUM(t8.stopped_at - t8.created_at) / 60 FROM task_watchers t8 WHERE t8.stopped_at is not null and t8.user_id=t1.id and t8.task_id=(select t9.task_id from task_user as t9 where t9.active=1 and t9.user_id=t1.id limit 1)) as time_spent'),
            DB::raw('(select t10.time from task_user as t10 where t10.active=1 and t10.user_id=t1.id limit 1) as given_time'),
            DB::raw('(select t11.type from task_user as t11 where t11.active=1 and t11.user_id=t1.id limit 1) as task_type'),
            DB::raw('(select t12.deadline from task_user as t12 where t12.active=1 and t12.user_id=t1.id limit 1) as deadline'),
            DB::raw('(select max(t13.stopped_at) FROM task_watchers t13 WHERE t13.stopped_at is not null and t13.user_id=t1.id) as last_pause'),
        )->whereNotIn('t3.name', ['Admin', 'Manager'])->get();
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
