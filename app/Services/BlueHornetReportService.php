<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 1/13/16
 * Time: 3:31 PM
 */

namespace App\Services;

use App\Services\API\BlueHornet;
use App\Repositories\ReportRepo;
use App\Services\Interfaces\IAPIReportService;
use App\Services\Interfaces\IReportService;
use League\Flysystem\Exception;
use Illuminate\Support\Facades\Event;
use App\Events\RawReportDataWasInserted;
//TODO FAILED MONITORING - better error messages
//TODO Create Save Record method
/**
 * Class BlueHornetReportService
 * @package App\Services
 */
class BlueHornetReportService extends BlueHornet implements IAPIReportService, IReportService
{
    /**
     * @var ReportRepo
     */
    protected $reportRepo;
    /**
     * @var string
     */

    /**
     * BlueHornetReportService constructor.
     * @param ReportRepo $reportRepo
     * @param $accountNumber
     */
    public function __construct(ReportRepo $reportRepo, $apiName, $accountNumber)
    {
        parent::__construct($apiName, $accountNumber);
        $this->reportRepo = $reportRepo;
    }

    /**
     * @param $date
     * @return \SimpleXMLElement
     * @throws \Exception
     */
    public function retrieveApiReportStats($date)
    {
        $methodData = array(
            "date" => $date
        );
        try {
            $xml = $this->buildRequest('legacy.message_stats', $methodData);
            $response = $this->sendApiRequest($xml);
            $xmlBody = simplexml_load_string($response->getBody()->__toString());
        } catch (Exception $e){
            throw new Exception($e->getMessage());
        }
        if ($xmlBody->item->responseCode != 201) {
            throw new \Exception($xmlBody->asXML());
        }
        return $xmlBody;
    }

    public function insertApiRawStats($xmlData)
    {
        $arrayReportList = array();
        $reports = $xmlData->item->responseData->message_data;
        foreach ($reports->message as $report) {
            //Please make a function that makes this not horrible
            $convertedReport = $this->mapToRawReport($report);

            try {
                $this->reportRepo->insertStats($this->getAccountName(), $convertedReport);
            } catch (Exception $e){
                throw new \Exception($e->getMessage());
            }

            $arrayReportList[] = $convertedReport;
        }

        Event::fire(new RawReportDataWasInserted($this->getApiName(),$this->getAccountName(), $arrayReportList));
    }

    public function insertCsvRawStats($reports){
        $arrayReportList = array();
        foreach ($reports as $report) {

            try {
                $this->reportRepo->insertStats($this->getAccountName(), $report);
            } catch (Exception $e){
                throw new \Exception($e->getMessage());
            }

            $arrayReportList[] = $report;
        }

        Event::fire(new RawReportDataWasInserted($this->getApiName(),$this->getAccountName(), $arrayReportList));
    }

>>>>>>> init commit
    public function mapToStandardReport($report){
        return array(
            "internal_id" => $report['internal_id'],
            "account_name"=> $this->getAccountName(),
            "name" => $report['message_name'],
            "subject" => $report['message_subject'],
            "opens"   => $report['opened_total'],
            "clicks"  => $report['clicked_total']
        );
    }

    public function mapToRawReport($report){
        return array(
            "internal_id" => (string)$report['id'],
            "account_name" => $this->getAccountName(),
            "message_subject" => (string)$report->message_subject,
            "message_name" => (string)$report->message_name,
            "date_sent" => (string)$report->date_sent,
            "message_notes" => (string)$report->message_notes,
            "withheld_total" => (string)$report->withheld_total,
            "globally_suppressed" => (string)$report->globally_suppressed,
            "suppressed_total" => (string)$report->suppressed_total,
            "bill_codes" => (string)$report->bill_codes,
            "sent_total" => (string)$report->sent_total,
            "sent_total_html" => (string)$report->sent_total_html,
            "sent_total_plain" => (string)$report->sent_total_plain,
            "sent_rate_total" => (string)$report->sent_rate_total,
            "sent_rate_html" => (string)$report->sent_rate_html,
            "sent_rate_plain" => (string)$report->sent_rate_plain,
            "delivered_total" => (string)$report->delivered_total,
            "delivered_html" => (string)$report->delivered_html,
            "delivered_plain" => (string)$report->delivered_plain,
            "delivered_rate_total" => (string)$report->delivered_rate_total,
            "delivered_rate_html" => (string)$report->delivered_rate_html,
            "delivered_rate_plain" => (string)$report->delivered_rate_plain,
            "bounced_total" => (string)$report->bounced_total,
            "bounced_html" => (string)$report->bounced_html,
            "bounced_plain" => (string)$report->bounced_plain,
            "bounced_rate_total" => (string)$report->bounced_rate_total,
            "bounced_rate_html" => (string)$report->bounced_rate_html,
            "bounced_rate_plain" => (string)$report->bounced_rate_plain,
            "invalid_total" => (string)$report->invalid_total,
            "invalid_rate_total" => (string)$report->invalid_rate_total,
            "has_dynamic_content" => (string)$report->has_dynamic_content,
            "has_delivery_report" => (string)$report->has_delivery_report,
            "link_append_statement" => (string)$report->link_append_statement,
            "timezone" => (string)$report->timezone,
            "ftf_forwarded" => (string)$report->ftf_forwarded,
            "ftf_signups" => (string)$report->ftf_signups,
            "ftf_conversion_rate" => (string)$report->ftf_conversion_rate,
            "optout_total" => (string)$report->optout_total,
            "optout_rate_total" => (string)$report->optout_rate_total,
            "opened_total" =>(string)$report->opened_total,
            "opened_unique" => (string)$report->opened_unique,
            "opened_rate_unique" => (string)$report->opened_rate_unique,
            "opened_rate_aps" => (string)$report->opened_rate_aps,
            "clicked_total" => (string)$report->clicked_total,
            "clicked_unique" => (string)$report->clicked_unique,
            "clicked_rate_unique" => (string)$report->clicked_rate_unique,
            "clicked_rate_aps" => (string)$report->clicked_rate_aps,
            "campaign_name" => (string)$report->campaign_name,
            "campaign_id" => (string)$report->campaign_id
        );
    }


}
