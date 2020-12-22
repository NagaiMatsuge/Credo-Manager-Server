<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
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
        $user = User::userWithRole($id);
        $user[0]->working_days = json_decode($user[0]->working_days);
        return $this->successResponse($user);
    }

    //* Update user By its id
    public function update(Request $request, User $user)
    {
        $this->validateRequest($request);

        $data = $request->except(['role', 'photo', 'password']);

        if ($request->has('photo')) {
            $image = $request->file('photo')->store('avatars');
            $data['photo'] = $image;
        }
        if ($request->has('password'))
            $data['password'] = Hash::make($request->password);

        $user->update($data);
        $user->syncRoles($request->role);
        return $this->successResponse($user->toArray());
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
            'password' => 'nullable|string|min:8',
            'work_start_time' => 'required|date_format:H:i',
            'work_end_time' => 'required|date_format:H:i',
            'pause_start_time' => 'required|date_format:H:i',
            'pause_end_time' => 'required|date_format:H:i',
            'working_days' => 'required|array',
            'role' => [
                'required',
                Rule::in(config('params.roles'))
            ],
            'color' => 'nullable|string',
            'photo' => 'nullable|image'
        ]);
    }

    public function settingUpdate(Request $request, $id)
    {
        $request->validate([
            'photo' => 'nullable|image',
            'new_password' => 'nullable|min:8',
            'name' => 'required|min:3',
            'phone' => 'nullable',
            'password' => [
                'min:8',
                Rule::requiredIf($request->has('new_password'))
            ]
        ]);

        $data = $request->only(['photo', 'password', 'new_password', 'name', 'phone']);

        $user = DB::table('users')->where('id', $id)->first();

        if ($request->has('photo')) {
            if ($user->photo)
                Storage::disk('public')->delete($user->photo);
            $image = $request->file('photo')->store('avatars');
            $data['photo'] = $image;
        }

        if ($request->has('new_password')) {
            if (Hash::check($request->new_password, $user->password))
                return $this->errorResponse('password-matches-old');
            if (Hash::check($request->password, $user->password)) {
                $data['password'] = Hash::make($request->new_password);
            } else {
                return $this->errorResponse('password-dontmatch');
            }
        }

        $res = DB::table('users')->where('id', $id)->update($data);

        return $this->successResponse($res);
    }
}
