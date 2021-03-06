<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 1/28/16
 * Time: 1:30 PM
 */

namespace App\Services;

use Cartalyst\Sentinel\Sentinel;
use App\Models\User;
use Hash;

/**
 * Class UserService
 * @package App\Services
 */
class UserService
{
    /**
     * @var \Cartalyst\Sentinel\Users\UserRepositoryInterface
     */
    protected $userRepo;

    protected $reminderRepo;

    /**
     * @var Sentinel
     */
    protected $authObject;

    /**
     * UserService constructor.
     * @param Sentinel $authObject
     */
    public function __construct(Sentinel $authObject)
    {
        $this->authObject = $authObject;
        $this->userRepo = $authObject->getUserRepository();  //dumb but needed
        $this->reminderRepo = $authObject->getReminderRepository();
    }


    /**
     * @param $userFields
     * @param $roles
     * @return bool
     * @throws \Exception
     */
    public function createAndRegisterUser($userFields, $roles)
    {
        try {
            $user = $this->authObject->registerAndActivate($userFields);
            foreach ($roles as $role) {
                $roleObject = $this->authObject->findRoleById($role);
                $roleObject->users()->attach($user);
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
        return true;
    }

    //move to RolesService
    /**
     * @return mixed
     */
    public function getAvailableRoles()
    {
        $user = $this->authObject->getUser();
        $roles = $this->authObject->getRoleRepository()->all();
        $filtered = $roles->filter(function ($role) use ($user) {
            if (!$user->inRole('admin') && $role->slug == "admin") {
                return false;
            } elseif (!$user->inRole('gtdev') && $role->slug == "gtdev") {
                return false;
            } else {
                return true;
            }
        });
        return $filtered;
    }

    /**
     * @return mixed
     */
    public function getAllUsersWithRolesNames()
    {
        $users = $this->userRepo->select('id', 'email','username', 'first_name', 'last_name', 'last_login')
            ->with(['roles' => function ($query) {
                $query->addSelect('name');
            }, 'activations'])->get();
        foreach ($users as $user) {
            $user->roles->transform(function ($item, $key) {
                return $item->name;
            });
        }
        return $users;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getUserWithRoles($id)
    {
        $user = $this->userRepo->select('id', 'email','username', 'first_name', 'last_name', 'last_login')
            ->with(['roles' => function ($query) {
                $query->addSelect('id');
            }])->get()->find($id);
        $user->roles->transform(function ($item, $key) {
            return $item->id;
        });
        return $user;
    }


    /**
     * @param $input
     * @param $roles
     * @param $id
     */
    public function updateUserAndRoles($input, $roles, $id)
    {
        $user = $this->userRepo->findById($id);
        if($roles) {
            $user->roles()->detach(); //remove all roles.
            foreach ($roles as $role) {
                $roleObject = $this->authObject->findRoleById($role);
                $roleObject->users()->attach($user);
            }
        }
        $this->userRepo->update($user, $input);
    }


    public function checkifUserExists($id){
        return !is_null($this->userRepo->findById($id));
    }

    public function findByUsername ( $username ) {
        return $this->userRepo->findByCredentials( [ 'username' => $username ] );
    }

    public function findById ( $id ) {
        return $this->userRepo->findById( $id );
    }

    public function findByEmail ( $email ) {
        return $this->userRepo->findByCredentials( [ 'email' => $email ] );
    }

    public function resetPassword ( User $user , $password ) {
        $response = [ "status" => true , "errorMessages" => [] ];
        $isSamePassword = Hash::check( $password , $user->getUserPassword() );

        if ( $isSamePassword ) {
            $response[ 'status' ] = false;
            $response[ 'errorMessages' ] []= "New password is the same.";

            return $response;
        }

        $reminder = $this->reminderRepo->create( $user );
        
        if ( !$this->reminderRepo->complete( $user , $reminder[ 'code' ] , $password ) ) {
            $response[ 'status' ] = false;
            $response[ 'errorMessages' ] []= "Failed to reset password.";

            return $response;
        }
        
        if ( !$this->authObject->activate( $user ) ) {
            $response[ 'status' ] = false;
            $response[ 'errorMessages' ] []= "Failed to activate user.";
        }

        return $response;
    }
}
