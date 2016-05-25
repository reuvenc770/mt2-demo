<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Repositories\MT1Repositories;

use App\Models\ModelTraits\ModelCacheControl;
use App\Models\MT1Models\CategoryInfo;
use Log;

class OfferCategoryRepo {
    use ModelCacheControl;

    protected $model;

    public function __construct ( CategoryInfo $model ) {
        $this->model = $model;
    }

    public function getAll () {
        try {
            return $this->model::select( 'category_id AS id' , 'category_name as name' )
                ->where( 'status' , 'A' )
                ->orderBy( 'category_name' )
                ->get();
        } catch ( \Exception $e ) {
            Log::error( "OfferCategoryRepo Error: " . $e->getMessage() );
        }
    }
}
