<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AdjustDomainsSwitchInuseToArecordlive extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $schema = config('database.connections.mysql.database');

        DB::statement( "ALTER TABLE {$schema}.domains MODIFY COLUMN in_use TINYINT(1) NOT NULL DEFAULT '1';" );

        Schema::table( 'domains' , function ( $table ) {
            $table->renameColumn( 'in_use' , 'live_a_record' );
        } );

        DB::table('domains')->update(['live_a_record' => 1]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table( 'domains' , function ( $table ) {
            $table->renameColumn( 'live_a_record' , 'in_use' );
        } );
    }
}
