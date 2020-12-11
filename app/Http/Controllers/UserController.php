<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    use ResponseTrait;

    public function index(Request $request)
    {
        $user = User::paginate(10);
        return $this->successResponse($user);
    }

    public function show(User $id)
    {
        return $this->successResponse($id);;
    }

    public function store(Request $request)
    {
        $create_user = User::create($request->validate([
            'name' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|string|min:8',
            'pause_start_time' => 'required',
            'pause_end_time' => 'required',
            'working_days' => 'required',
            'developer' => 'required'
        ]));
        return $this->successResponse($create_user);
    }

    public function update(Request $request, User $id)
    {
        $id->update($request->all());
        return $this->successResponse($id);
    }

    public function destroy($user)
    {
        $res = DB::table('users')->where('id', $user)->delete();
        return $this->successResponse($res);
    }
}
