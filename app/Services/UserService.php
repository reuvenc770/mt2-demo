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



    public function createAndRegisterUser($userFields, $roleID){

        try {
            $user = $this->authObject->registerAndActivate($userFields);
            $role = $this->authObject->findRoleById($roleID);
            $role->users()->attach($user);
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

}