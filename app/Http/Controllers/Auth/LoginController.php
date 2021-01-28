<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\VerifyEmail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Traits\ResponseTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

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

        if (!$user->email_verified_at) {
            Mail::to($user->email)->send(new VerifyEmail($user, 'Пароль не бел изменен'));
            return $this->errorResponse('auth/email-not-verified');
        }
        $token = $user->createToken('Personal access token')->accessToken;
        $rolesOfTheUser = DB::table('roles')->where('id', $user->role_id)->first();
        $res = array_merge($request->user()->toArray(), ['_token' => $token]);
        $res['role'] = $rolesOfTheUser->name;
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
        //     'color' => '#8F73FC'
        // ]);

        // $user2 = User::create([
        //     'name' => 'Palonchi',
        //     'email' => 'TestEmail@gmail.com',
        //     'password' => Hash::make('password'),
        //     'work_start_time' => '9:9:9',
        //     'work_end_time' => '8:8:8',
        //     'pause_start_time' => '7:7:7',
        //     'pause_end_time' => '6:6:6',
        //     'working_days' => '[1, 2, 3, 4, 5]',
        //     'color' => '#FCB573'
        // ]);
        // $user3 = User::create([
        //     'name' => 'Somebody',
        //     'email' => 'TestTestEmail@gmail.com',
        //     'password' => Hash::make('password'),
        //     'work_start_time' => '9:9:9',
        //     'work_end_time' => '8:8:8',
        //     'pause_start_time' => '7:7:7',
        //     'pause_end_time' => '6:6:6',
        //     'working_days' => '[1, 2, 3, 4, 5]',
        //     'color' => '#FC73AD'
        // ]);
        // return response()->json($user2->toArray());
        // $role = Role::create(['name' => 'Admin', 'guard_name' => 'web']);
        // $user = User::find('290001ce-f3b6-4f93-9e90-eb1613347b77');
        // $user2->syncRoles('Admin');
        // $user3->syncRoles('Admin');

        // return response()->json($user->getRoleNames()->toArray());
    }
}
