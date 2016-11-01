<?php

use Illuminate\Database\Seeder;
use App\Models\EtlPickup;

class SuppressionPickupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        $etlPickup = new EtlPickup();
        $etlPickup->name = 'LastVendorSuppId';
        $etlPickup->stop_point = 4129832586;
        $etlPickup->save();
    }
}
