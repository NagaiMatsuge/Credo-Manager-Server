<?php

namespace App\Http\Controllers;

use App\Models\DbAccess;
use App\Models\FtpAccess;
use App\Models\Project;
use App\Models\Server;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    //**Testing Models */
    public function task()
    {
        $test = User::find(1);
        return response()->json($test, 200);
    }
}
