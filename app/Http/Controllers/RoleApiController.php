<?php

namespace App\Http\Controllers;

use App\Facades\Permission;
use App\Services\RoleService;
use App\Services\PagePermissionService;
use Illuminate\Http\Request;
use Route;
use Laracasts\Flash\Flash;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Requests\RoleAddRequest;
use App\Http\Requests\RoleEditRequest;

class RoleApiController extends Controller
{
    protected $roleService;
    protected $pagePermissionService;

    public function __construct( RoleService $roleService , PagePermissionService $pagePermissionService )
    {
        $this->roleService = $roleService;
        $this->pagePermissionService = $pagePermissionService;
    }

    public function index()
    {
        $rawRolesList = $this->roleService->getAllRoles()->toArray();

        $roles = [];
        foreach ( $rawRolesList as $currentRole ) {
            $roles []= [
                "id" => $currentRole[ 'id' ] ,
                "slug" => $currentRole[ 'slug' ],
                "name" => $currentRole[ 'name' ]
            ];
        }

        return response()->json($roles);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function listAll()
    {
        return response()
            ->view( 'pages.role.role-index' );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view( 'pages.role.role-add' );

    }

    public function permissions () {
        $permissions = Permission::getAllPermissions();
        $response = [ "routes" => [ "general" => [] ] , "api" => [ "general" => [] ] ];

        foreach ( $permissions[ 'routes' ] as $index => $route ) {
            if ( $route[ 'name' ] == '' ) continue;

            $routeNameSections = explode( '.' , $route[ 'name' ] );

            $currentRoutePrefix = $routeNameSections[ 0 ];

            if ( count( $routeNameSections ) < 2 ) {
                $response[ 'routes' ][ 'general' ] []= $route[ 'name' ];
            } elseif ( in_array( $currentRoutePrefix , [ 'forget' , 'password' ] ) ) {
                $response[ 'routes' ][ 'password' ] []= $route[ 'name' ];
            } else {
                if ( !isset( $response[ 'routes' ][ $currentRoutePrefix ] ) ) {
                    $response[ 'routes' ][ $currentRoutePrefix ] = [];
                }

                $response[ 'routes' ][ $currentRoutePrefix ] []= $route[ 'name' ];
            }
        }

        foreach ( $permissions[ 'api' ] as $index => $api ) {
            $apiNameSections = explode( '.' , $api[ 'name' ]  );

            $currentApiPrefix = $apiNameSections[ 1 ];

            if ( in_array( $currentApiPrefix , [ 'jobEntry' , 'pager' , 'profile' , 'showinfo' ] ) ) {
                $response[ 'api' ][ 'general' ] []= $api[ 'name' ];
            } else {
                if ( !isset( $response[ 'api' ][ $currentApiPrefix ] ) ) {
                    $response[ 'api' ][ $currentApiPrefix ] = [];
                }

                $response[ 'api' ][ $currentApiPrefix ] []= $api[ 'name' ];
            }
        }

        return response()->json( $response );
    }

    public function getPermissionTree ( $id ) {
        $permissions = [];

        $role = $this->roleService->getRole($id);

        if ( !is_null( $role ) ) {
            $permissions = array_keys($role->permissions);
        }

        return response()->json( $this->pagePermissionService->getPermissionTree( $permissions ) );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(RoleAddRequest $request)
    {
        $permissions = $request->input('permissions');
        $input = $request->only('name');
        $this->roleService->createRoleAddPermissions($input, $permissions);
        Flash::success("Role was Successfully Created");
        return array("success" => (bool) true);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $role = $this->roleService->getRole($id);
        $role->permissions = array_keys($role->permissions);
        return $role;
    }

    /**
     * Show the form for editing the specified resource.
     *
     *
     * @return \Illuminate\Http\Response
     */
    public function edit()
    {
        return view('pages.role.role-edit');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(RoleEditRequest $request, $id)
    {
        $permissions = $request->input('permissions');
        $input = $request->only('name', 'slug');
        $this->roleService->updateRole($input, $permissions, $id);
        Flash::success("Role Successfully Updated");
        return array("success" => (bool) true);
    }


}
