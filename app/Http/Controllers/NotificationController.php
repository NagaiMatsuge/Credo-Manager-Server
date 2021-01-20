<?php

namespace App\Http\Controllers;

use App\Helpers\At;
use App\Helpers\Logger;
use App\Models\Notification;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    use ResponseTrait;

    //* Get all notifications with pagination
    public function index(Request $request)
    {
        return $this->successResponse(Notification::paginate(10));
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
            $command = "php " . public_path() . "/artisan send:nofication $create->id";
            At::newAtCommand($command, $create->publish_date);
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
        return $this->successResponse(DB::table('notifications')->where('id', $id)->delete());
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
