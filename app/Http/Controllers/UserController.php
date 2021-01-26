<?php

namespace App\Http\Controllers;

use App\Http\Resources\SingleUserResource;
use App\Models\User;
use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Traits\UploadTrait;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    use ResponseTrait, UploadTrait;

    //* Return All users with their role
    public function index(Request $request)
    {
        return $this->successResponse(User::usersWithRoleAndPagination());
    }

    //* Show one user by its id
    public function show($id)
    {
        $user = User::userWithRole($id);
        $res = new SingleUserResource($user[0]);
        return $this->successResponse([$res]);
    }

    //* Update user By its id
    public function update(Request $request, User $user)
    {
        $this->validateRequest($request);

        $data = $request->except(['role', 'photo', 'password']);

        if ($request->has('photo')) {
            $data['photo'] = $this->uploadFile($request->input('photo'), 'avatars');
            if ($user->photo) {
                Storage::disk('public')->delete($user->photo);
            }
        }
        if ($request->has('password'))
            $data['password'] = Hash::make($request->password);

        $user->update($data);
        if ($request->user()->hasRole(['Admin'])) {
            $user->syncRoles($request->role);
        }

        if (in_array($request->role, ['Admin', 'Manager']))
            DB::table('task_user')->where('user_id', $user->id)->delete();

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
        $curr_user = $request->user();
        $user = User::userWithRole($curr_user->id);
        $notifs = DB::table('notification_user as t1')->leftJoin('notifications as t2', 't1.notification_id', '=', 't2.id')->leftJoin('model_has_roles as t3', 't3.model_uuid', '=', 't2.user_id')->leftJoin('roles as t4', 't4.id', '=', 't3.role_id')->leftJoin('users as t5', 't5.id', '=', 't2.user_id')->where('t1.read', false)->select('t2.id', 't2.user_id', 't5.name as user_name', 't5.color as user_color', 't5.photo as user_photo', 't2.text', 't2.publish_date', 't4.name as role')->where('t1.to_user', $curr_user->id)->where('t2.publish_date', '<', now())->get();
        $user = $user[0];
        $res = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'work_start_time' => substr($user->work_start_time, 0, -3),
            'work_end_time' => substr($user->work_end_time, 0, -3),
            'pause_start_time' => substr($user->pause_start_time, 0, -3),
            'pause_end_time' => substr($user->pause_end_time, 0, -3),
            'notifications' => $notifs,
            'manager_id' => $user->manager_id,
            'photo' => $user->photo,
            'color' => $user->color,
            'theme' => $user->theme,
            'role' => $user->role
        ];
        return $this->successResponse($res);
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
            'photo' => 'nullable|string'
        ]);
    }

    public function settingUpdate(Request $request, $id)
    {
        $request->validate([
            'photo' => 'nullable|string',
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
            $image = $this->uploadFile($request->input('photo'), 'avatars');
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

    //* Change users pereferred theme
    public function changeTheme(Request $request)
    {
        $request->validate([
            'theme' => [
                Rule::in(config('params.themes')),
                'required'
            ]
        ]);

        DB::table('users')->where('id', $request->user()->id)->update([
            'theme' => $request->theme
        ]);
        return $this->successResponse(true);
    }
}
