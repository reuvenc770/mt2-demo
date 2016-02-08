<?php

use Illuminate\Database\Seeder;

class UserRoles extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //i am super proud of myself, i was going to make a static list.  i Know its too many but for now its fine
        $routeCollection = Route::getRoutes();
        $routlist = array();

        foreach ($routeCollection as $value) {
            $routlist[] = array("name" => $value->getName());
        }

       \App\Models\Permission::insert($routlist);

        $roleAdmin = Sentinel::getRoleRepository()->createModel()->create([
            'name' => 'Global Tech Devs',
            'slug' => 'gtdev',
        ]);
        $roleGTD = Sentinel::getRoleRepository()->createModel()->create([
            'name' => 'Admin',
            'slug' => 'admin',
        ]);

        $creds = array(
            'first_name' => "Admin",
            'last_name'  => 'McAdmin',
            'email'     => 'admin@mt2.com',
            'password'  => 'admin'
        );

        foreach($routlist as $route){
            $roleAdmin->addPermission($route['name']);
            $roleAdmin->save();
            $roleGTD->addPermission($route['name']);
            $roleGTD->save();

        }
        $user = Sentinel::registerAndActivate($creds);
        $roleAdmin->users()->attach($user);
        $roleGTD->users()->attach($user);




    }
}
