<?php

// Authentication Routes

use App\Events\NewMessage;
use App\Patterns\Builders\DbAccess\DbAccessFacade;
use App\Patterns\Builders\FtpAccess\FtpAccessFacade;
use App\Patterns\Builders\Server\ServerFacade;
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

Route::get('/test', function () {
    $var = DbAccessFacade::setUser("bobur")
        ->setPassword("password")
        ->setHost("localhost")
        ->setDatabaseName("hello_world")->create();
    $server = ServerFacade::setUser("bobur")->setDir("/home/bobur")->create();
    $ftp = FtpAccessFacade::setUser("bobur")->setPassword("password")->create();
    dd($var, $server, $ftp);
});
