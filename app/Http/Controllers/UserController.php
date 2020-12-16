<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Traits\ResponseTrait;

class UserController extends Controller
{
    use ResponseTrait;

    //* Return All users with their role
    public function index(Request $request)
    {
        return $this->successResponse(User::usersWithRoleAndPagination());
    }

    //* Show one user by its id
    public function show($id)
    {
        return $this->successResponse([
            'user' => User::userWithRole($id),
            'roles' => config('params.roles')
        ]);
    }

    //* Update user by its id
    public function update(Request $request, User $id)
    {
        $id->update($request->input());
        return $this->successResponse($id);
    }

    //* Delete user by its id
    public function destroy($id)
    {
        return $this->successResponse(User::deleteOneById($id));
    }

    //* Fetch user credentials
    public function getUser(Request $request)
    {
        $user = User::userWithRole($request->user()->id);
        return $this->successResponse($user[0]);
    }
}
