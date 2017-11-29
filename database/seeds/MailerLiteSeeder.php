<?php

use Illuminate\Database\Seeder;
use App\Models\EspAccount;
use App\Models\SuppressionReason;
use Carbon\Carbon;

class MailerLiteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $newEsp = new \App\Models\Esp();
        $newEsp->name = "MailerLite";
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

        $newAcc = new EspAccount();
        $newAcc->esp_id = $newEsp->id;
        $newAcc->account_name = 'ML001';
        $newAcc->custom_id = null;
        $newAcc->key_1 = ''; // To be provided by hand
        $newAcc->key_2 = '';
        $newAcc->created_at = Carbon::now();
        $newAcc->updated_at = Carbon::now();
        $newAcc->enable_stats = 1;
        $newAcc->enable_suppression = 1;
        $newAcc->deactivation_date = null;
        $newAcc->save();
    }
}