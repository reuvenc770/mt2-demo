<?php

namespace App\Repositories;

use DB;
use App\Models\FrontendFeature;
use App\Models\FrontendFeaturePermissionMapping;

class FrontendFeaturePermissionRepo {
  protected $frontendFeature;
  protected $featurePermission;

  public function __construct ( FrontendFeature $frontendFeature , FrontendFeaturePermissionMapping $featurePermission ) {
    $this->frontendFeature = $frontendFeature;
    $this->featurePermission = $featurePermission;
  }

  public function getAllFeaturePermissions (){
    $features = $this->frontendFeature->with('permissions')->get();
    return $features->groupBy('page_id');
  }
}
