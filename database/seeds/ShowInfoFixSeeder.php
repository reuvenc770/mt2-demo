<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Cache;

class ShowInfoFixSeeder extends Seeder
{
    const PERMISSION_NAME = 'tools.list';

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        #Add permission
        $permissionId = DB::table( 'permissions' )
            ->insertGetId( [ 'name' => self::PERMISSION_NAME , 'crud_type' => 'read' ] );

        #Add page permissions mapping
        $date = Carbon::now();
        DB::table( 'page_permissions' )
            ->insert( [ "page_id" => 1 , "permission_id" => $permissionId , 'created_at' => $date , 'updated_at' => $date ] );

        #Assign permissions to roles
        Artisan::call( 'permissions:update' , [
            '--grant' => true ,
            '--permissionName' => self::PERMISSION_NAME ,
            '--role' => 'admin'
        ] );

        Artisan::call( 'permissions:update' , [
            '--grant' => true ,
            '--permissionName' => self::PERMISSION_NAME ,
            '--role' => 'gtdev'
        ] );

        Artisan::call( 'permissions:update' , [
            '--grant' => true ,
            '--permissionName' => self::PERMISSION_NAME ,
            '--role' => 'standard'
        ] );

        Cache::tags( [ 'navigation' ] )->flush();
    }
}
