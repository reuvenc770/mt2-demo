<?php

use Illuminate\Database\Seeder;
use App\Models\EspAccount;
use App\Models\SuppressionReason;
class AWeberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $newEsp = new \App\Models\Esp();
        $newEsp->name = "AWeber";
        $newEsp->save();

        $reason = new SuppressionReason();
        $reason->display_status = "Suppression because of ESP Unsub - {$newEsp->name}";
        $reason->esp_id = $newEsp->id;
        $reason->display = 1;
        $reason->suppression_type = \App\Models\Suppression::TYPE_UNSUB;
        $reason->save();

        $reason = new SuppressionReason();
        $reason->display_status = "Suppression because of ESP Hardbounce - {$newEsp->name}";
        $reason->esp_id = $newEsp->id;
        $reason->display = 1;
        $reason->suppression_type = \App\Models\Suppression::TYPE_HARD_BOUNCE;
        $reason->save();

        $reason = new SuppressionReason();
        $reason->display_status = "Suppression because of ESP Complaint - {$newEsp->name}";
        $reason->esp_id = $newEsp->id;
        $reason->display = 1;
        $reason->suppression_type = \App\Models\Suppression::TYPE_COMPLAINT;
        $reason->save();
    }
}