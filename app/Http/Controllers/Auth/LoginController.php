<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\VerifyEmail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Traits\ResponseTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Role;

class LoginController extends Controller
{
    use ResponseTrait;

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
            'remember_me' => 'nullable|boolean'
        ]);

        if (!Auth::attempt($request->only(['email', 'password'])))
            return $this->errorResponse('auth/fail', 400);
        $user = $request->user();
        $request->user()->tokens()->delete();
        // if (!$user->email_verified_at) {
        //     Mail::to($user->email)->send(new VerifyEmail($user, 'Пароль не бел изменен'));
        //     return $this->errorResponse('auth/email-not-verified');
        // }
        $token = $user->createToken('Personal access token')->accessToken;
        $rolesOfTheUser = $user->getRoleNames()->toArray();
        $res = array_merge($request->user()->toArray(), ['_token' => $token]);
        $res['role'] = $rolesOfTheUser;
        return $this->successResponse($res, 200, 'Login Successfull');
    }

    //* Describe your method
    public function create(Request $request)
    {
        // $user = User::create([
        //     'name' => 'Ruslan',
        //     'email' => 'menrusamen19992@gmail.com',
        //     'password' => Hash::make('password'),
        //     'work_start_time' => '9:9:9',
        //     'work_end_time' => '8:8:8',
        //     'pause_start_time' => '7:7:7',
        //     'pause_end_time' => '6:6:6',
        //     'working_days' => '[1, 2, 3, 4, 5]',
        //     'developer' => true,
        //     'color' => '#8F73FC'
        // ]);
        // return response()->json($user->toArray());
        // $role = Role::create(['name' => 'Admin', 'guard_name' => 'web']);
        // $user = User::find('539e6f20-bf49-456c-a4fb-b4c483fddea2');
        // $user->syncRoles('Admin');
        // return response()->json($user->getRoleNames()->toArray());
    }
}
