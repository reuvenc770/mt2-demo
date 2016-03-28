<?php

use Illuminate\Database\Seeder;

class new_actions extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        DB::connection('reporting_data')->statement("INSERT INTO action_types (id, name)
            VALUES
            ('7', 'unsubscriber'),
            ('8', 'complainer')");
    }
}
