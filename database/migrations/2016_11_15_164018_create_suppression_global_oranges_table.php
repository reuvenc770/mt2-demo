<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSuppressionGlobalOrangesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connetion( 'suppression' )->create('suppression_global_orange', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned();
            $table->string( 'email_adress' );
            $table->dateTime( 'suppress_datetime' );
            $table->integer( 'reason_id' )->unsigned();
            $table->integer( 'type_id' )->unsigned();
            $table->timestamps();

            $table->unique( 'email_address' );
            $table->index( 'reason_id' );
            $table->index( 'type_id' );
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connetion( 'suppression' )->drop('suppression_global_orange');
    }
}
