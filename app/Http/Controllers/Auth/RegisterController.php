<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\VerifyEmail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Traits\ResponseTrait;

class RegisterController extends Controller
{
    use ResponseTrait;

    //* Registering users
    public function register(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'work_start_time' => 'required|date',
            'work_end_time' => 'required|date',
            'manager_id' => 'nullable',
            'pause_start_time' => 'required|date',
            'pause_end_time' => 'required|date',
            'working_days' => 'required',
            'developer' => 'nullable|boolean',
            'name' => 'required|string|min:3|max:255'
        ]);

        $user = User::create($request->input());

        Mail::to($request->email)->send(new VerifyEmail($user, $request->password));

        return $this->successResponse($user->toArray(), 201, 'email-verify');
    }
}
