<?php

namespace App\Repositories;

use DB;
use App\Models\FrontendFeature;

class FrontendFeaturePermissionRepo {
  protected $frontendFeature;

  public function __construct ( FrontendFeature $frontendFeature ) {
    $this->frontendFeature = $frontendFeature;
  }

  public function getAllFeaturePermissions (){
    $features = $this->frontendFeature->with('permissions')->get();
    return $features->groupBy('page_id');
  }
}
