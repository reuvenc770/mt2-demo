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
        $pickup->stop_point = 
    }
}
