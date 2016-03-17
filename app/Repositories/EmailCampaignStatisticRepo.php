<?php

namespace App\Repositories;
use App\Models\EmailCampaignStatistic;
use DB;

class EmailCampaignStatistic {
    protected $model;

    public function __construct(EmailCampaignStatistic $model){
        $this->model = $model;
    }

    public function insertOrUpdate($row, $actionType) {
        /*
            1. Get action type.
            2. Find if row exists:
                a. If it does not, insert and set appropriate rows
                b. else: update appropriate rows
        */
        switch ($actionType) {
            // NOW, ARE LISTS DUPLICATES?
            case "opener":
                DB::connection('reporting_data')->statement(
                    "INSERT INTO email_campaign_statistic 
                    (email_id, campaign_id, last_status, esp_first_open_datetime,
                    esp_last_open_datetime, esp_total_opens, created_at,
                    updated_at) VALUES (:email_id, :campaign_id, 'opener', :dt,
                    :dt, 1, NOW(), NOW()) 

                    ON DUPLICATE KEY UPDATE
                        email_id=email_id,
                        campaign_id=campaign_id,
                        last_status = 'opener',
                        esp_first_open_datetime=esp_first_open_datetime,
                        esp_last_open_datetime = :dt,
                        esp_total_opens = esp_total_opens + 1,
                        created_at=created_at,
                        updated_at = NOW()",
                    array(
                        'email_id' => $row['email_id'],
                        'campaign_id' => $row['campaign_id'],
                        'dt' => $row['datetime']
                    )
                );
                break;

            case "clicker":

                /*
                    Danny Espaillat: "We should treat the opener and
                    clicker files as exclusing [sic]"
                
                    Thus, for clickers, set 
                */

                DB::connection('reporting_data')->statement(
                    "INSERT INTO email_campaign_statistic 
                    (email_id, campaign_id, last_status, esp_first_open_datetime,
                    esp_last_open_datetime, esp_total_opens, created_at,
                    updated_at) VALUES (:email_id, :campaign_id, 'clicker', :dt,
                    :dt, 1, NOW(), NOW()) 

                    ON DUPLICATE KEY UPDATE
                        email_id=email_id,
                        campaign_id=campaign_id,
                        last_status = 'clicker',
                        esp_first_open_datetime=esp_first_open_datetime,
                        esp_last_open_datetime = :dt,
                        esp_total_opens = esp_total_opens + 1,
                        created_at=created_at,
                        updated_at = NOW()",
                    array(
                        'email_id' => $row['email_id'],
                        'campaign_id' => $row['campaign_id'],
                        'dt' => $row['datetime']
                    )
                );
                break;

            case
            default:
                break;
        }
    }



}