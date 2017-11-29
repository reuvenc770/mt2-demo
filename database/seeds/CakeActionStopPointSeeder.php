<?php

use Illuminate\Database\Seeder;
use App\Models\EtlPickup;

class CakeActionStopPointSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        $pickup = new EtlPickup();
        $pickup->name = 'CakeActions';
        $pickup->stop_point = 10865130; //'2017-11-09'
        $pickup->save();
    }
}
