<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAttributionExpirationSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attribution_expiration_schedules', function (Blueprint $table) {
            $table->integer( 'email_id' )
            $table->date( 'expiration_date' );
            $table->timestamps();

            $table->primary( 'email_id' );
            $table->index( 'expiration_date' );
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('attribution_expiration_schedules');
    }
}
