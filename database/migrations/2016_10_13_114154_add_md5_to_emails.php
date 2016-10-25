<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMd5ToEmails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('emails', function(Blueprint $table) {
            $table->char('lower_case_md5', 32)->default('')->after('email_domain_id');
            $table->char('upper_case_md5', 32)->default('')->after('lower_case_md5');

            $table->index('lower_case_md5', 'lower_case_md5');
            $table->index('upper_case_md5', 'upper_case_md5');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('emails', function(Blueprint $table) {

            // In MySQL, dropping a column drops it from any whole-column indices that it is part of
            $table->dropColumn('upper_case_md5');
            $table->dropColumn('lower_case_md5');
        });
    }
}
