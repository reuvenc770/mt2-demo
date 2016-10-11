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
    protected $sectionList = [];

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
            $cachedMenu = null;
            if ( is_null( $cachedMenu ) ) {
                $this->loadMenu();

                $template = 'bootstrap.layout.side-nav';

                $sideNav = view( $template , [ 'menuItems' => $this->menuList ] )->render() ;

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
                ( ( $this->isPrefixValid() && $this->isValidName() ) || $this->isException() )
                && $this->hasAccess()
            ) {
                if ( !$this->getSectionName() )  {
                    continue;
                }

                if ( !isset( $this->menuList[ $this->getSectionName() ] ) ) {
                    $this->menuList[ $this->getSectionName() ] = [
                        'name' => $this->getSectionName() ,
                        'children' => [] ,
                        "icon" => $this->getMenuIcon( $this->getSectionName() )
                    ];
                }

                $this->menuList[ $this->getSectionName() ][ 'children' ] []= $this->getCurrentMenuItem();
            }
        }

        ksort( $this->menuList );

        foreach ( $this->menuList as &$section ) {
            uasort( $section[ 'children' ] , array( $this , 'compareMenuItems' ) );
        }
    }

    protected function compareMenuItems ( $itemA , $itemB ) {
        if ( $itemA[ 'name' ] == $itemB[ 'name' ] ) { return 0; }

        return $itemA[ 'name' ] < $itemB[ 'name' ] ? -1 : 1;
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
        return ( preg_match( '/^\/(?!api)(?!tools)/' , $this->currentRoute[ 'prefix' ] ) === 1 );
    }

    protected function isValidName () {
        return ( preg_match( '/(list)$/' , $this->currentRoute[ 'name' ] ) === 1 );
    }

    protected function isException () {
        return ( preg_match( '/^(?!api).+(?:bulksuppression|jobs|recordlookup)/' , $this->currentRoute[ 'name' ] ) === 1 );
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
        ];
    }

    protected function getSectionName () {
        return trans( 'navigationSections.' . $this->currentRoute[ 'name' ] );
    }

    protected function getMenuName () {
        return trans( 'navigation.' . $this->currentRoute[ 'name' ] );
    }
}
