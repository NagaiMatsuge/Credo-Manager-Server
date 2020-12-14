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
        $user = User::latest()->paginate(10);
        return $this->successResponse($user);
    }

    public function show(User $id)
    {
        return $this->successResponse($id);;
    }

    public function update(Request $request, User $id)
    {
        $id->update($request->all());
        return $this->successResponse($id);
    }

    public function destroy($id)
    {
        $res = DB::table('users')->where('id', $id)->delete();
        return $this->successResponse($res);
    }

    //* Fetch user credentials
    public function getUser(Request $request)
    {
        $roles = $request->user()->getRoleNames()->toArray();
        return $this->successResponse(array_merge(['role' => $roles], $request->user()->toArray()));
    }
}
