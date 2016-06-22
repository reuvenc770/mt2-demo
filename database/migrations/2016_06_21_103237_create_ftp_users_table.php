<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFtpUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ftp_users', function (Blueprint $table) {
            $table->increments('id');
            $table->string( 'username' );
            $table->string( 'password' );
            $table->string( 'host' )->default( 'localhost' );
            $table->text( 'directory' );
            $table->string( 'service' )->default( '' );
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('ftp_users');
    }
}
