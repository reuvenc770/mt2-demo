<?php

use Illuminate\Database\Seeder;
use App\Models\EtlPickup;

class AttributionEtlPickupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        $pickup = new EtlPickup();
        $pickup->name = 'AttributionJob';
        $pickup->stop_point = 1470166204; //'2016-08-02 15:30:04'
        $pickup->save();
    }
}
