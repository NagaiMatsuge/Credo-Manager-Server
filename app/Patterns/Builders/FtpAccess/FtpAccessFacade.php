<?php

namespace App\Patterns\Builders\FtpAccess;

use Illuminate\Support\Facades\Facade;

class FtpAccessFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return "ftpAccess";
    }
}
