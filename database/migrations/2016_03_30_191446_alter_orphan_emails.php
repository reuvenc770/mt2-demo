<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterOrphanEmails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table( 'orphan_emails' , function ( $table ) {
            $table->integer( 'adopt_attempts' )->default( 0 )->after( 'datetime' );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table( 'orphan_emails' , function ( $table ) {
            $table->dropColumn( 'adopt_attempts' );
        } );
    }
}
