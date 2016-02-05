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
        $creds = array(
            'first_name' => "Admin",
            'last_name'  => 'McAdmin',
            'email'     => 'admin@mt2.com',
            'password'  => 'admin'
        );
        $user = Sentinel::registerAndActivate($creds);
        $role = Sentinel::findRoleByName('Admins');
        $role->users()->attach($user);

        $role2 = Sentinel::findRoleByName('GTDev');
        $role2->users()->attach($user);
    }
}
