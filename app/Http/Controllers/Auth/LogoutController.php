<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\ResponseTrait;

class LogoutController extends Controller
{
    use ResponseTrait;

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return $this->successResponse([], 200, 'Logout successfull');
    }
}
