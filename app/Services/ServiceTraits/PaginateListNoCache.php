<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Services\ServiceTraits;

use Log;

trait PaginateListNoCache {

    public function getPaginatedJson ( $page , $count ) {

            try {
                $eloquentObj = $this->getModel();

                $paginationJSON = $eloquentObj->paginate( $count )->toJSON();

                return $paginationJSON;
            } catch ( \Exception $e ) {
                Log::error($e->getMessage());
                return false;

            }
    }

    abstract public function getModel();
}
