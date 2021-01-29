<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\VerifyEmail;
use App\Models\User;
use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class EmailVerifyController extends Controller
{
    use ResponseTrait;

    //* Verify users Email
    public function verify(Request $request, $id)
    {
        if (!$request->hasValidSignature())
            return $this->errorResponse('link-expire', 405);
        $user = User::find($id);
        if (!$user)
            return $this->errorResponse('not-found/user', 405);
        if ($user->email_verified_at)
            return $this->errorResponse('repeat/email-verified', 405);
        $user->email_verified_at = now();
        $user->save();
        return redirect()->away(config('params.urls.front_base_url') . '/login');
    }

    //* Send email verification message
    // public function verifyAgain(Request $request)
    // {
    //     $request->validate([
    //         'email' => 'required|email'
    //     ]);
    //     $user = DB::table('users')->where('email', $request->email)->first();

    //     if (!$user)
    //         return $this->errorResponse('not-found/user');
    //     if ($user->email_verfied_at)
    //         return $this->errorResponse('repeat/email-verified');

    //     Mail::to($request->email)->send(new VerifyEmail($user, 'Пароль не бел изменен'));

    //     return $this->successResponse($user->toArray(), 201, 'email-verify');
    // }
}
