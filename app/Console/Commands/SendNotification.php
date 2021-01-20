<?php

namespace App\Console\Commands;

use App\Events\NotificationSent;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SendNotification extends Command
{
    /**
     * The name and signature of the command.
     *
     * @var string
     */
    protected $signature = 'SendNofication {--i|notification_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sending notifications at planned date';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // $notification = DB::table('notifications as t1')->leftJoin("users as t2", "t2.")->where('id', $this->option('notification_id'));
        // broadcast(new NotificationSent());
        return 0;
    }
}
