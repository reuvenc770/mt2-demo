<?php

namespace App\Services;

use App\Repositories\NavigationParentRepo;
use App\Repositories\PermissionRepo;
use Sentinel;
use Route;
use Cache;

class NavigationService
{
    protected $auth;
    protected $currentUser;

    protected $router;
    protected $routeList;
    protected $menuList = [];
    protected $sectionList = [];
    protected $sectionRepo;
    protected $permissionRepo;

    protected $landingRoute;
    protected $currentRoute = ["prefix" => "", "name" => "", "uri" => ""];

    protected $cacheId;

    public function __construct(Sentinel $auth, Route $router, PermissionRepo $permissionRepo, NavigationParentRepo $parent)
    {
        $this->auth = $auth;
        $this->router = $router;
        $this->permissionRepo = $permissionRepo;
        $this->sectionRepo = $parent;
    }

    public function getMenuHtml()
    {
        $userPresent = $this->loadUser();
        if ($userPresent) {
            $cachedMenu = Cache::tags("navigation")->get($this->cacheId);
            if (is_null($cachedMenu)) {
                $this->loadMenu();
                $template = 'layout.side-nav';
                $sideNav = view($template, ['menuItems' => $this->menuList])->render();

                Cache::tags('navigation')->forever($this->cacheId, $sideNav);

                return $sideNav;
            } else {
                return $cachedMenu;
            }
        } else {
            return view('layout.side-nav-guest');
        }
    }

    public function getMenuHtmlBootStrap()
    {
        $userPresent = $this->loadUser();
        if ($userPresent) {
            $cachedMenu = Cache::tags("navigation-bootstrap")->get( $this->cacheId );
            if (is_null($cachedMenu)) {
                $this->loadMenu();
                $template = 'bootstrap.layout.side-nav';

                $sideNav = view($template, ['menuItems' => $this->menuList])->render();

                Cache::tags('navigation-bootstrap')->forever($this->cacheId, $sideNav);

                return $sideNav;
            } else {
                return $cachedMenu;
            }
        } else {
            return view('layout.side-nav-guest');
        }
    }

    public function getMenuIcon($route)
    {
        return config('menuicons.' . $route, '');
    }

    protected function loadMenu()
    {
        $this->loadRoutes();
        $sections = $this->sectionRepo->getAllSections();

        foreach ($sections as $section) {

            $this->menuList[$section->name]['name'] = $section->name;
            $this->menuList[$section->name]['glyth'] = $section->glyth;

            $permissions = $this->permissionRepo->getAllPermissionsWithParent($section->id);
            foreach ($permissions as $permission) {
                $route = $this->routeList->getByName($permission->name);
                $this->loadPrefix($route);
                $this->loadName($route);
                $this->loadUri($route);

                if ($this->hasAccess()) {

                    $this->menuList[$section->name]['children'] [] = $this->getCurrentMenuItem();
                }
            }
        }


    }

    protected function compareMenuItems($itemA, $itemB)
    {
        if ($itemA['name'] == $itemB['name']) {
            return 0;
        }

        return $itemA['name'] < $itemB['name'] ? -1 : 1;
    }

    protected function loadUser()
    {
        $this->currentUser = Sentinel::getUser();
        if (is_null($this->currentUser)) return false;

        $this->cacheId = 'nav-' . $this->currentUser->getUserId();

        return true;
    }

    protected function loadRoutes()
    {
        $routeObj = $this->router;

        $this->landingRoute = $routeObj::currentRouteName();

        $this->routeList = $routeObj::getRoutes();
    }

    protected function loadPrefix($route)
    {
        $this->currentRoute['prefix'] = $route->getPrefix();
    }

    protected function loadName($route)
    {
        $this->currentRoute['name'] = $route->getName();
    }

    protected function loadUri($route)
    {
        $this->currentRoute['uri'] = $route->getUri();
    }

    protected function isPrefixValid()
    {
        return (preg_match('/^\/(?!api)(?!tools)/', $this->currentRoute['prefix']) === 1);
    }

    protected function isValidName()
    {
        return (preg_match('/(list)$/', $this->currentRoute['name']) === 1);
    }

    protected function isException()
    {
        $exceptionString = implode('|', trans('navigationExceptions.list'));

        return (preg_match('/^(?!api).+(?:' . $exceptionString . ')/', $this->currentRoute['name']) === 1);
    }

    protected function hasAccess()
    {
        return $this->currentUser->hasAccess([$this->currentRoute['name']]);
    }

    protected function getCurrentMenuItem()
    {
        return [
            "name" => $this->getMenuName(),
            "uri" => $this->currentRoute['uri'],
            "prefix" => str_replace("/", "", $this->currentRoute['prefix']),
        ];
    }

    protected function getSectionName()
    {
        return trans('navigationSections.' . $this->currentRoute['name']);
    }

    protected function getMenuName()
    {
        return trans('navigation.' . $this->currentRoute['name']);
    }


    public function getMenuTreeJson()
    {
        $this->loadRoutes();
        $parents = $this->sectionRepo->getAllSections();
        $returnArray = array();
        foreach ($parents as $section) {
            $permissionsArray = array();
            $permissions = $this->permissionRepo->getAllPermissionsWithParent($section->id);
            foreach ($permissions as $permission) {
                $route = $this->routeList->getByName($permission->name);
                $permissionsArray[] = ["id" => $permission->id, "parent" => $permission->parent, "name" => trans('navigation.' . $route->getName())];
            }
            $returnArray[] = ["id" => $section->id, "name" => $section->name, "childrenItems" => $permissionsArray];
        }
        return $returnArray;
    }

    public function getValidRoutesWithNoParent()
    {
        $this->loadRoutes();
        $permissionsArray = array();
        $permissions = $this->permissionRepo->getAllOrphanPermissions();

        foreach ($permissions as $permission) {
            $route = $this->routeList->getByName($permission->name);
            $exceptionString = implode('|', trans('navigationSkip.list'));
            if ($route) {
                if (preg_match('/^\/(?!api)/', $route->getPrefix())  &&
                    !preg_match('/(?:' . $exceptionString . ')/', $route->getName())) {
                    $permissionsArray[] = ["id" => $permission->id, "name" => trans('navigation.' . $route->getName())];
                }
            }

        }
        return $permissionsArray;
    }

    public function updateNavigation($navigation)
    {
        $parentRank = 1;
        $childrenToRemoveParents = array();
        foreach ($navigation as $parentItem) {
            $this->sectionRepo->updateRank($parentItem['id'], $parentRank);
            $childRank = 1;
            foreach ($parentItem['childrenItems'] as $childItem) {
                $this->permissionRepo->updateParentAndRank($childItem['id'], $parentItem['id'], $childRank);
                $childrenToRemoveParents[] = $childItem['id'];
                $childRank++;
            }
            $this->permissionRepo->removeParents($childrenToRemoveParents);
            $parentRank++;
        }
        return true;
    }
}
