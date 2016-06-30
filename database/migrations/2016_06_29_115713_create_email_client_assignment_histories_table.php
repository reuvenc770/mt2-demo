<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmailClientAssignmentHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection( 'attribution' )->create('email_client_assignment_histories', function (Blueprint $table) {
            $table->integer( 'email_id' )->unsigned();
            $table->integer( 'prev_client_id' )->unsigned();
            $table->integer( 'new_client_id' )->unsigned();
            $table->timestamps();

            $table->index( 'email_id' );
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection( 'attribution' )->drop('email_client_assignment_histories');
    }
}
