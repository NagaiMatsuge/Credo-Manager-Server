<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Traits\ResponseTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

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
        // $request->user()->tokens()->delete();

        $token = $user->createToken('Personal access token')->accessToken;
        return $this->successResponse(array_merge($request->user()->toArray(), ['_token' => $token]), 200, 'Login Successfull');
    }

    //* Describe your method
    public function create(Request $request)
    {
        $user = User::create([
            'name' => 'Ruslan',
            'email' => 'menrusamen19992@gmail.com',
            'password' => Hash::make('password'),
            'work_start_time' => '9:9:9',
            'work_end_time' => '8:8:8',
            'pause_start_time' => '7:7:7',
            'pause_end_time' => '6:6:6',
            'working_days' => '[1, 2, 3, 4, 5]',
            'developer' => true
        ]);
        return response()->json($user->toArray());
    }
}
