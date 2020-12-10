<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $validation = Validator::make($request->only(['email', 'password', 'remember_me']), [
            'email' => 'required|email',
            'password' => 'required|string',
            'remember_me' => 'nullable|boolean'
        ]);

        if ($validation->fails())
            return response()->json('Validation error');

        if (!Auth::attempt($request->only(['email', 'password']), $request->remember_me ?? false))
            return response()->json('Invalid credentials');

        $request->user()->tokens()->delete();
        $user = $request->user();
        $token = $user->createToken('Personal access token')->accessToken;
        return response()->json($token);
    }
}
