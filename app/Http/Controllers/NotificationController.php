<?php

namespace App\Http\Controllers;

use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    use ResponseTrait;
    
    //* Get all notifications with pagination
    public function index(Request $request)
    {
        return $this->successResponse(DB::table('notifications')->get());  
    }

    //* Get notification by its id
    public function show(Request $request, $id)
    {
        return $this->successResponse(DB::table('notifications')->where('id', $id)->get());
    }

    //* Create notification
    public function store(Request $request)
    {
        $validate = $request->validate([
            'text'=> 'required|string|min:3'
        ]);
        $create = DB::table('notifications')->insert($validate);
        return $this->successResponse($create);
    }

    //* Update notification by its id
    public function update(Request $request, $id)
    {
        $validate = $request->validate([
            'text'=> 'required|string|min:3'
        ]);
        $create = DB::table('notifications')->where('id', $id)->update($validate);
        return $this->successResponse($create);
    }
}
