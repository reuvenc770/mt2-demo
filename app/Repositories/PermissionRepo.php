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
        return $this->permission->all()->sortBy('name');
    }

    public function addPermission ( $routeName , $crudType ) {
        $permission = new Permission();
        $permission->name = $routeName;
        $permission->crud_type = $crudType;
        $permission->save();

        return $permission->id;
    }

    public function getCurrentPermissionNames () {
        $allPermissions = $this->getAllPermissions();

        return $allPermissions->pluck( 'name' )->flatten();
    }

    public function getId ( $permissionName ) {
        $permission = $this->permission->where( 'name' , $permissionName )->get();

        return $permission[0]->id;
    }
}
