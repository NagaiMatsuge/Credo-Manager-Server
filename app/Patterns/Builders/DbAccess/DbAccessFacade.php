<?php

namespace App\Patterns\Builders\DbAccess;

use Illuminate\Support\Facades\Facade;

class DbAccessFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return "dbAccess";
    }
}
