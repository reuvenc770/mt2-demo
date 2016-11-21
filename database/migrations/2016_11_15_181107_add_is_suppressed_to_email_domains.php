<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsSuppressedToEmailDomains extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('email_domains', function(Blueprint $table) {
            $table->boolean('is_suppressed')->default(0)->after('domain_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('email_domains', function(Blueprint $table) {
            $table->dropColumn('is_suppressed');
        });
    }
}
