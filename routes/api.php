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

Route::get('/test', function (Request $request) {
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
    return response()->json($unreadMessages);
})->middleware('auth:api');
