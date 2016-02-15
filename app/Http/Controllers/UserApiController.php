<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegistrationFormRequest;
use App\Http\Requests\RegistrationEditFormRequest;
use Laracasts\Flash\Flash;

class UserApiController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService){
        $this->userService  = $userService;
    }

    public function listAll() {
        return response()
            ->view( 'pages.user.user-index' );
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = $this->userService->getAllUsersWithRolesNames();
        $return = array();
        foreach ($users as $user){
            $return[] = array(
                $user->id,
                $user->email,
                $user->first_name,
                $user->last_name,
                implode(',',$user->roles->toArray()),
                $user->activations ? "active" : 'deactivated',
                $user->last_login
            );
        }
        return $return;

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $roles = $this->userService->getAvailableRoles();
        return view('pages.user.user-add',array("roles" => $roles));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(RegistrationFormRequest $request)
    {
        $roles = $request->input('roles');
        $input = $request->only('email', 'password', 'first_name', 'last_name');
        $this->userService->createAndRegisterUser($input, $roles);
        Flash::success("User was Successfully Created");

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return $this->userService->getUserWithRoles($id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if(!$this->userService->checkifUserExists($id)){
            Flash::error("User {$id} does not exist");
            return redirect("/user");
        }
        $roles = $this->userService->getAvailableRoles();
        return view('pages.user.user-edit',array("roles" => $roles));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(RegistrationEditFormRequest $request, $id)
    {
        $roles = $request->input('roles');
        $input = $request->only('email', 'first_name', 'last_name');
        $this->userService->updateUserAndRoles($input, $roles, $id);
        Flash::success("User Successfully Updated");
    }

}
