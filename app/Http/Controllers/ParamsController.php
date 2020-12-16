<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\ResponseTrait;

class ParamsController extends Controller
{
    use ResponseTrait;

    //* Get all Roles
    public function getAllRoles(Request $request)
    {
        return $this->successResponse(config('params.roles'));
    }
}
