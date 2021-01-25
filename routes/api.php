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

Route::get('/test', function (Request $request) {
    Server::create(['title' => 'date', 'host' => 'date']);
});
