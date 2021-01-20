<?php

namespace App\Http\Controllers;

use App\Helpers\At;
use App\Models\Notification;
use App\Models\User;
use App\Traits\ResponseTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    use ResponseTrait;

    //* Get all notifications with pagination
    public function index(Request $request)
    {
        return $this->successResponse(Notification::paginate(30));
    }

    //* Get notification by its id
    public function show(Request $request, $id)
    {
        return $this->successResponse(DB::table('notifications')->where('id', $id)->get());
    }

    //* Create notification
    public function store(Request $request)
    {
        $this->makeValidation($request);
        $auth_user_id = $request->user()->id;
        DB::transaction(function () use ($auth_user_id, $request) {
            $create = Notification::create(array_merge(['user_id' => $auth_user_id], $request->input()));
            $userIds = User::get()->pluck('id');
            $notificationUser = [];
            foreach ($userIds as $userId) {
                $notificationUser[] = [
                    'user_id' => $userId,
                    'notification_id' => $create->id
                ];
            }
            DB::table('notification_user')->insert($notificationUser);
            $command = "php " . public_path() . "/artisan send:notification $create->id";
            $res = At::newAtCommand($command, $create->publish_date);
            if ($res['success']) {
                $job_number = $res['job'];
                DB::table('notifications')->where('id', $create->id)->update(['job_number' => $job_number]);
            } else {
                throw new Exception($res['message']);
            }
        });
        return $this->successResponse(true);
    }

    //* Update notification by its id
    public function update(Request $request, $id)
    {
        $create = DB::table('notifications')->where('id', $id)->update($this->makeValidation($request));
        return $this->successResponse($create);
    }

    //* Delete notification by its id
    public function destroy($id)
    {
        DB::transaction(function () use ($id) {
            $notification = DB::table('notifications')->where('id', $id)->first();
            if ($notification->job_number)
                At::deleteAtCommand($notification->job_number);
            DB::table('notifications')->where('id', $id)->delete();
        });
        return $this->successResponse(true);
    }

    //* Validation function
    public function makeValidation(Request $request)
    {
        return $request->validate([
            'text' => 'required|string|min:3',
            'publish_date' => 'required|date|date_format:Y-m-d H:i:s'
        ]);
    }
}
