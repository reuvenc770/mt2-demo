<?php
/**
 * User: rbertorelli
 * Date: 1/27/16
 */

namespace App\Services;

use App\Facades\DeployActionEntry;
use App\Repositories\ReportRepo;
use App\Services\API\MaroApi;
use App\Services\AbstractReportService;
use Illuminate\Support\Facades\Event;
use App\Events\RawReportDataWasInserted;
use App\Services\Interfaces\IDataService;
use App\Facades\Suppression;
use Illuminate\Queue\InteractsWithQueue;
use App\Exceptions\JobException;
use Carbon\Carbon;

/**
 * Class BlueHornetReportService
 * @package App\Services
 */
class MaroReportService extends AbstractReportService implements IDataService
{
    use InteractsWithQueue;

    protected $actions = ['opens', 'clicks', 'complaints', 'unsubscribes', 'bounces'];
    public $pageType = 'opens';
    public $pageNumber = 1;
    public $currentPageData = array();

    public function __construct(ReportRepo $reportRepo, MaroApi $api, EmailRecordService $emailRecord)
    {
        parent::__construct($reportRepo, $api, $emailRecord);
    }

    public function retrieveApiStats($date)
    {
        $this->api->setDate($date);
        $outputData = array();

        $this->api->constructApiUrl();
        $firstData = $this->api->sendApiRequest();
        $firstData = $this->processGuzzleResult($firstData);
        try {
            $outputData = array_merge($outputData, $firstData);
        } catch (\Exception $e){
            $jobException = new JobException('Failed to merge array' . $e->getMessage(), JobException::NOTICE);
            throw $jobException;
        }
        if (sizeof($firstData) > 0) {
            $pages = (int)$firstData[0]['total_pages'];

            if ($pages > 0) {
                $i = 0;
                while ($i <= $pages) {
                    $this->api->constructApiUrl($i);
                    $data = $this->api->sendApiRequest();
                    $data = $this->processGuzzleResult($data);
                    $outputData = array_merge($outputData, $data);
                    $i++;
                }
            }
        }

        $completeData = array();
        foreach ($outputData as $id => $campaign) {
            $metadata = $this->retrieveSingleCampaignStats( $campaign['campaign_id'] );

            $campaign['from_name'] = $metadata['from_name'];
            $campaign['from_email'] = $metadata['from_email'];
            $campaign['subject'] = $metadata['subject'];
            $campaign['unique_opens'] = $metadata['unique_opens'];
            $campaign['unique_clicks'] = $metadata['unique_clicks'];
            $campaign['unsubscribes'] = $metadata['unsubscribed'];
            $campaign['complaints'] = $metadata['complaint'];
            $completeData[] = $campaign;
        }

        return $completeData;
    }

    public function retrieveSingleCampaignStats ( $campaignId ) {
        $this->api->constructAdditionalInfoUrl( $campaignId );
        $return = $this->api->sendApiRequest();
        return $this->processGuzzleResult( $return );
    }

    public function splitTypes($processState)
    {
        if ('rerun' === $processState['pipe']) {
            $typeList = [];
            // data in $processState['campaign']

            if (1 == $processState['campaign']->delivers) {
                $typeList[] = "delivered";
            }
            if (1 == $processState['campaign']->opens) {
                $typeList[] = 'opens';
            }
            if (1 == $processState['campaign']->clicks) {
                $typeList[] = 'clicks';
            }
            if (1 == $processState['campaign']->unsubs) {
                $typeList[] = 'unsubscribes';
            }
            if (1 == $processState['campaign']->bounces) {
                $typeList[] = 'bounces';
            }
        }
        else {
            $typeList = ['opens', 'clicks', 'complaints', 'unsubscribes', 'bounces'];
        }
        return $typeList;
    }

    public function savePage(&$processState, $map)
    {
        $type = "";
        $internalIds = array();
        try {
            switch ($processState['recordType']) {
                case 'opens' :
                    foreach ($processState['currentPageData'] as $key => $opener) {
                        $deployId = isset($map[$opener['campaign_id']]) ? (int)$map[$opener['campaign_id']] : 0;
                        $this->emailRecord->queueDeliverable(
                            self::RECORD_TYPE_OPENER,
                            $opener['contact']['email'],
                            $this->api->getId(),
                            $deployId,
                            $opener['campaign_id'],
                            $opener['recorded_at']
                        );
                    $internalIds[] = $opener['campaign_id'];
                    }
                    $type = "open";
                    break;

                case 'clicks' :
                    foreach ($processState['currentPageData'] as $key => $clicker) {
                        $deployId = isset($map[$clicker['campaign_id']]) ? (int)$map[$clicker['campaign_id']] : 0;
                        $this->emailRecord->queueDeliverable(
                            self::RECORD_TYPE_CLICKER,
                            $clicker['contact']['email'],
                            $this->api->getId(),
                            $deployId,
                            $clicker['campaign_id'],
                            $clicker['recorded_at']
                        );
                        $internalIds[] = $clicker['campaign_id'];
                    }

                    $type = "click";
                    break;

                case 'unsubscribes' :
                    foreach ($processState['currentPageData'] as $key => $unsub) {
                        Suppression::recordRawUnsub(
                            $this->api->getId(),
                            $unsub['contact']['email'],
                            $unsub['campaign_id'],
                            $unsub['recorded_on']
                        );
                        $internalIds[] = $unsub['campaign_id'];
                    }

                    $type = "optout";
                    break;

                case 'bounces' :
                    foreach ($processState['currentPageData'] as $key => $bounce) {
                        Suppression::recordRawHardBounce(
                            $this->api->getId(),
                            $bounce['contact']['email'],
                            $bounce['campaign_id'],
                            $bounce['recorded_on']
                        );
                        $internalIds[] = $bounce['campaign_id'];
                    }
                    $type = "bounce";
                    break;

                case 'complaints' :
                    foreach ($processState['currentPageData'] as $key => $complainer) {
                        Suppression::recordRawComplaint(
                            $this->api->getId(),
                            $complainer['contact']['email'],
                            $complainer['campaign_id'],
                            $complainer['recorded_on']
                        );
                        $internalIds[] = $complainer['campaign_id'];
                    }
                    $type = "complaint";
                    break;

                case 'delivered':
                    foreach ($processState['currentPageData'] as $key => $delivered) {
                        $deployId = isset($map[$delivered['campaign_id']]) ? (int)$map[$delivered['campaign_id']] : 0;
                        $this->emailRecord->queueDeliverable(
                            self::RECORD_TYPE_DELIVERABLE,
                            $delivered['email'],
                            $this->api->getId(),
                            $deployId,
                            $delivered['campaign_id'],
                            Carbon::parse($delivered['created_at'])
                        );
                        $internalIds[] = $delivered['campaign_id'];
                    }
                    $type = "deliverable";
                    break;
            }
        } catch (\Exception $e) {
            DeployActionEntry::recordFailedRunArray($this->api->getEspAccountId(), array_unique($internalIds), $type);
            $jobException = new JobException('Failed to retrieve records. ' . $e->getMessage(), JobException::NOTICE);
            $jobException->setDelay(180);
            throw $jobException;
        }
        $this->emailRecord->massRecordDeliverables();
        DeployActionEntry::recordSuccessRunArray($this->api->getEspAccountId(), array_unique($internalIds), $type);
    }


    public function saveActionPage(&$processState, $map)
    {
        $type = "";
        $internalIds = array();

        switch ($processState['recordType']) {

            case 'delivered':
                $datetimeField = 'created_at';
                $type = self::RECORD_TYPE_DELIVERABLE;
                $deployActionType = 'deliverable';
                break;

            case 'opens':
                $datetimeField = 'recorded_at';
                $type = self::RECORD_TYPE_OPENER;
                $deployActionType = 'open';
                break;

            case 'clicks':
                $datetimeField = 'recorded_at';
                $type = self::RECORD_TYPE_CLICKER;
                $deployActionType = 'click';
                break;

            default:
                throw new \Exception("Inappropriate type record type {$processState['recordType']} in saveActionPage"); // THIS SHOULD BE SOMETHING ELSE
        }

        try {
            foreach ($processState['currentPageData'] as $key => $record) {
                $deployId = isset($map[$record['campaign_id']]) ? (int)$map[$record['campaign_id']] : 0;
                $this->emailRecord->queueDeliverable(
                    $type,
                    $record['email'],
                    $this->api->getId(),
                    $deployId,
                    $record['campaign_id'],
                    Carbon::parse($record[$datetimeField])
                );
                $internalIds[] = $record['campaign_id'];
            }
        } catch (\Exception $e) {
            DeployActionEntry::recordFailedRunArray($this->api->getEspAccountId(), array_unique($internalIds), $deployActionType);
            $jobException = new JobException('Failed to retrieve records. ' . $e->getMessage(), JobException::NOTICE);
            $jobException->setDelay(180);
            throw $jobException;
        }
        $this->emailRecord->massRecordDeliverables();
        DeployActionEntry::recordSuccessRunArray($this->api->getEspAccountId(), array_unique($internalIds), $deployActionType);
    }

    public function shouldRetry()
    {
        return false; #releases if guzzle result is not HTTP 200
    }

    public function getUniqueJobId(&$processState)
    {
        $jobId = (isset($processState['jobId']) ? $processState['jobId'] : '');

        if (!isset($processState['jobIdIndex'])) {
            $processState['jobIdIndex'] = 0;
        }

        $filterIndex = $processState['currentFilterIndex'];
        $pipe = $processState['pipe'];

        if ($pipe == 'default' && $filterIndex == 1) {
            $jobId .= '::Pipe-' . $pipe . '::' . $processState['recordType'] . '::Page-' . (isset($processState['pageNumber']) ? $processState['pageNumber'] : 1);
        } elseif ($pipe == 'delivered' && $filterIndex == 1) {
            $jobId .= (isset($processState['campaign']) ? '::Pipe-' . $pipe . '::Campaign-' . $processState['campaign']->esp_internal_id : '');
        } elseif ('rerun' === $pipe && isset( $processState[ 'campaign' ]) && 2 == $filterIndex) {
            $jobId = '-page:' . $this->pageNumber;
        }

        $processState['jobIdIndex'] = $processState['currentFilterIndex'];
        $processState['jobId'] = $jobId;

        return $jobId;
    }

    public function setPageType($pageType)
    {
        if (in_array($pageType, ['opens', 'clicks', 'complaints', 'unsubscribes', 'bounces'])) {
            $this->pageType = $pageType;
        }
    }

    public function setPageNumber($pageNumber)
    {
        $this->pageNumber = $pageNumber;
    }

    public function getPageNumber()
    {
        return $this->pageNumber;
    }

    public function nextPage()
    {
        $this->pageNumber++;
    }

    public function pageHasData()
    {
        $this->api->setDeliverableLookBack();
        $this->api->constructDeliverableUrl($this->pageType, $this->pageNumber);

        $data = $this->api->sendApiRequest();
        $data = $this->processGuzzleResult($data);
        if (empty($data)) {
            return false;
        } else {
            $this->currentPageData = $data;

            return true;
        }
    }

    public function pageHasCampaignData($processState)
    {

        $campaignId = $processState['campaign']->esp_internal_id;
        $actionType = $processState['recordType'];

        $this->api->setDeliverableLookBack();
        $this->api->setActionUrl($campaignId, $actionType, $this->pageNumber);

        $data = $this->api->sendApiRequest();
        $data = $this->processGuzzleResult($data);

        if (empty($data)) {
            return false;
        } else {
            $this->currentPageData = $data;
            return true;
        }

    }

    public function getPageData()
    {
        return $this->currentPageData;
    }

    protected function processGuzzleResult($data)
    {
        if ($data->getStatusCode() != 200) {
            throw new JobException('API call failed.', JobException::NOTICE);
        }

        $data = $data->getBody()->getContents();
        return json_decode($data, true);
    }

    public function insertApiRawStats($data)
    {
        $convertedDataArray = [];
        $espAccountId = $this->api->getEspAccountId();
        foreach ($data as $id => $row) {
            $row['esp_account_id'] = $espAccountId;
            $convertedReport = $this->mapToRawReport($row);
            $this->insertStats($espAccountId, $convertedReport);

            if ( !is_null( $convertedReport[ 'sent_at' ] ) ) {
                $convertedDataArray[] = $convertedReport;
            }
        }

        Event::fire(new RawReportDataWasInserted($this, $convertedDataArray));
    }

    public function mapToStandardReport($data)
    {
        $deployId = $this->parseSubID($data['name']);
        return array(
            'campaign_name' => $data['name'],
            'external_deploy_id' => $deployId,
            'm_deploy_id' => $deployId,
            'esp_account_id' => $data['esp_account_id'],
            'esp_internal_id' => $data['internal_id'],
            'datetime' => $data['sent_at'],
            #'name' => $data['name'],
            'subject' => $data['subject'],
            'from' => $data['from_name'],
            'from_email' => $data['from_email'],
            'e_sent' => $data['sent'],
            'delivered' => $data['delivered'],
            'bounced' => (int)$data['bounce'],
            'optouts' => $data['unsubscribes'],
            'e_opens' => $data['open'],
            'e_opens_unique' => $data['unique_opens'],
            'e_clicks' => $data['click'],
            'e_clicks_unique' => $data['unique_clicks'],
        );
    }

    public function mapToRawReport($data)
    {
        return array(
            'status' => $data['status'],
            'esp_account_id' => $data['esp_account_id'],
            'internal_id' => (int)$data['campaign_id'],
            'name' => $data['name'],
            'sent' => (int)$data['sent'],
            'delivered' => (int)$data['delivered'],
            'open' => (int)$data['open'],
            'click' => (int)$data['click'],
            'bounce' => (int)$data['bounce'],
            'send_at' => $data['send_at'],
            'sent_at' => $data['sent_at'],
            'maro_created_at' => $data['created_at'],
            'maro_updated_at' => $data['updated_at'],
            'from_name' => $data['from_name'],
            'from_email' => $data['from_email'],
            'subject' => $data['subject'],
            'unique_opens' => $data['unique_opens'],
            'unique_clicks' => $data['unique_clicks'],
            'unsubscribes' => $data['unsubscribes'],
            'complaints' => $data['complaints'],
        );
    }

    public function pullUnsubsEmailsByLookback($lookback)
    {
        $this->setPageType("unsubscribes");
        $this->setPageNumber(1);
        $return = array();
        while ($this->pageHasData()) {
            $records = $this->getPageData();
            $return = array_merge($return, $records);
            $this->nextPage();
        }
        return $return;
    }

    public function insertUnsubs($data, $espAccountId)
    {
        foreach ($data as $entry) {
            Suppression::recordRawUnsub($espAccountId, $entry['contact']['email'], $entry['campaign_id'], $entry['recorded_on']);
        }
    }

    public function pushRecords(array $records, $targetId) {}

    public function addContact($record, $listId) {
        $this->api->addContact($record, $listId);
    }

    public function getMissingCampaigns ( $espAccountId ) {
        $fullCampaignList = [];

        try {
            $this->api->constructCampaignListUrl();
            $response = $this->api->sendApiRequest();
            $campaignData = $this->processGuzzleResult( $response );

            if ( count( $campaignData ) > 0 ) {
                $firstCampaignList = array_column( $campaignData , 'id' );
                $fullCampaignList = array_merge( $fullCampaignList , $firstCampaignList );
                $pageCount = $campaignData[ 0 ][ 'total_pages' ];

                if ( $pageCount > 0 ) {
                    $currentPage = 0;

                    while ( $currentPage <= $pageCount ) {
                        $this->api->constructCampaignListUrl( $currentPage );
                        $nextResponse = $this->api->sendApiRequest();
                        $nextCampaignData = $this->processGuzzleResult( $nextResponse );

                        $nextCampaignList = array_column( $nextCampaignData , 'id' );
                        $fullCampaignList = array_merge( $fullCampaignList , $nextCampaignList );

                        $currentPage++;
                    }

                    $fullCampaignList = array_unique( $fullCampaignList );
                }

                $localCampaignList = $this->reportRepo->getAllCampaigns( $espAccountId )->pluck( 'internal_id' )->toArray();

                $missingCampaigns = array_diff( $fullCampaignList , $localCampaignList );

                return $missingCampaigns;
            }
        } catch ( \Exception $e ) {
            $jobException = new JobException('Failed to merge array' . $e->getMessage(), JobException::NOTICE);
            throw $jobException;
        }

        return [];
    }

    public function getRawByExternalId ( $id ) {
        return $this->reportRepo->getRowByExternalId( $id );
    }
}
