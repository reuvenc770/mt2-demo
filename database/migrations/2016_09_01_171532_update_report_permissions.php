<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateReportPermissions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        #Need to switch the pages
        DB::table( 'pages' )
            ->where( 'name' , 'attr.report.view' )
            ->update( [ 'name' => 'report.list' ] );

        #Need to switch the permissions
        DB::table( 'permissions' )
            ->where( 'name' , 'api.attribution.report' )
            ->update( [ 'name' => 'api.report.getRecords' ] );

        DB::table( 'permissions' )
            ->where( 'name' , 'attr.report.view' )
            ->update( [ 'name' => 'report.list' ] );

        DB::table( 'permissions' )
            ->where( 'name' , 'attr.report.export' )
            ->update( [ 'name' => 'report.export' ] );

        Artisan::call( 'permissions:update' , [
            '--grant' => true ,
            '--role' => 'admin' ,
            '--crudOperation' => 'read' ,
            '--page' => 'report.list'
        ] );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table( 'pages' )
            ->where( 'name' , 'report.list' )
            ->update( [ 'name' => 'attr.report.view' ] );

        DB::table( 'permissions' )
            ->where( 'name' , 'api.report.getRecords' )
            ->update( [ 'name' => 'api.attribution.report' ] );

        DB::table( 'permissions' )
            ->where( 'name' , 'report.list' )
            ->update( [ 'name' => 'attr.report.view' ] );

        DB::table( 'permissions' )
            ->where( 'name' , 'report.export' )
            ->update( [ 'name' => 'attr.report.export' ] );

        Artisan::call( 'permissions:update' , [
            '--grant' => true ,
            '--role' => 'admin' ,
            '--crudOperation' => 'read' ,
            '--page' => 'attr.report.view'
        ] );
    }
}
