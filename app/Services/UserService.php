<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 1/28/16
 * Time: 1:30 PM
 */

namespace App\Services;



use Cartalyst\Sentinel\Sentinel;


class UserService
{
    protected $userRepo;
    protected $authObject;

    public function __construct(Sentinel $authObject)
    {
        $this->authObject = $authObject;
        $this->userRepo = $authObject->getUserRepository();  //dumb but needed
    }



    public function createAndRegisterUser($userFields, $roles){

        try {
            $user = $this->authObject->registerAndActivate($userFields);
            foreach($roles as $role) {
                $roleObject = $this->authObject->findRoleById($role);
                $roleObject->users()->attach($user);
            }
        } catch (\Exception $e){
            throw new \Exception($e->getMessage());
        }
        return true;
    }

    //move to RolesService
    public function getAvailableRoles(){
        $user = $this->authObject->getUser();
        $roles = $this->authObject->getRoleRepository()->all();
        $filtered = $roles->filter(function ($role) use ($user) {
            if(!$user->hasAccess('register.admin') && $role->slug =="admins"){
               return false;
            } elseif(!$user->hasAccess('register.gtdev') && $role->slug =="gtdev"){
                return false;
            } else{
                return true;
            }});
        return $filtered;
    }

    public function getAllUsersWithRolesNames()
    {
       $users = $this->userRepo->select('id','email', 'first_name', 'last_name', 'last_login' )
           ->with(['roles' => function ($query) {
               $query->addSelect('name');
            }, 'activations'])->get();
        foreach($users as $user) {
            $user->roles->transform(function ($item, $key) {
                return $item->name;
            });
        }
        return $users;
    }

    public function getUserWithRoles($id){
        $user = $this->userRepo->select('id','email', 'first_name', 'last_name', 'last_login' )
            ->with(['roles' => function ($query) {
                $query->addSelect('id');
            }])->get()->find($id);
            $user->roles->transform(function ($item, $key) {
                return $item->id;
            });
        return $user;
    }

    public function updateUserAndRoles($input, $roles, $id){
        $user = $this->userRepo->findById($id);
        $user->roles()->detach(); //remove all roles.
        foreach($roles as $role) {
            $roleObject = $this->authObject->findRoleById($role);
            $roleObject->users()->attach($user);
         }
        $this->userRepo->update($user,$input);
    }
}