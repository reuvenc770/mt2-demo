<?php

namespace App\Repositories;
use App\Models\EmailCampaignStatistic;
use DB;
use Carbon\Carbon;

class EmailCampaignStatisticRepo {
    protected $model;

    public function __construct(EmailCampaignStatistic $model){
        $this->model = $model;
    }

    public function massInsertActions($massData) {

        echo "Preparing to insert at " . microtime(true) . PHP_EOL;
        $insertList = [];

        foreach ($massData as $row) {
            $rowString = "('{$row['email_id']}', 
                '{$row['campaign_id']}', 
                '{$row['last_status']}', 
                '{$row['esp_first_open_datetime']}', 
                '{$row['esp_last_open_datetime']}',
                '{$row['esp_total_opens']}',
                '{$row['esp_first_click_datetime']}',
                '{$row['esp_last_click_datetime']}',
                '{$row['esp_total_clicks']}',
                '{$row['unsubscribed']}',
                NOW())";
            $insertList[]= $rowString;
        }

        $insertString = implode(',', $insertList);

        DB::connection('reporting_data')->insert(
            "INSERT INTO email_campaign_statistics 
            (email_id, deploy_id, last_status, esp_first_open_datetime,
            esp_last_open_datetime, esp_total_opens, esp_first_click_datetime,
            esp_last_click_datetime, esp_total_clicks, unsubscribed,
            updated_at) VALUES $insertString

            ON DUPLICATE KEY UPDATE
                email_id=email_id,
                deploy_id=deploy_id,
                last_status = VALUES(last_status),
                esp_first_open_datetime= IF(esp_first_open_datetime IS NULL, 
                                                VALUES(esp_first_open_datetime), 
                                                IF(esp_first_open_datetime < VALUES(esp_first_open_datetime), 
                                                    esp_first_open_datetime, 
                                                    VALUES(esp_first_open_datetime) )) ,
                esp_last_open_datetime = VALUES(esp_last_open_datetime),
                esp_total_opens = IFNULL(esp_total_opens, 0) + IFNULL(VALUES(esp_total_opens), 0),
                esp_first_click_datetime = IF(esp_first_click_datetime IS NULL, 
                                                VALUES(esp_first_click_datetime), 
                                                IF(esp_first_click_datetime < VALUES(esp_first_click_datetime), 
                                                    esp_first_click_datetime, 
                                                    VALUES(esp_first_click_datetime) )),
                esp_last_click_datetime = VALUES(esp_last_click_datetime),
                esp_total_clicks = IFNULL(esp_total_clicks, 0) + IFNULL(VALUES(esp_total_clicks), 0),
                unsubscribed = VALUES(unsubscribed),
                created_at=created_at,
                updated_at = NOW()"

        );
    }

    public function insertOrUpdate($row, $actionType) {

        // Currently need to insert/update. Later can just update.
        // the somewhat complicated update scheme likely requires a raw query

        switch ($actionType) {
            
            case "opener":
                DB::connection('reporting_data')->statement(
                    "INSERT INTO email_campaign_statistics 
                    (email_id, deploy_id, last_status, esp_first_open_datetime,
                    esp_last_open_datetime, esp_total_opens, created_at,
                    updated_at) VALUES (:email_id, :deploy_id, :action1, :dt1,
                    :dt2, 1, NOW(), NOW()) 

                    ON DUPLICATE KEY UPDATE
                        email_id=email_id,
                        deploy_id=deploy_id,
                        last_status = :action2,
                        esp_first_open_datetime=esp_first_open_datetime,
                        esp_last_open_datetime = :dt3,
                        esp_total_opens = esp_total_opens + 1,
                        created_at=created_at,
                        updated_at = NOW()",
                    array(
                        ':email_id' => $row['email_id'],
                        ':deploy_id' => $row['campaign_id'],
                        ':dt1' => $row['datetime'],
                        ':dt2' => $row['datetime'],
                        ':dt3' => $row['datetime'],
                        ':action1' => $actionType,
                        ':action2' => $actionType
                    )
                );
                break;

            case "clicker":

                /*
                    Danny Espaillat: "We should treat the opener and
                    clicker files as exclusing [sic]"
                
                    Thus, for clickers, increment opens as well
                */
                DB::connection('reporting_data')->statement(
                    "INSERT INTO email_campaign_statistics 
                    (email_id, deploy_id, last_status, esp_first_open_datetime,
                    esp_last_open_datetime, esp_total_opens, esp_first_click_datetime, 
                    esp_last_click_datetime, esp_total_clicks, created_at,
                    updated_at) VALUES (:email_id, :deploy_id, :action1, :dt1,
                    :dt2, 1, :dt3, :dt4, 1, NOW(), NOW()) 

                    ON DUPLICATE KEY UPDATE
                        email_id=email_id,
                        deploy_id=deploy_id,
                        last_status = :action2,
                        esp_first_open_datetime=esp_first_open_datetime,
                        esp_last_open_datetime = :dt5,
                        esp_total_opens = esp_total_opens + 1,
                        esp_first_click_datetime=esp_first_click_datetime,
                        esp_last_click_datetime = :dt6,
                        esp_total_clicks = esp_total_clicks + 1,
                        created_at=created_at,
                        updated_at = NOW()",
                    array(
                        ':email_id' => $row['email_id'],
                        ':deploy_id' => $row['campaign_id'],
                        ':dt1' => $row['datetime'],
                        ':dt2' => $row['datetime'],
                        ':dt3' => $row['datetime'],
                        ':dt4' => $row['datetime'],
                        ':dt5' => $row['datetime'],
                        ':dt6' => $row['datetime'],
                        ':action1' => $actionType,
                        ':action2' => $actionType
                    )
                );
                break;

            case "deliverable":
                DB::connection('reporting_data')->statement(
                    "INSERT INTO email_campaign_statistics 
                    (email_id, deploy_id, last_status, created_at,
                    updated_at) VALUES (:email_id, :deploy_id, :action1, NOW(), NOW()) 

                    ON DUPLICATE KEY UPDATE
                        email_id=email_id,
                        deploy_id=deploy_id,
                        last_status = :action2,
                        created_at=created_at,
                        updated_at = NOW()",
                    array(
                        ':email_id' => $row['email_id'],
                        ':deploy_id' => $row['campaign_id'],
                        ':action1' => $actionType,
                        ':action2' => $actionType
                    )
                );
                break;

            default:
                break;
        }
    }

    public function updateExternalData($emailId, $deployId, $data) {
        $this->model
            ->where('email_id', '=', $emailId)
            ->where('deploy_id', '=', $deployId)
            ->update($data);
    }

    public function updateUnsubStatus($email, $espInternalId) {
        $this->updateSuppressionStatus($email, $espInternalId, 'unsubscribed');
    }

    public function updateHardBounce($email, $espInternalId) {
        $this->updateSuppressionStatus($email, $espInternalId, 'hard_bounce');
    }

    protected function updateSuppressionStatus($email, $espInternalId, $field) {
        $dataSchema = config('database.connections.mysql.database');
        $this->model
             ->join("$dataSchema.emails as e", 'email_campaign_statistics.email_id', '=', 'e.id')
             ->join('standard_reports as sr', 'email_campaign_statistics.deploy_id', '=', 'sr.external_deploy_id')
             ->where('e.email_address', $email)
             ->where('sr.esp_internal_id', $espInternalId)
             ->update([
                 "email_campaign_statistics.$field" => 1,
                 "email_campaign_statistics.updated_at" => Carbon::now()->format('Y-m-d H:i:s')
             ]);
    }

    public function updateWithTrackingInfo($data) {

        $this->model
            ->where('email_id', '=', $data['email_id'])
            ->where('deploy_id', '=', $data['campaign_id'])
            ->update([
                'trk_first_click_datetime' => $data['first_click'],
                'trk_last_click_datetime' => $data['last_click'],
                'trk_total_clicks' => $data['clicks'],
                'user_agent_id' => $data['uas_id']
            ]);
    }

    public function updateWithContentServerInfo($data) {
        $this->model
            ->where('email_id', '=', $data['email_id'])
            ->where('deploy_id', '=', $data['sub_id'])
            ->update([
                'mt_first_open_datetime' => $data['first_open'],
                'mt_last_open_datetime' => $data['last_open'],
                'mt_total_opens' => $data['clicks'],
                'mt_first_click_datetime' => $data['first_click'],
                'mt_last_click_datetime' => $data['last_click'],
                'mt_total_clicks' => $data['clicks']
            ]);
    }

}