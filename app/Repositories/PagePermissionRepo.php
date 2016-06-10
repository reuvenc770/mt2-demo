<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Repositories;

use DB;
use App\Models\PagePermission;

class PagePermissionRepo {
    protected $pagePermission;

    public function __construct ( PagePermission $pagePermission ) {
        $this->pagePermission = $pagePermission;
    }

    public function getPermissions ( $pageId ) {
        return $this->pagePermission->where( 'page_id' , $pageId )->with( 'permissions' )->get();
    }

    public function addPagePermission ( $pageId , $permissionId ) {
        $pagePermission = new PagePermission();
        $pagePermission->page_id = $pageId;
        $pagePermission->permission_id = $permissionId;
        $pagePermission->save();
    }

    public function getCrudPermissions ( $pageId , $crudOperation ) {
        $pagePermissions = DB::table( 'page_permissions' ) 
            ->join( 'permissions' , 'page_permissions.permission_id' , '=' , 'permissions.id' )
            ->where( [
                [ 'page_permissions.page_id' , $pageId ] ,
                [ 'permissions.crud_type' , $crudOperation ]
            ] )
            ->pluck( 'permissions.name' );

        return $pagePermissions;
    }
}
