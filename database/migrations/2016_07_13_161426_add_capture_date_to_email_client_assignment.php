<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCaptureDateToEmailClientAssignment extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::connection('attribution')->table('email_client_assignments', function(Blueprint $table) {
            $table->date('capture_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::connection('attribution')->table('email_client_assignments', function(Blueprint $table) {
            $table->dropColumn('capture_date');
        });
    }
}
