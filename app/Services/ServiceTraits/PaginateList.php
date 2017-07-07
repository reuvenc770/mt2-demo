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
                $eloquentObj = $this->getModel( $options );

                if ( isset( $options['sort'] ) ){
                    $sort = json_decode( $options['sort'] , true );

                    $order = 'asc';

                    if ( isset( $sort[ 'desc' ] ) && $sort[ 'desc' ] === true ) {
                        $order = 'desc';
                    }

                    $eloquentObj = $eloquentObj->orderBy($sort['field'], $order );
                }

                if ( $count > 0 ) {
                    $paginationJSON = $eloquentObj->paginate( $count )->toJSON();
                } else {
                    $recordCount = $eloquentObj->count();

                    $paginationJSON = json_encode( [
                        "current_page" => 1 ,
                        "last_page" => 1 ,
                        "from" => 1 ,
                        "to" => $recordCount ,
                        "total" => $recordCount ,
                        "data" => $eloquentObj->get()->toArray()
                    ] );
                }

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
    abstract public function getModel( $options = [] );
}
