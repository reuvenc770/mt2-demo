<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Services\ServiceTraits;

use Log;
use App\Services\ServiceTraits\PaginationCache;

trait PaginateList {
    use PaginationCache;

    public function getPaginatedJson ( $page , $count, $options=[] ) {

        if ( $this->hasCache( $page , $count, $options ) ) {
            return $this->getCachedJson( $page , $count, $options );

        } else {
            try {
                $eloquentObj = $this->getModel();

                if ( isset( $options['sort'] ) ){
                    $sort = json_decode( $options['sort'] , true );

                    $order = 'asc';

                    if ( isset( $sort[ 'desc' ] ) && $sort[ 'desc' ] === true ) {
                        $order = 'desc';
                    }

                    $eloquentObj = $eloquentObj->orderBy($sort['field'], $order );
                }

                $paginationJSON = $eloquentObj->paginate( $count )->toJSON();

                $this->cachePagination(
                    $paginationJSON ,
                    $page ,
                    $count,
                    $options
                );

                return $paginationJSON;
            } catch ( \Exception $e ) {
                Log::error( $e->getMessage() );
                return false;
            }
        }
    }
    public function getType(){
        return class_basename($this->getModel());
    }
    abstract public function getModel();
}
