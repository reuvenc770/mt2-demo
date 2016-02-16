<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 2/8/16
 * Time: 11:34 AM
 */

namespace App\Services;


use App\Repositories\PermissionRepo;

class PermissionService
{
    protected $permissionRepo;

    public function __construct(PermissionRepo $permissionRepo)
    {
        $this->permissionRepo = $permissionRepo;
    }


    public function getAllPermissions()
    {
        $permissions = $this->permissionRepo->getAllPermissions();
        $returnArray = array();
        foreach($permissions as $permission){
           $whichArray = substr($permission->name,0,3) == "api" ? "api":"routes";
           $returnArray[$whichArray][] = $permission;
        }
        return $returnArray;
    }
}