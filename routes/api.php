<?php

// Authentication Routes

use App\Models\Notification;
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

Route::get('/test', function () {
    $notification = DB::table('notification_user as t1')->leftJoin('notifications as t2', 't2.id', '=', 't1.notification_id')->leftJoin('users as t3', 't3.id', '=', 't2.user_id')->where('notification_id', '1')->select('t3.photo', 't3.id as from_user', 't3.color', 't3.email', 't3.name', 't1.to_user', 't2.text', 't2.publish_date')->get();

    return response()->json($notification);
});
