<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\VerifyEmail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Traits\ResponseTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class RegisterController extends Controller
{
    use ResponseTrait;

    //* Registering users
    public function register(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'work_start_time' => 'required|date_format:H:i',
            'work_end_time' => 'required|date_format:H:i',
            'manager_id' => 'nullable',
            'pause_start_time' => 'required|date_format:H:i',
            'pause_end_time' => 'required|date_format:H:i',
            'working_days' => 'required|array',
            'name' => 'required|string|min:3|max:255',
            'role' => [
                'required',
                Rule::in(config('params.roles'))
            ],
            'color' => 'required|string',
            'manager_id' => 'nullable|string|exists:users,id'
        ]);
        // $data = $request->except('working_days');
        // $working_days = json_encode($request->working_days);
        // $data['working_days'] = "$working_days";
        // return $this->successResponse($request->input());
        $data = $request->except(['password', 'role']);
        $data['password'] = Hash::make($request->password);
        $role = DB::table('roles')->where('name', $request->role)->first();
        $data['role_id'] = $role->id;
        $user = User::create($data);

        Mail::to($request->email)->send(new VerifyEmail($user, $request->password));

        return $this->successResponse($user->toArray(), 201, 'email-verify');
    }
}
