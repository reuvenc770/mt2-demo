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

    public function getAllPermissionsWithParent($id)
    {
        return $this->permission->where("parent",$id)->orderBy("rank")->get();
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

    public function getAllOrphanPermissions(){
        return $this->permission->where("parent", 0)->get();
    }

    public function updateParentAndRank($id, $parentId, $rank){
        return $this->permission->find($id)->update(['parent'=>$parentId, 'rank' =>$rank]);
    }

    public function makeBatmans($wealthyKidsWithParents){
        return $this->permission->whereNotIn('id',$wealthyKidsWithParents)->update(['parent'=>0,'rank'=>0]);
    }
}
