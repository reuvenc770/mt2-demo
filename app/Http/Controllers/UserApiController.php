<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileUpdate;
use App\Http\Requests\RegistrationFormRequest;
use App\Http\Requests\RegistrationEditFormRequest;
use Laracasts\Flash\Flash;
use Hash;
use Log;
class UserApiController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function listAll()
    {
        return response()
            ->view('pages.user.user-index');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json($this->userService->getAllUsersWithRolesNames());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $roles = $this->userService->getAvailableRoles();
        return view('pages.user.user-add', array("roles" => $roles), ["formType"=> 'add']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(RegistrationFormRequest $request)
    {
        $roles = $request->input('roles');
        $input = $request->only('email', 'username', 'password', 'first_name', 'last_name');
        $this->userService->createAndRegisterUser($input, $roles);
        Flash::success("User was Successfully Created");

    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return $this->userService->getUserWithRoles($id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!$this->userService->checkifUserExists($id)) {
            Flash::error("User {$id} does not exist");
            return redirect("/user");
        }
        $roles = $this->userService->getAvailableRoles();
        return view('pages.user.user-edit', array("roles" => $roles), ["formType"=> 'edit']);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(RegistrationEditFormRequest $request, $id)
    {
        $roles = $request->input('roles');
        $input = $request->only('email', 'username', 'first_name', 'last_name');
        $this->userService->updateUserAndRoles($input, $roles, $id);
        Flash::success("User Successfully Updated");
    }

    public function myProfile()
    {
        $user = \Sentinel::getUser();
        return response()
            ->view('pages.user.user-profile', array( "id" => $user->getUserId()));
    }

    public function updateProfile(ProfileUpdate $request, $id)
    {
        $roles = $request->input('roles');
        $newPass = $request->input('newpass');
        if(isset($newPass) || $newPass != ''){
            $request->merge(array('password' => $newPass));
            $input = $request->only('email', 'username', 'first_name', 'last_name', 'password');
            $this->userService->updateUserAndRoles($input, $roles, $id);
        } else {
            $input = $request->only('email', 'username', 'first_name', 'last_name');
            $this->userService->updateUserAndRoles($input, $roles, $id);

        }

        Flash::success("User Successfully Updated");
    }

}
