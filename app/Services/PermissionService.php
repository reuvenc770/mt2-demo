<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 2/8/16
 * Time: 11:34 AM
 */

namespace App\Services;


use App\Repositories\PermissionRepo;
use App\Models\Permission;

class PermissionService
{
    protected $permissionRepo;

    protected $routeTypeSearchMap = [
        Permission::TYPE_CREATE => [ "add" , "store" , "copy" , "upload" , "create" ] ,
        Permission::TYPE_UPDATE => [ "edit" , "update" ] ,
        Permission::TYPE_DELETE => [ "destroy" ]
    ];

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

    public function addPermission ( $routeName , $crudType = null ) {
        if ( is_null( $crudType ) ) { $crudType = $this->getPermissionType( $routeName ); }

        return $this->permissionRepo->addPermission( $routeName , $crudType );
    }

    public function getPermissionType ( $routeName ) {
        $routeType = Permission::TYPE_READ;
        $routeEnding = '';

        if ( false === strrpos( $routeName , '.' ) ) { $routeEnding = $routeName; }
        else {
            $routeEnding = substr( $routeName , strrpos( $routeName , '.' ) + 1 );
        }

        if ( in_array( $routeEnding , $this->routeTypeSearchMap[ Permission::TYPE_CREATE ] ) ) {
            $routeType = Permission::TYPE_CREATE;
        } elseif ( in_array( $routeEnding , $this->routeTypeSearchMap[ Permission::TYPE_UPDATE ] ) ) {
            $routeType = Permission::TYPE_UPDATE;
        } elseif ( in_array( $routeEnding , $this->routeTypeSearchMap[ Permission::TYPE_DELETE ] ) ) {
            $routeType = Permission::TYPE_DELETE;
        }

        return $routeType;
    }

    public function getCurrentPermissionNames () {
        return $this->permissionRepo->getCurrentPermissionNames();
    }

    public function getId ( $permissionName ) {
        return $this->permissionRepo->getId( $permissionName );
    }
}
