<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCrudTypeToPermissions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table( 'permissions' , function ( Blueprint $table ) {
            $table->enum( 'crud_type' , [ 'create' , 'read' , 'update' , 'delete' ] )->default( 'read' )->after( 'name' );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table( 'permissions' , function ( Blueprint $table ) {
            $table->dropColumn( 'crud_type' );
        } );
    }
}
