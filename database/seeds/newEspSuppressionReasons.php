<?php

use Illuminate\Database\Seeder;
use App\Models\SuppressionReason;
class newEspSuppressionReasons extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $esps = \App\Models\Esp::all();
        foreach ($esps as $esp) {
            if($esp->suppressionReasons()->count() == 0) {
                $reason = new SuppressionReason();
                $reason->display_status = "Suppression because of ESP Unsub - {$esp->name}";
                $reason->esp_id = $esp->id;
                $reason->display = 1;
                $reason->suppression_type = \App\Models\Suppression::TYPE_UNSUB;
                $reason->save();

                $reason = new SuppressionReason();
                $reason->display_status = "Suppression because of ESP Hardbounce - {$esp->name}";
                $reason->esp_id = $esp->id;
                $reason->display = 1;
                $reason->suppression_type = \App\Models\Suppression::TYPE_HARD_BOUNCE;
                $reason->save();

                $reason = new SuppressionReason();
                $reason->display_status = "Suppression because of ESP Complaint - {$esp->name}";
                $reason->esp_id = $esp->id;
                $reason->display = 1;
                $reason->suppression_type = \App\Models\Suppression::TYPE_COMPLAINT;
                $reason->save();
            }
        }
    }
}
