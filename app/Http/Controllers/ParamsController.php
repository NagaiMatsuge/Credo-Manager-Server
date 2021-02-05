<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use Illuminate\Support\Facades\DB;

class ParamsController extends Controller
{
    use ResponseTrait;

    //* Get all Roles
    public function getAllRoles(Request $request)
    {
        $managers = DB::table('users as t1')
            ->leftJoin('roles as t2', 't2.id', '=', 't1.role_id')
            ->where('t2.name', 'Manager')
            ->where('t1.id', '<>', $request->user()->id)
            ->select(
                't1.id',
                't1.name',
                't1.photo',
                't1.color',
                't2.name as role'
            )
            ->get();
        return $this->successResponse([
            'roles' => DB::table('roles')->where('name', '<>', 'Admin')->get(),
            'managers' => $managers
        ]);
    }
}
