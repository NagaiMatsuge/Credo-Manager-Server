<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

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

    //* Update user By its id
    public function update(Request $request, $id)
    {
        $this->validateRequest($request);

        // DB::table('users')->
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

    //* Validate request
    public function validateRequest(Request $request)
    {
        $request->validate([
            'email' => 'required|email|min:3',
            'password' => 'required|string|min:8',
            'work_start_time' => 'required|date',
            'work_end_time' => 'required|date',
            'pause_start_time' => 'required|date',
            'pause_end_time' => 'required|date',
            'working_days' => 'required|json',
            'role' => [
                'required',
                Rule::in(config('params.roles'))
            ],
            'color' => 'required|string',
            'photo' => 'nullable|image'
        ]);
    }
}
