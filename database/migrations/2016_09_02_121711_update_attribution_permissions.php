<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateAttributionPermissions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $oldPage = DB::table( 'pages' )
            ->select( 'id' )
            ->where( 'name' , 'client.attribution' )
            ->first();

        /**
         * Delete Old Page and Related Page Permissions
         */
        if ( !is_null( $oldPage ) ) {
            DB::table( 'page_permissions' )
                ->where( 'page_id' , $oldPage->id )
                ->delete();

            DB::table( 'pages' )
                ->where( 'id' , $oldPage->id )
                ->delete();
        }

        /**
         * Delete Permissions
         */
        foreach ( [ 'client.attribution' , 'api.attribution.store' , 'api.client.attribution.list' ] as $permissionName ) {
            $oldPermission = DB::table( 'permissions' )
                ->select( 'id' )
                ->where( 'name' , $permissionName )
                ->first();

            if ( !is_null( $oldPermission ) ) {
                DB::table( 'permissions' )
                    ->where( 'id' , $oldPermission->id )
                    ->delete();
            }
        }

        /**
         * Rename Permissions
         */
        DB::table( 'pages' )
            ->where( 'name' , 'attr.model.list' )
            ->update( [ 'name' => 'attribution.list' ] );

        DB::table( 'permissions' )
            ->where( 'name' , 'attr.model.list' )
            ->update( [ 'name' => 'attribution.list' ] );


        DB::table( 'permissions' )
            ->where( 'name' , 'attr.model.add' )
            ->update( [ 'name' => 'attributionModel.add' ] );

        DB::table( 'permissions' )
            ->where( 'name' , 'attr.model.edit' )
            ->update( [ 'name' => 'attributionModel.edit' ] );

        /**
         * Reassign Permissions
         */
        $newPage = DB::table( 'pages' )
            ->select( 'id' )
            ->where( 'name' , 'attribution.list' )
            ->first();

        if ( !is_null( $newPage ) ) {
            $permissionObj = DB::table( 'permissions' )
                ->select( 'id' )
                ->where( 'name' , 'api.attribution.bulk' )
                ->first();

            if ( !is_null( $permissionObj ) ) {
                DB::table( 'page_permissions' )
                    ->where( 'permission_id' , $permissionObj->id )
                    ->update( [ 'page_id' => $newPage->id ] );
            }
        }

        Artisan::call( 'permissions:update' , [
            '--grant' => true ,
            '--role' => 'admin' ,
            '--crudOperation' => 'read' ,
            '--page' => 'attribution.list'
        ] );

        Artisan::call( 'permissions:update' , [
            '--grant' => true ,
            '--role' => 'admin' ,
            '--crudOperation' => 'create' ,
            '--page' => 'attribution.list'
        ] );

        Artisan::call( 'permissions:update' , [
            '--grant' => true ,
            '--role' => 'admin' ,
            '--crudOperation' => 'update' ,
            '--page' => 'attribution.list'
        ] );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        /**
         * Put old page back
         */
        DB::table( 'pages' )
            ->insert( [ 'name' => 'client.attribution' ] );

        $newPage = DB::table( 'pages' )
            ->select( 'id' )
            ->where( 'name' , 'client.attribution' )
            ->first();

        if ( !is_null( $newPage ) ) {
            /**
             * Put old permission back.
             */
            foreach ( [ 'client.attribution' , 'api.attribution.store' , 'api.client.attribution.list' , 'api.attribution.bulk' ] as $permissionName ) {
                if ( $permissionName != 'api.attribution.bulk' ) {
                    DB::table( 'permissions' )
                        ->insert( [ 'name' => $permissionName ] );
                }

                $newPermission = DB::table( 'permissions' )
                    ->select( 'id' )
                    ->where( 'name' , $permissionName )
                    ->first();

                if ( !is_null( $newPermission ) ) {
                    DB::table( 'page_permissions' )
                        ->insert( [
                            'page_id' => $newPage->id ,
                            'permission_id' => $newPermission->id
                        ] );
                }
            }

            /**
             * Change model permissions back
             */
            DB::table( 'pages' )
                ->where( 'name' , 'attribution.list' )
                ->update( [ 'name' => 'attr.model.list' ] );

            DB::table( 'permissions' )
                ->where( 'name' , 'attributionModel.add' )
                ->update( [ 'name' => 'attr.model.add' ] );

            DB::table( 'permissions' )
                ->where( 'name' , 'attributionModel.edit' )
                ->update( [ 'name' => 'attr.model.edit' ] );

            Artisan::call( 'permissions:update' , [
                '--grant' => true ,
                '--role' => 'admin' ,
                '--crudOperation' => 'read' ,
                '--page' => 'client.attribution'
            ] );

            Artisan::call( 'permissions:update' , [
                '--grant' => true ,
                '--role' => 'admin' ,
                '--crudOperation' => 'create' ,
                '--page' => 'client.attribution'
            ] );
        }
    }
}
