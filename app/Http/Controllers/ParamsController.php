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
            ->whereRaw('t1.id in (select a1.manager_id from users as a1 where a1.manager_id is not null)')
            ->select(
                't1.id',
                't1.name',
                't1.photo',
                't1.color',
                't2.name as role'
            )
            ->get();
        return $this->successResponse([
            'roles' => DB::table('roles')->get(),
            'managers' => $managers
        ]);
    }
}
