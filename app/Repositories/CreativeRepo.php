<?php

namespace App\Repositories;

use App\Models\Creative;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;
use Log;
class CreativeRepo extends AbstractDataSyncRepo{
  
    private $model;


    public function __construct(Creative $model) {
        $this->model = $model;

    } 

    public function updateOrCreate($data) {
            $this->addToBulkRecords($data);
            if($this->isReadyToSave()) {

                    $this->bulkInsert();

                $this->clearBulkRecords();
            }
    }

    public function getCreativeOfferClickRate($offerId) {
        $schema = config("database.connections.reporting_data.database");
        return $this->model
            ->leftJoin("$schema.offer_creative_maps as ocm", 'creatives.id', '=', 'ocm.creative_id')
            ->leftJoin("$schema.creative_clickthrough_rates as crate", 'crate.creative_id', '=', 'creatives.id')
            ->where('ocm.offer_id', $offerId)
            ->where('creatives.status', 'A')
            ->where('creatives.approved', 'Y')
            ->groupBy('creatives.id', 'name')
            ->orderBy("click_rate", 'desc')
            ->select(DB::raw("creatives.id, creatives.file_name as name, ROUND(SUM(IFNULL(clicks, 0)) / SUM(IFNULL(opens, 0)) * 100, 3) AS click_rate"))
            ->get();
    }

    public function getCreativesByOffer($offerId)
    {
        $schema = config("database.connections.reporting_data.database");
        return $this->model//LAME
            ->leftJoin("$schema.offer_creative_maps as ocm", 'creatives.id', '=', 'ocm.creative_id')
            ->where('ocm.offer_id', $offerId)
            ->where('creatives.status', 'A')
            ->where('creatives.approved', 'Y')
            ->get();
    }

    public function bulkInsert(){
        DB::insert(
            "INSERT INTO creatives (id,file_name,approved,status,creative_html,is_original,trigger_flag,creative_date,
inactive_date,unsub_image,default_subject,default_from,image_directory,thumbnail,date_approved,approved_by,content_id,
header_id,body_content_id,style_id,replace_flag,mediactivate_flag,hitpath_flag,comm_wizard_c3,comm_wizard_cid,
comm_wizard_progid,cr,landing_page,is_internally_approved,internal_date_approved,internal_approved_by,copywriter,
copywriter_name,original_html,deleted_by,host_images,needs_processing)
            VALUES
                        " . join(' , ', $this->getBulkRecords()) . "
            ON DUPLICATE KEY UPDATE
            id = id, file_name = VALUES(file_name),creative_html = VALUES(creative_html),approved = VALUES(approved),
            status = VALUES(status),is_original = VALUES(is_original),trigger_flag = VALUES(trigger_flag),
            creative_date = VALUES(creative_date),inactive_date = VALUES(inactive_date),unsub_image = VALUES(unsub_image),
            default_subject = VALUES(default_subject),default_from = VALUES(default_from),
            image_directory = VALUES(image_directory),thumbnail = VALUES(thumbnail),date_approved = VALUES(date_approved),
            approved_by = VALUES(approved_by),content_id = VALUES(content_id),header_id = VALUES(header_id),
            body_content_id = VALUES(body_content_id),style_id = VALUES(style_id),replace_flag = VALUES(replace_flag),
            mediactivate_flag = VALUES(mediactivate_flag),hitpath_flag = VALUES(hitpath_flag),
            comm_wizard_c3 = VALUES(comm_wizard_c3),comm_wizard_cid = VALUES(comm_wizard_cid),
            comm_wizard_progid = VALUES(comm_wizard_progid),cr = VALUES(cr),landing_page = VALUES(landing_page),
            is_internally_approved = VALUES(is_internally_approved),internal_date_approved = VALUES(internal_date_approved),
            internal_approved_by = VALUES(internal_approved_by),copywriter = VALUES(copywriter),
            copywriter_name = VALUES(copywriter_name),original_html = VALUES(original_html),deleted_by = VALUES(deleted_by),
            host_images = VALUES(host_images),needs_processing = VALUES(needs_processing)");
    }

}
