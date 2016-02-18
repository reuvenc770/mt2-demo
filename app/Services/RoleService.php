<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 2/8/16
 * Time: 10:58 AM
 */

namespace App\Services;


use Cartalyst\Sentinel\Sentinel;
use Cache;
use Illuminate\Cache\CacheManager;

class RoleService
{
    protected $authObject;
    protected $roleRepo;
    protected $cache;

    public function __construct(Sentinel $authObject, CacheManager $cache)
    {
        $this->authObject = $authObject;
        $this->roleRepo = $authObject->getRoleRepository();  //dumb but needed
        $this->cache = $cache;
    }


    public function getAllRoles()
    {
        return $this->roleRepo->all();
    }

    public function createRoleAddPermissions($input, $permissions){
        try {
            $role = $this->roleRepo->createModel()->create([
                'name' => $input['name'],
                'slug' => str_slug($input['name'], '-'),
            ]);
            foreach ($permissions as $permission) {
                $role->addPermission($permission);
            }
            $role->save();
        } catch(\Exception $e){
            throw new \Exception($e->getMessage());
        }
        $this->cache->tags('navigation')->flush();
        return true;

    }

    public function getRole($id){
        return $this->roleRepo->find($id);
    }

    public function updateRole($input,$permissions, $id){
        $role = $this->roleRepo->findById($id);
        $role->fill($input);
        $role->permissions = array();
        foreach ($permissions as $permission) {
            $role->addPermission($permission);
        }
        $role->save();
        $this->cache->tags('navigation')->flush();

    }

}