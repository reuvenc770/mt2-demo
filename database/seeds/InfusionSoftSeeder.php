<?php

use Illuminate\Database\Seeder;
use App\Models\EspAccount;
use App\Models\SuppressionReason;
use App\Models\OAuthTokens;
use Carbon\Carbon;

class InfusionSoftSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $newEsp = new \App\Models\Esp();
        $newEsp->name = "InfusionSoft";
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
        $newAcc->account_name = 'IS001';
        $newAcc->custom_id = null;
        $newAcc->key_1 = ''; // To be provided by hand
        $newAcc->key_2 = '';
        $newAcc->created_at = Carbon::now();
        $newAcc->updated_at = Carbon::now();
        $newAcc->enable_stats = 1;
        $newAcc->enable_suppression = 1;
        $newAcc->deactivation_date = null;
        $newAcc->save();

        $oauth = new OAuthTokens();
        $oauth->access_token = '';
        $oauth->access_secret = '';
        $oauth->esp_account_id = $newAcc->id;
        $oauth->redirect_uri = '';
        $oauth->save();
    }
}