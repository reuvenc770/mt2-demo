<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 2/8/16
 * Time: 11:32 AM
 */

namespace App\Repositories;


use App\Models\Permission;

class PermissionRepo
{
    protected $permission;

    public function __construct(Permission $permission)
    {
        $this->permission = $permission;
    }

    public function getAllPermissions()
    {
        return $this->permission->all();
    }
}