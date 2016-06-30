<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAttributionActivitySchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection( 'attribution' )->create('attribution_activity_schedules', function (Blueprint $table) {
            $table->bigInteger( 'email_id' )->unsigned();
            $table->date( 'inactive_date' );
            $table->timestamps();

            $table->primary( 'email_id' );
            $table->index( 'inactive_date' );
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection( 'attribution' )->drop('attribution_activity_schedules');
    }
}
