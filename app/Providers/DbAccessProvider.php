<?php

namespace App\Providers;

use App\Patterns\Builders\DbAccess\DbAccess;
use App\Patterns\Builders\FtpAccess\FtpAccess;
use App\Patterns\Builders\Server\Server;
use Illuminate\Support\ServiceProvider;

class DbAccessProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('dbAccess', function () {
            return new DbAccess;
        });

        $this->app->bind('ftpAccess', function () {
            return new FtpAccess;
        });

        $this->app->bind('server', function () {
            return new Server;
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
