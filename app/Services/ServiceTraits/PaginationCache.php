<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Services\ServiceTraits;

use Log;
use Cache;

trait PaginationCache {
    public function getCacheKey ( $page , $count ) {
        return $this->getType() . ".{$page}.{$count}";
    }

    public function hasCache ( $page , $count ) {
        return Cache::tags( $this->getType() )->has( $this->getCacheKey( $page , $count ) );
    }

    public function getCachedJson ( $page , $count ) {
        return Cache::tags( $this->getType() )
            ->get( $this->getCacheKey( $page , $count ) );
    }

    public function cachePagination ( $json , $page , $count ) {
        $timeout = env("CACHETIMEOUT",60);

        Cache::tags( $this->getType() )->put(
            $this->getCacheKey( $page , $count ) ,
            $json ,
            $timeout 
        );
    }
    
    abstract public function getType();
}
