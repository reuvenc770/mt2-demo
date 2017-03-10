<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameCaptureDateInEfa extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::connection('attribution')->table('email_feed_assignments', function($table) {
            $table->renameColumn('capture_date', 'subscribe_date');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::connection('attribution')->table('email_feed_assignments', function($table) {
            $table->renameColumn('subscribe_date', 'capture_date');
        });

    }
}
