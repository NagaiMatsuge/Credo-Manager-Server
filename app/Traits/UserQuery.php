<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;

trait UserQuery
{
    //* Get all users with their role and pagination
    public static function usersWithRoleAndPagination($per_page = 10)
    {
        return DB::table('users')->select('users.*', DB::raw('(SELECT roles.name FROM roles WHERE roles.id=(SELECT model_has_roles.role_id FROM model_has_roles WHERE model_has_roles.model_uuid=users.id LIMIT 1)) as role'))->paginate($per_page);
    }

    //* Get one user with his role
    public static function userWithRole($id)
    {
        return DB::select('SELECT users.*, (SELECT roles.name FROM roles WHERE roles.id=(SELECT model_has_roles.role_id FROM model_has_roles WHERE model_has_roles.model_uuid=users.id LIMIT 1)) as role FROM users WHERE users.id=?', [$id]);
    }

    //* Delete user By its id
    public static function deleteOneById($id)
    {
        return DB::table('users')->where('id', $id)->delete();
    }
}
