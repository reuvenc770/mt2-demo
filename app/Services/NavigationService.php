<?php

namespace App\Services;

use Cartalyst\Sentinel\Sentinel;
use Route;
use Cache;

class NavigationService {
    protected $auth;
    protected $currentUser;

    protected $router;
    protected $routeList;
    protected $menuList = [];

    protected $landingRoute;
    protected $currentRoute = [ "prefix" => "" , "name" => "" , "uri" => "" ];

    protected $cacheId;

    public function __construct ( Sentinel $auth , Route $router ) {
        $this->auth = $auth;
        $this->router = $router;
    }

    public function getMenu () {
        $userPresent = $this->loadUser();

        if ( $userPresent ) {
            $cachedMenu = Cache::get( $this->cacheId );

            if ( is_null( $cachedMenu ) ) {
                if ( empty( $this->menuList ) ) $this->loadMenu();

                return $this->menuList;
            } else {
                return $cachedMenu;
            }
        } else {
            return [];
        }
    } 

    protected function loadMenu () {
        $this->loadRoutes();

        foreach ( $this->routeList as $route ) {
            $this->loadPrefix( $route );
            $this->loadName( $route );
            $this->loadUri( $route );
            
            if (
                $this->isPrefixValid()
                && $this->isValidName()
                && $this->hasAccess()
            ) {
                $this->menuList []= $this->getCurrentMenuItem();
            }
        }

        Cache::forever( $this->cacheId , $this->menuList );
    }

    protected function loadUser () {
        $this->currentUser = $this->auth->getUser();

        if ( is_null( $this->currentUser ) ) return false;

        $userCollection = $this->currentUser->pluck( 'id' );
        $this->cacheId = 'nav-' . $userCollection->first();

        return true;
    }

    protected function loadRoutes () {
        $routeObj = $this->router;

        $this->landingRoute = $routeObj::currentRouteName();

        $this->routeList = $routeObj::getRoutes();
    }

    protected function loadPrefix ( $route ) {
        $this->currentRoute[ 'prefix' ] = $route->getPrefix();
    }

    protected function loadName ( $route ) {
        $this->currentRoute[ 'name' ] = $route->getName();
    }

    protected function loadUri ( $route ) {
        $this->currentRoute[ 'uri' ] = $route->getUri();
    }

    protected function isPrefixValid () {
        return ( preg_match( '/^\/(?!api)(?!role)(?!user)/' , $this->currentRoute[ 'prefix' ] ) === 1 );
    }

    protected function isValidName () {
        return ( preg_match( '/.{1,}[.]{1}(?!index)(?!add)(?!edit)(?!show)/' , $this->currentRoute[ 'name' ] ) === 1 );
    }

    protected function hasAccess () {
        return $this->currentUser->hasAccess( [ $this->currentRoute[ 'name' ] ] );
    }

    protected function getCurrentMenuItem () {
        return [
            "name" => $this->getMenuName() ,
            "uri" => $this->currentRoute[ 'uri' ] ,
            "active" => ( $this->currentRoute[ 'name' ] == $this->landingRoute ? 1 : 0 )
        ];
    }

    protected function getMenuName () {
        return trans( 'navigation.' . $this->currentRoute[ 'name' ] );
    }
}
