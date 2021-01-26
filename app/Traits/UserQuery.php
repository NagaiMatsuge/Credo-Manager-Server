<?php

namespace App\Traits;

use Exception;
use Illuminate\Support\Facades\DB;

trait UserQuery
{
    //* Get user and role | one or multiple | with or without pagination
    public static function userRole($id = null, $per_page = null): object
    {
        $query = DB::table('users')->leftJoin('roles', 'users.role_id', '=', 'roles.id')->select(
            'users.id',
            'users.name',
            'users.email',
            'users.phone',
            'users.work_start_time',
            'users.work_end_time',
            'users.manager_id',
            'users.pause_start_time',
            'users.pause_end_time',
            'users.photo',
            'users.color',
            'users.theme',
            'roles.name as role'
        );
        if ($id)
            $query = $query->where('users.id', $id)->first();
        else if ($per_page)
            $query = $query->paginate($per_page);
        else
            $query = $query->get();

        return $query;
    }

    //* Delete user By its id
    public static function deleteOneById($id)
    {
        return DB::table('users')->where('id', $id)->delete();
    }

    //* Check if user has role | one of given roles
    public function hasRole($role)
    {
        $query = DB::table('users')->leftJoin('roles', 'users.role_id', '=', 'roles.id')->select(
            'users.id',
            'users.name',
            'users.email',
            'users.phone',
            'users.work_start_time',
            'users.work_end_time',
            'users.manager_id',
            'users.pause_start_time',
            'users.pause_end_time',
            'users.photo',
            'users.color',
            'users.theme',
            'roles.name as role'
        );
        $user = $query->where('users.id', $this->id)->first();

        if (is_string($role))
            return (bool)($user->role === $role);
        else if (is_array($role)) {
            $res = false;
            foreach ($role as $single)
                if ($single === $user->role) $res = true;

            return $res;
        }
    }

    //* Synchronize user role
    public function syncRoles(String $role)
    {
        $role = DB::table('roles')->where('name', $role)->first();
        if (!$role)
            throw new Exception("Role not found");
        else
            DB::table('users')->where('id', $this->id)->update(['role_id' => $role->id]);
    }
}
