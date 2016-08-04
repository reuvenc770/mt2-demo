<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFromsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('froms', function (Blueprint $table) {
            $table->increments('id');
            $table->string('from_line')->default('');
            $table->boolean('is_approved')->default(0);
            $table->boolean('status')->default(0);

            # Potential legacy fields
            $table->integer('offer_id')->default(0);
            $table->boolean('is_original')->default(0);
            $table->dateTime('date_approved')->nullable();
            $table->string('approved_by', 20)->nullable();
            $table->date('inactive_date')->nullable();
            $table->boolean('internal_approved_flag')->default(0);
            $table->dateTime('internal_date_approved')->nullable();
            $table->string('internal_approved_by', 20)->nullable();
            $table->boolean('copywriter')->default(0);
            $table->string('copywriter_name', 5)->nullable();
            $table->index('offer_id', 'offer_id');
            $table->index('inactive_date', 'inactive_date');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('froms');
    }
}
