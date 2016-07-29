<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMassAdjustmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::connection('reporting_data')->create('mass_adjustments', function (Blueprint $table){
            $table->integer('deploy_id')->default(0);
            $table->decimal('amount', 9, 2)->default(0.00);
            $table->date('date')->nullable();
            $table->timestamps();

            $table->primary('deploy_id');
            $table->index('date', 'date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::connection('reporting_data')->drop('mass_adjustments');
    }
}
