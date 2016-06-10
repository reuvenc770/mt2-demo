<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Services;

use App\Repositories\PagePermissionRepo;
use App\Services\PageService;

class PagePermissionService {
    protected $repo;
    protected $pageService;

    public function  __construct ( PagePermissionRepo $repo , PageService $pageService ) {
        $this->repo = $repo;
        $this->pageService = $pageService;
    }

    public function getPermissionTree ( $rolePermissions = [] ) {
        $permissionTree = [];

        $pages = $this->pageService->getAllPages();

        foreach ( $pages as $current ) {
            $currentNode = [
                'id' => $current->id . '.page',
                'label' => trans( 'navigation.' . $current->name ) ,
                'children' => []
            ];

            $permissions = $this->repo->getPermissions( $current->id );

            $typeIndices = [];

            foreach ( $permissions as $currentPermission ) {
                if ( isset( $currentPermission->permissions[ 0 ] ) ) {
                    $permissionType = $currentPermission->permissions[ 0 ]->crud_type;
                } else {
                    continue;
                }

                if ( !isset( $typeIndices[ $permissionType ] ) ) {
                    $currentNode[ 'children' ] []= [
                        'id' => $current->id . '.' . $permissionType ,
                        'label' => studly_case( $permissionType ) ,
                        'children' => []
                    ];
                    
                    $typeIndices[ $permissionType ] = count( $currentNode[ 'children' ] ) - 1;
                }

                $childIndex = $typeIndices[ $permissionType ];

                $childPermission = [
                    'id' => $currentPermission->permissions[ 0 ]->name ,
                    'label' => trans( 'permissions.' . $currentPermission->permissions[ 0 ]->name ) 
                ];

                if ( in_array( $currentPermission->permissions[ 0 ]->name , $rolePermissions ) ) {
                    $childPermission[ 'selected' ] = true;
                }

                $currentNode[ 'children' ][ $childIndex ][ 'children' ] []= $childPermission;
            }

            ksort( $currentNode[ 'children' ] );

            $permissionTree []= $currentNode;
        }

        return $permissionTree;
    }

    public function addPagePermission ( $pageId , $permissionId ) {
        $this->repo->addPagePermission( $pageId , $permissionId );
    }

    public function getCrudPermissions ( $pageId , $crudOperation ) {
        return $this->repo->getCrudPermissions( $pageId , $crudOperation );
    }
}
