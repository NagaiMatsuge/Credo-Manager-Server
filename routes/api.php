<?php

// Authentication Routes

use App\Events\NewMessage;
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
