<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSuppressionTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection( 'suppression' )->create('suppression_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string( 'name' );
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
        Schema::connection( 'suppression' )->drop('suppression_types');
    }
}
