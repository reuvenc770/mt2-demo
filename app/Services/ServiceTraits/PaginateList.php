<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Services\ServiceTraits;

use Log;
use Cache;
#use App\Facades\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;

trait PaginateList {
    public function getPaginatedJson ( $page , $count ) {

        try {
            $eloquentObj = $this->getModel();

            $paginationJSON = $eloquentObj->paginate( $count )->toJSON();
        } catch ( \Exception $e ) {
            Log::error( $e->getMessage() );
            return false;
        }
    }

    public function getRecordCacheKey ( $page , $count ) {
        return $this->getType() . ".{$page}.{$count}";
    }

    public function getPageCountCacheKey ( $count ) {
        return $this->getType() . ".pageCount.{$count}";
    }

    abstract public function getModel();

    abstract public function getType();
}
