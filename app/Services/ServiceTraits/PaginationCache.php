<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Services\ServiceTraits;

use Log;
use Cache;

trait PaginationCache {
    public function getCacheKey ( $page , $count, $params = null ) {
        $md5ParamList = isset($params) ? md5(implode(',', $params)) : '';
        return $this->getType() . $md5ParamList . ".{$page}.{$count}";
    }

    public function hasCache ( $page , $count, $params = null ) {
        return Cache::tags( $this->getType() )->has( $this->getCacheKey( $page , $count, $params ) );
    }

    public function getCachedJson ( $page , $count, $params = null ) {
        return Cache::tags( $this->getType() )
            ->get( $this->getCacheKey( $page , $count, $params ) );
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
