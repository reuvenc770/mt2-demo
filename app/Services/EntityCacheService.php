<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

namespace App\Services;

use Cache;

class EntityCacheService {
    const CONFIG_FILE = 'entity_cache';
    const ENTITY_CACHE_TAG = 'entity_cache';

    public function __construct () {}

    public static function exists ( $class , $configString ) {
        return Cache::tags( self::ENTITY_CACHE_TAG )->has( self::getCacheKey( $class , $configString  ) );
    }

    public static function get ( $class , $configString , $parameters = [] ) {
        $cacheKey = self::getCacheKey( $class , $configString );
        $method = self::getMethod( $class , $configString );

        if ( !self::exists( $class , $configString ) ) {
            $repo = \App::make( $class );

            if ( !method_exists( $repo , $method ) ) {
                $message = "$class does not have the method '$method' using config '$configString'.";
                \Log::error( $message );

                throw new \Exception( $message );
            }

            self::set( $class , $configString , call_user_func_array( [ $repo , $method ] , $parameters ) );

            unset( $repo );
        }

        return Cache::tags( self::ENTITY_CACHE_TAG )->get( $cacheKey );
    }

    public static function set ( $class , $configString , $data ) {
        return Cache::tags( self::ENTITY_CACHE_TAG )->forever( self::getCacheKey( $class , $configString  ) , $data );
    }

    public static function forget ( $class , $configString ) {
        return Cache::tags( self::ENTITY_CACHE_TAG )->forget( self::getCacheKey( $class , $configString  ) );
    }

    public static function forgetForModel ( $modelClass ) {
        $config = config( self::CONFIG_FILE );

        foreach ( $config as $repoClass => $details ) {
            if ( in_array( $modelClass , $details[ 'models' ] ) ) {
                foreach ( $details as $key => $value ) {
                    if ( $key == 'models' ) {
                        continue;
                    }

                    self::forget( $repoClass , $key );
                }
            }
        }
    }

    private static function getCacheKey ( $class , $configString ) {
        return $class . '::' . config( self::CONFIG_FILE . ".$class.$configString" );
    }

    private static function getMethod ( $class , $configString ) {
        return config( self::CONFIG_FILE . ".$class.$configString" );
    }
}
