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
        $users = DB::table('users')->select('users.*', DB::raw('(SELECT roles.name FROM roles WHERE roles.id=(SELECT model_has_roles.role_id FROM model_has_roles WHERE model_has_roles.model_uuid=users.id LIMIT 1)) as role'))->paginate(10);
        return $this->successResponse($users);
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
        $user = DB::select('SELECT users.*, (SELECT roles.name FROM roles WHERE roles.id=(SELECT model_has_roles.role_id FROM model_has_roles WHERE model_has_roles.model_uuid=users.id LIMIT 1)) as role FROM users WHERE users.id=?', [$request->user()->id]);
        return $this->successResponse($user[0]);
    }
}
