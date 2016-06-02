<?php

use Illuminate\Database\Seeder;
use App\Models\SuppressionReason;
class SuppressionReasons extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $startData = '[
 {
   "legacy_status":"CA Address",
   "display_status":"Suppression because of CASL Address",
      "display" : 1,
      "suppression_type":0
 },
 {
   "legacy_status":"Flagged Words",
   "display_status":"Suppression because of Flagged Words",
      "display" : 1,
      "suppression_type":0
 },
 {
   "legacy_status":"Suppression because of ESP - AllInclusive",
   "display_status":"Suppression because of ESP Unsub - Legacy ESP",
      "display" : 0,
      "suppression_type":1
 },
 {
   "legacy_status":"Suppression because of ESP - Bounce",
   "display_status":"Suppression because of Hard Bounce - Legacy ESP",
      "display" : 0,
      "suppression_type":2
 },

 {
   "legacy_status":"Suppression because of ESP - ConstantContact AllInclusive",
   "display_status":"Suppression because of ESP Unsub - Legacy ESP",
      "display" : 0,
      "suppression_type":1
 },
 {
   "legacy_status":"Suppression because of ESP - DEPRECATED",
   "display_status":"Suppression because of ESP Unsub - DEPRECATED",
      "display" : 0,
      "suppression_type":1
 },
 {
   "legacy_status":"Suppression because of ESP - EmailVision bounce",
   "display_status":"Suppression because of Hard Bounce - EV",
      "display" : 1,
      "suppression_type":2
 },
 {
   "legacy_status":"Suppression because of ESP - EmailVision complaint",
   "display_status":"Suppression because of ESP Complaint - EV",
      "display" : 1,
      "suppression_type":3
 },
 {
   "legacy_status":"Suppression because of ESP - KobeMail bounce",
   "display_status":"Suppression because of Hard Bounce- Legacy ESP",
      "display" : 0,
      "suppression_type":2
 },
 {
   "legacy_status":"Suppression because of ESP - KobeMail complaint",
   "display_status":"Suppression because of ESP Complaint - Legacy ESP",
      "display" : 0,
      "suppression_type":3
 },

 {
   "legacy_status":"Suppression because of ESP - MyNewsletterBuilder bounce",
   "display_status":"Suppression because of Hard Bounce- Legacy ESP",
      "display" : 0,
      "suppression_type":2
 },
 {
   "legacy_status":"Suppression because of ESP - MyNewsletterBuilder complaint",
   "display_status":"Suppression because of ESP Complaint - Legacy ESP",
      "display" : 0,
      "suppression_type":3
 },
 {
   "legacy_status":"Suppression because of ESP - Netatlantic bounce",
   "display_status":"Suppression because of Hard Bounce- Legacy ESP",
      "display" : 0,
      "suppression_type":2
 },
 {
   "legacy_status":"Suppression because of ESP - Netatlantic complaint",
   "display_status":"Suppression because of ESP Complaint - Legacy ESP",
      "display" : 0,
      "suppression_type":3
 },

 {
   "legacy_status":"Suppression because of ESP - SMTP bounce",
   "display_status":"Suppression because of Hard Bounce - Legacy ESP",
      "display" : 0,
      "suppression_type":2
 },
 {
   "legacy_status":"Suppression because of ESP - SMTP complaint",
   "display_status":"Suppression because of ESP Complaint - Legacy ESP",
      "display" : 0,
      "suppression_type":3
 },
 {
   "legacy_status":"Suppression because of ImpressionWise",
   "display_status":"Suppression because of 3rd Party Service - Impressionwise",
      "display" : 1,
      "suppression_type":0
 },
 {
   "legacy_status":"Suppression because of Zeta bounce",
   "display_status":"Suppression because of Hard Bounce- Legacy ESP",
      "display" : 0,
      "suppression_type":2
 },
 {
   "legacy_status":"Suppression because of Zeta complaint",
   "display_status":"Suppression because of ESP Complaint - Legacy ESP",
      "display" : 0,
      "suppression_type":3
 },
 {
   "legacy_status":"Suppression because of a bounce",
   "display_status":"Suppression because of Hard Bounce - Legacy",
      "display" : 0,
      "suppression_type":2
 },
 {
   "legacy_status":"Suppression because of a complaint",
   "display_status":"Suppression because of ESP Complaint - Legacy",
      "display" : 0,
      "suppression_type":3
 },
 {
   "legacy_status":"Suppression via the mailing tool - Advertiser Screamer",
   "display_status":"Suppression because of Advertiser Screamer",
      "display" : 1,
      "suppression_type":0
 },
 {
   "legacy_status":"Suppression via the mailing tool - DEPRECATED",
   "display_status":"Suppression because of MT1 Unsubscribe -DEPRECATED",
      "display" : 0,
      "suppression_type":1
 },
 {
   "legacy_status":"Suppression via the mailing tool - IP Provider Complaint",
   "display_status":"Suppression because of IP Provider Complaint",
      "display" : 1,
      "suppression_type":3
 },
 {
   "legacy_status":"Suppression via the mailing tool - List Owner Screamer",
   "display_status":"Suppression because of List Owner Screamer",
      "display" : 1,
      "suppression_type":0
 },
 {
   "legacy_status":"Suppression via the mailing tool - Spamtrap",
   "display_status":"Suppression because of Spamtrap",
   "display" : 0,
      "suppression_type":0
 },
 {
   "legacy_status":"Suppression via the mailing tool - Supersketch Spamtrap",
   "display_status":"Suppression because of Spamtrap",
      "display" : 1,
      "suppression_type":0
 },
 {
   "legacy_status":"Suppression via unsub account",
   "display_status":"Suppression because of MT1 Unsubscribe",
   "display" : 0,
      "suppression_type":1
 }
]';
        //Insert the base mappings
        $startData = json_decode($startData, true);
        foreach( $startData as $data){
            SuppressionReason::create($data);
        }
        //make one for each ESP avaiable.
        $esps = \App\Models\Esp::all();
        foreach ($esps as $esp) {
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

        //Make modifcations for old specific ESP
        $campaigner = \App\Models\Esp::where('name', "Campaigner")->first();
        $reason = SuppressionReason::where('esp_id', $campaigner->id)
                            ->where('suppression_type', \App\Models\Suppression::TYPE_HARD_BOUNCE)->first();
        $reason->legacy_status = "Suppression because of ESP - Campaigner bounce";
        $reason->save();

        $reason = SuppressionReason::where('esp_id', $campaigner->id)
            ->where('suppression_type', \App\Models\Suppression::TYPE_COMPLAINT)->first();
        $reason->legacy_status = "Suppression because of ESP - Campaigner complaint";
        $reason->save();

        $maro = \App\Models\Esp::where('name', "Maro")->first();
        $reason = SuppressionReason::where('esp_id', $maro->id)
            ->where('suppression_type', \App\Models\Suppression::TYPE_UNSUB)->first();
        $reason->legacy_status = "Suppression because of ESP - Maro";
        $reason->save();

        $publicators = \App\Models\Esp::where('name', "Publicators")->first();
        $reason = SuppressionReason::where('esp_id', $publicators->id)
            ->where('suppression_type', \App\Models\Suppression::TYPE_UNSUB)->first();
        $reason->legacy_status = "Suppression because of ESP - PUB";
        $reason->save();



        //now lets fix the suppression table
        DB::statement("UPDATE suppressions s INNER JOIN esp_accounts eac ON eac.id = s.esp_account_id INNER JOIN suppression_reasons sr ON eac.esp_id = sr.esp_id SET s.reason_id = sr.id WHERE s.type_id = sr.suppression_type and s.reason_id = 0");
    }

}
