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
        return $this->permissionRepo->getAllPermissions();
    }
}