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
    $result = 'job 43 at Wed Jan 20 17:22:00 2021';
    $word = 'job';
    $end_word = 'at';
    //Position of the job number in at
    $pos = strpos($result, $word) + strlen($word) + 1;
    $pos_end = strpos($result, $end_word) - 1;
    $res = substr($result, $pos, $pos_end - $pos);

    return response()->json([
        'pos' => $pos,
        'pos_end' => $pos_end,
        'res' => $res
    ]);
});
