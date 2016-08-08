<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Services\ServiceTraits;

use Log;
use App\Services\ServiceTraits\PaginationCache;

trait PaginateList {
    use PaginationCache;

    public function getPaginatedJson ( $page , $count ) {

            try {
                $eloquentObj = $this->getModel();

                $paginationJSON = $eloquentObj->paginate( $count )->toJSON();
                $this->cachePagination(
                    $paginationJSON ,
                    $page ,
                    $count
                );

                return $paginationJSON;
            } catch ( \Exception $e ) {
                Log::error( $e->getMessage() );
                return false;
            }

    }
    public function getType(){
        return class_basename($this->getModel());
    }
    abstract public function getModel();
}
