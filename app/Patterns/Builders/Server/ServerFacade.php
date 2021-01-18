<?php

namespace App\Patterns\Builders\Server;

use Illuminate\Support\Facades\Facade;

class ServerFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return "server";
    }
}
