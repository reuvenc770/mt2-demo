<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Http\Requests\RegistrationFormRequest;
use App\Repositories\UserRepositoryInterface;
use App\Services\UserService;
use Illuminate\Http\Request;
use Sentinel;
class RegistrationController extends Controller
{
    /**
     * @var $user
     */
    protected $userService;
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $roles = $this->userService->getAvailableRoles();
        return view('registration.create',array("roles" => $roles));
    }
    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(RegistrationFormRequest $request)
    {
        $roleID = $request->input('type');
        $input = $request->only('email', 'password', 'first_name', 'last_name');
        $this->userService->createAndRegisterUser($input, $roleID);
        return redirect('login')->withFlashMessage('User Successfully Created!');
    }
}