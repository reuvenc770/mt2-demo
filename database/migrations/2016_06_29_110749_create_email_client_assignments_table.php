<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmailClientAssignmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('email_client_assignments', function (Blueprint $table) {
            $table->integer( 'email_id' )->unsigned();
            $table->integer( 'client_id' )->unsigned();
            $table->timestamps();

            $table->primary( [ 'email_id' , 'client_id' ] );
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('email_client_assignments');
    }
}
