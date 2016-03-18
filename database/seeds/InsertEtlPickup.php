<?php

use Illuminate\Database\Seeder;
use App\Models\EtlPickup;

class InsertEtlPickup extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        $x = new EtlPickup();
        $x->name = 'PopulateEmailCampaignStats';
        $x->stop_point = 0;
        $x->save();
    }
}
