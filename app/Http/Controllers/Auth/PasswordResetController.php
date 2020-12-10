<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\PasswordReset;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Traits\ResponseTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class PasswordResetController extends Controller
{
    use ResponseTrait;

    //* Send email for password reset
    public function forgot(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $user = User::where('email', $request->email)->first();
        if (!$user)
            return $this->errorResponse('Email doesn\'t exists', 403);

        $token = Str::random(60);
        DB::table('password_resets')->insert([
            'email' => $user->email,
            'token' => $token,
            'created_at' => now()
        ]);

        Mail::to($user)->send(new PasswordReset($user, $token));

        return $this->successResponse([], 200, "Email is sent to $user->email");
    }

    //* Reset password of the user
    public function reset(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'token' => 'required|string',
            'password' => 'required|string|min:6'
        ]);

        $user = User::where('email', $request->email)->first();
        if (!$user)
            return $this->errorResponse('User not found', 403);
        $token = DB::table('password_resets')->where('token', $request->token)->first();
        if (!$token)
            return $this->errorResponse('Password reset failed');
        $user->update(['password' => Hash::make($request->password)]);
        DB::table('password_resets')->where('token', $request->token)->delete();
        return $this->successResponse($user->toArray(), 200, 'Password has been changed successfully');
    }
}
