<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use Illuminate\Support\Facades\Hash;

class EmailVerifyController extends Controller
{
    use ResponseTrait;

    //* Verify users Email
    public function verify(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'email' => 'required|string'
        ]);
        if (!$request->hasValidSignature())
            return $this->errorResponse('link-expire', 405);
        $user = User::find($request->id);
        if (!$user)
            return $this->errorResponse('not-found/user', 405);
        if (!Hash::check($user->email, $request->email))
            return $this->errorResponse('not-found/user', 405);
        if ($user->email_verified_at)
            return $this->errorResponse('repeat/email-verified', 405);
        $user->email_verified_at = now();
        $user->save();
        return $this->successResponse($user->toArray(), 200, 'auth/email-verified');
    }
}
