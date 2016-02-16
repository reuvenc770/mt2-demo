<?php

namespace App\Http\Controllers;

use App\Facades\Permission;
use App\Services\RoleService;
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

    public function __construct(RoleService $roleService)
    {
        $this->roleService = $roleService;
    }

    public function index()
    {
        $roleDisplay = array();
        $roles = $this->roleService->getAllRoles();
        foreach($roles as $role){
            $roleDisplay[] = array(
                $role->id,
                $role->slug,
                $role->name,
            );
        }
        return $roleDisplay;
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
        $permissions = Permission::getAllPermissions();
        return view('pages.role.role-add',array("permissions" => $permissions['routes'], "permissionsAPI" => $permissions['api']));
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
        $permissions = Permission::getAllPermissions();
        return view('pages.role.role-edit',array("permissions" => $permissions));
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
    }


}
