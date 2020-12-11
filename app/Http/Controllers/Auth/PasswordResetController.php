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
            return $this->errorResponse('not-found/user');

        $token = Str::random(60);
        DB::table('password_resets')->updateOrInsert(
            [
                'email' => $user->email,
            ],
            [
                'token' => $token,
                'created_at' => now()
            ]
        );

        Mail::to($user)->send(new PasswordReset($user, $token));

        return $this->successResponse([], 200, "email-reset");
    }

    //* Reset password of the user
    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'password' => 'required|string|min:8'
        ]);

        $token = DB::table('password_resets')->where('token', $request->token)->first();
        if (!$token)
            return $this->errorResponse('not-found/user');
        $user = User::where('email', $token->email)->first();
        if (!$user)
            return $this->errorResponse('not-found/user', 403);
        $user->update(['password' => Hash::make($request->password)]);
        DB::table('password_resets')->where('token', $request->token)->delete();
        info($request->password);
        return $this->successResponse($user->toArray(), 200, 'Password has been changed successfully');
    }
}
