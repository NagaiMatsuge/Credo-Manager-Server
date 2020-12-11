<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Traits\ResponseTrait;

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

        if (!Auth::attempt($request->only(['email', 'password']), $request->remember_me))
            return $this->errorResponse('Credentials don\'t match our records!', 401);
        $user = $request->user();
        $request->user()->tokens()->delete();

        $token = $user->createToken('Personal access token')->accessToken;
        return $this->successResponse(array_merge($request->user()->toArray(), ['_token' => $token]), 200, 'Login Successfull');
    }
}
