<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 4/18/16
 * Time: 3:26 PM
 */

namespace App\Services;
use App\Services\AbstractReportService;
use App\Repositories\ReportRepo;
use App\Services\EmailRecordService;
use App\Services\API\RelevantToolsApi;
use App\Services\Interfaces\IDataService;

class RelevantToolsReportService extends AbstractReportService implements IDataService
{
    public function __construct(ReportRepo $reportRepo, RelevantToolsApi $api , EmailRecordService $emailRecord )
    {
        parent::__construct($reportRepo, $api , $emailRecord );
    }

    public function retrieveApiStats($data)
    {
        throw new \Exception("RT does not use the API");
    }

    public function insertApiRawStats($data)
    {
        throw new \Exception("RT does not use the API");
    }

    public function mapToRawReport($data)
    {
        throw new \Exception("RT does not use the API");
    }

    public function mapToStandardReport($data)
    {
        return array(
            'campaign_name' => $data['campaign_name'],
            'external_deploy_id' => $this->getDeployIDFromName($data['campaign_name']),
            'm_deploy_id' => $this->getDeployIDFromName($data['campaign_name']),
            'esp_account_id' => $data['esp_account_id'],
            'esp_internal_id' => "",
            'datetime' => $data['datetime'],
            'name' => "",
            'subject' => "",
            'from' => "",
            'from_email' => "",
            'delivered' => $data[ 'total_sent' ],
            'bounced' => "",
            'e_opens' => $data[ 'total_open' ],
            'e_opens_unique' => "",
            'e_clicks' => "",
            'e_clicks_unique' => "",
        );
    }

    public function pushRecords(array $records, $targetId) {}
}