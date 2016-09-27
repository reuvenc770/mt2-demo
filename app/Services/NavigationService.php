<?php

namespace App\Services;

use Sentinel;
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

    public function getMenuHtml () {
        $userPresent = $this->loadUser();
        if ( $userPresent ) {
            $cachedMenu = Cache::tags("navigation")->get( $this->cacheId );
            if ( is_null( $cachedMenu ) ) {
                $this->loadMenu();
                $sideNav = view( 'layout.side-nav', [ 'menuItems' => $this->menuList ] )->render() ;

                Cache::tags('navigation')->forever( $this->cacheId , $sideNav);

                return $sideNav;
            } else {
                return $cachedMenu;
            }
        } else {
            return view( 'layout.side-nav-guest' );
        }
    }

    public function getMenuIcon ( $route ) {
        return config( 'menuicons.' . $route , '' );
    }

    protected function loadMenu () {
        $this->loadRoutes();

        foreach ( $this->routeList as $route ) {
            $prefix = str_replace("/","",$route->getPrefix());
            $name = $route->getName();
            $this->loadPrefix( $route );
            $this->loadName( $route );
            $this->loadUri( $route );

            if (
                $this->isPrefixValid()
                && $this->isValidName()
                && $this->hasAccess()
                && $route->getName() != ""
            ) {
                if(substr($name, -4) == "list") {
                    $this->menuList[$prefix] = $this->getCurrentMenuItem();
                    $this->menuList[$prefix]['children'] = array();
                } else {
                    if(isset($this->menuList[$prefix]['children'])) {
                        array_push($this->menuList[$prefix]['children'], $this->getCurrentMenuItem());
                    }
                    else {
                        $this->menuList[$prefix] = $this->getCurrentMenuItem();
                    }
                }
            }
        }
    }

    protected function loadUser () {
        $this->currentUser = Sentinel::getUser();
        if ( is_null( $this->currentUser ) ) return false;


        $this->cacheId = 'nav-' . $this->currentUser->getUserId();

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
        return ( preg_match( '/^\/(?!api)/' , $this->currentRoute[ 'prefix' ] ) === 1 );
    }

    protected function isValidName () {
        return ( preg_match( '/.{1,}[.]{1}(?!index)(?!edit)(?!show)(?!preview)(?!export)(?!downloadhtml)/' , $this->currentRoute[ 'name' ] ) === 1 );
    }

    protected function hasAccess () {
        return $this->currentUser->hasAccess( [ $this->currentRoute[ 'name' ] ] );
    }

    protected function getCurrentMenuItem () {
        return [
            "name" => $this->getMenuName() ,
            "uri" => $this->currentRoute[ 'uri' ] ,
            "active" => ( $this->currentRoute[ 'name' ] == $this->landingRoute ? 1 : 0 ),
            "prefix" => str_replace("/","",$this->currentRoute['prefix']),
            "icon" => $this->getMenuIcon($this->currentRoute[ 'uri' ])
        ];
    }

    protected function getMenuName () {
        return trans( 'navigation.' . $this->currentRoute[ 'name' ] );
    }
}
