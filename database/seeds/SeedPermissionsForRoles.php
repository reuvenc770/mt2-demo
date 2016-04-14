<?php

use Illuminate\Database\Seeder;

class SeedPermissionsForRoles extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = DB::table( 'roles' )
            ->whereIn( 'slug' , [ 'gtdev' , 'admin' ] )
            ->where( 'permissions' , 'NOT LIKE' , '%api.role.permissions%' )
            ->get(); 

        foreach ( $roles as $roleIndex => $role ) {
            $currentPermissions = json_decode( $role->permissions , true );
            $currentPermissions [ 'api.role.permissions' ] = true;

            DB::table( 'roles' )
                ->where( 'id' , $role->id )
                ->update( [ 'permissions' => json_encode( $currentPermissions ) ] );
        }
    }
}
