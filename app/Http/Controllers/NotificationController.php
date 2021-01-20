<?php

namespace App\Http\Controllers;

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
        $create = Notification::create($this->makeValidation($request));
        return $this->successResponse($create);
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
            'publish_date' => 'required|date',
        ]);
    }
}
