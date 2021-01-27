<?php

// Authentication Routes

use App\Models\Message;
use App\Models\Notification;
use App\Models\Server;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

require_once __DIR__ . "/Auth/auth.php";
require_once __DIR__ . "/MicroApiRoutes/users.php";
require_once __DIR__ . "/MicroApiRoutes/projects.php";
require_once __DIR__ . "/MicroApiRoutes/tasks.php";
require_once __DIR__ . "/MicroApiRoutes/servers.php";
require_once __DIR__ . "/MicroApiRoutes/params.php";
require_once __DIR__ . "/MicroApiRoutes/steps.php";
require_once __DIR__ . "/MicroApiRoutes/payments.php";
require_once __DIR__ . "/MicroApiRoutes/messages.php";
require_once __DIR__ . "/MicroApiRoutes/notifications.php";
require_once __DIR__ . "/MicroApiRoutes/notes.php";
require_once __DIR__ . "/MicroApiRoutes/main.php";


use Illuminate\Http\Request;



/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/* 
        '(select t4.task_id from task_user as t4 where t4.active=1 and t4.user_id=t1.id limit 1) as active_task_id',
        '(select t5.step_id from tasks t5 where t4.id=(select t4.task_id from task_user as t4 where t4.active=1 and t4.user_id=t1.id limit 1))',
        '(select t6.project_id from steps t6 where t6.id=(select t5.step_id from tasks t5 where t4.id=(select t4.task_id from task_user as t4 where t4.active=1 and t4.user_id=t1.id limit 1)))',
*/

Route::post('/test', function (Request $request) {
    // $res = DB::table('task_user as t1')
    //     ->leftJoin('tasks as t2', 't2.id', '=', 't1.task_id')
    //     ->leftJoin('steps as t3', 't3.id', '=', 't2.step_id')
    //     ->leftJoin('projects as t4', 't4.id', '=', 't3.project_id')
    //     ->select(
    //         't2.id',
    //         't4.id as project_id',
    //         't4.title as project_title',
    //         't2.title as task_title',
    //         't2.time',
    //         't2.type',
    //         't2.deadline',
    //         DB::raw('(select count(t5.id) from unread_messages as t5 where t5.user_id=t1.user_id) as unread_count'),
    //         DB::raw('(select SUM(TIMESTAMPDIFF(MINUTE, t6.created_at, t6.stopped_at)) from task_watchers as t6 where t6.task_user_id=t1.id) as time_spent'),
    //         DB::raw('(select TIMESTAMPDIFF(MINUTE, (select max(t7.created_at) from task_watchers as t7 where t7.task_user_id=t1.id and t7.stopped_at IS NULL), CURRENT_TIMESTAMP)) as additional_time')
    //     )->get();
    // $user_id = null;
    // $res = DB::table('users as t1')
    //     ->leftJoin('roles as t2', 't2.id', '=', 't1.role_id')
    //     ->select(
    //         't1.id as user_id',
    //         't1.name as user_name',
    //         't1.photo as user_photo',
    //         't1.color as user_color',
    //         't2.name as user_role',
    //         't1.active_task_id',
    //         DB::raw('(1) as worked')
    //     )
    //     ->whereNotIn('t2.name', ['Admin', 'Manager'])
    //     ->when($user_id, function ($query) use ($user_id) {
    //         return $query->where('t1.id', $user_id);
    //     })
    //     ->paginate(4)
    //     ->toArray();
    // $user_id = "1a010639-e3bd-4c49-8672-abc4181f9d4c";
    // $project_id = null;
    // $res = DB::table('task_user as t1')
    //     ->leftJoin('tasks as t2', 't2.id', '=', 't1.task_id')
    //     ->leftJoin('steps as t3', 't3.id', '=', 't2.step_id')
    //     ->leftJoin('projects as t4', 't4.id', '=', 't3.project_id')
    //     ->leftJoin('users as t8', 't8.id', '=', 't1.user_id')
    //     ->select(
    //         't2.id',
    //         't4.id as project_id',
    //         't4.title as project_title',
    //         't2.title as task_title',
    //         't2.time',
    //         't2.type',
    //         't2.deadline',
    //         't8.active_task_id',
    //         DB::raw('(select count(t5.id) from unread_messages as t5 where t5.user_id=?) as unread_count'),
    //         DB::raw('(select SUM(TIMESTAMPDIFF(MINUTE, t6.created_at, t6.stopped_at)) from task_watchers as t6 where t6.task_user_id=t1.id) as time_spent'),
    //         DB::raw('(select TIMESTAMPDIFF(MINUTE, (select max(t7.created_at) from task_watchers as t7 where t7.task_user_id=t1.id and t7.stopped_at IS NULL), CURRENT_TIMESTAMP)) as additional_time')
    //     )
    //     ->setBindings([$user_id])
    //     ->where('t1.user_id', $user_id)
    //     ->when($project_id, function ($q) use ($project_id) {
    //         return $q->where('t4.id', $project_id);
    //     })
    //     ->get();
    // $user_id = "2bdf2f24-82b7-413b-aac7-3a7922a0b741";
    // $res = Message::from('messages as t1')->rightJoin('unread_messages as t2', 't1.id', '=', 't2.message_id')->leftJoin('users as t3', 't1.user_id', '=', 't3.id')->select(
    //     't1.text',
    //     't1.created_at as sent_at',
    //     't3.photo as user_photo',
    //     't3.color as user_color',
    //     't3.name as user_name',
    //     DB::raw('(select t5.title from projects as t5 where t5.id=(select t6.project_id from steps as t6 where t6.id=(select t7.step_id from tasks as t7 where t7.id=t1.task_id))) as project_title'),
    //     DB::raw('(select t4.title from tasks t4 where t4.id=t1.task_id) as task_title')
    // )->where('t2.user_id', $user_id)->with('files')->get();
    $res = $request->only(['server.id', 'server.name']);
    // unset($res['server']['id']);

    return response()->json([$res['server'], is_array($res)]);
});
