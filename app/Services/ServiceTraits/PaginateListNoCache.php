<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Services\ServiceTraits;

use Log;

trait PaginateListNoCache {

    public function getPaginatedJson ( $page , $count, $options=[] ) {

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

                return $paginationJSON;
            } catch ( \Exception $e ) {
                Log::error($e->getMessage());
                return false;

            }
    }

    abstract public function getModel();
}
