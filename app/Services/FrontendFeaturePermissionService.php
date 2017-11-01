<?php

namespace App\Services;

use App\Repositories\FrontendFeaturePermissionRepo;
use App\Services\PageService;

class FrontendFeaturePermissionService {
  protected $repo;
  protected $pageService;
  
  public function __construct ( FrontendFeaturePermissionRepo $repo , PageService $pageService ) {
    $this->repo = $repo;
    $this->pageService = $pageService;
  }

  public function getPermissionTree ( $rolePermissions = [] ){
    $permissionTree = [];

    $features = $this->repo->getAllFeaturePermissions();

    foreach ( $features as $key => $currentGroup ) {

      $featureGroupName = $this->pageService->getPageName( $key );

      $currentNode = [
        'id' => $key . '.featureGroup',
        'label' => trans( 'featureGroup.' . $featureGroupName ),
        'children' => []
      ];

      foreach ( $currentGroup as $featureKey => $currentFeature ){
        $currentNode['children'] [] = [
          'id' => $currentFeature->id,
          'label' => $currentFeature->name ,
          'value' => [],
          'children' => []
        ];

        foreach ( $currentFeature['permissions'] as $permission) {
          $currentNode['children'][$featureKey]['value'] [] = $permission->name;
        }

        $featurePermissions = $currentNode['children'][$featureKey]['value'];
        if ( count( array_diff( $featurePermissions , $rolePermissions ) ) === 0 ) {
          $currentNode['children'][ $featureKey]['selected'] = true;
        }

        $currentNode['children'][$featureKey]['children'] [] = [
          'id' => $currentFeature->id . '.featureDescription',
          'label' => $currentFeature->description
        ];
      }

      $permissionTree [] = $currentNode;
    }

    return $permissionTree; 
  }
}
