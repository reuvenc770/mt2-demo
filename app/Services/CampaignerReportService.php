<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 1/20/16
 * Time: 10:28 AM
 */

namespace App\Services;


use App\Library\Campaigner\CampaignManagement;
use App\Repositories\ReportRepo;
use App\Services\API\Campaigner;
use App\Services\Interfaces\IAPIReportService;
use App\Services\Interfaces\IReportService;
use App\Library\Campaigner\DateTimeFilter;
use App\Library\Campaigner\GetCampaignRunsSummaryReport;
use Carbon\Carbon;
use App\Events\RawReportDataWasInserted;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;


/**
 * Class CampaignerReportService
 * @package App\Services
 */
class CampaignerReportService extends Campaigner implements IAPIReportService, IReportService
{
    /**
     * @var ReportRepo
     */
    protected $reportRepo;

    CONST NO_CAMPAIGNS = 'M_4.1.1.1_NO-CAMPAIGNRUNS-FOUND';
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
     * @param $data
     * @throws \Exception
     */
    public function insertRawStats($data)
    {
        $arrayReportList = array();

        if (count($data->getCampaign()) > 1) {  //another dumb check
            foreach ($data->getCampaign() as $report) {
                $convertedReport = $this->mapToRawReport($report);
                try {
                    $this->reportRepo->insertStats($this->getAccountName(), $convertedReport);
                } catch (\Exception $e) {
                    throw new \Exception($e->getMessage());
                }
                $arrayReportList[] = $convertedReport;
            }
        } else {
            $convertedReport = $this->mapToRawReport($data->getCampaign());
            try {
                $this->reportRepo->insertStats($this->getAccountName(), $convertedReport);
            } catch (\Exception $e) {
                throw new \Exception($e->getMessage());
            }
            $arrayReportList[] = $convertedReport;
        }

        Event::fire(new RawReportDataWasInserted($this->getApiName(), $this->getAccountName(), $arrayReportList));
    }

    /**
     * @param $report
     * @return array
     */
    public function mapToRawReport($report)
    {
        $keys = array('sent', 'delivered', 'hard_bounces', 'soft_bounces', 'spam_bounces', 'opens', 'clicks', 'unsubs', 'spam_complaints');
        $emailStats = array_fill_keys($keys, 0);
        //Dumb logic because of how things are nested
        $camaignRuns = $report->getCampaignRuns();

        $runs = $camaignRuns->getCampaignRun();
        if (count($runs) > 1) {  // Campaigner descides to not return array if its only 1 element
            foreach ($runs as $run) {
                $domains = $run->getDomains();
                $domain = $domains->getDomain();
                $deliveryResults = $domain->getDeliveryResults();
                $emailStats['sent'] += $deliveryResults->getSent();
                $emailStats['delivered'] += $deliveryResults->getDelivered();
                $emailStats['hard_bounces'] += $deliveryResults->getHardBounces();
                $emailStats['soft_bounces'] += $deliveryResults->getSoftBounces();
                $emailStats['spam_bounces'] += $deliveryResults->getSpamBOunces();
                $activityResults = $domain->getActivityResults();
                $emailStats['opens'] += $activityResults->getOpens();
                $emailStats['clicks'] += $activityResults->getClicks();
                $emailStats['unsubs'] += $activityResults->getUnsubscribes();
                $emailStats['spam_complaints'] += $activityResults->getSpamComplaints();
            }
        } else {
            $domains = $runs->getDomains();
            $domain = $domains->getDomain();
            $deliveryResults = $domain->getDeliveryResults();
            $emailStats['sent'] = $deliveryResults->getSent();
            $emailStats['delivered'] = $deliveryResults->getDelivered();
            $emailStats['hard_bounces'] = $deliveryResults->getHardBounces();
            $emailStats['soft_bounces'] = $deliveryResults->getSoftBounces();
            $emailStats['spam_bounces'] = $deliveryResults->getSpamBOunces();

            $activityResults = $domain->getActivityResults();
            $emailStats['opens'] = $activityResults->getOpens();
            $emailStats['clicks'] = $activityResults->getClicks();
            $emailStats['unsubs'] = $activityResults->getUnsubscribes();
            $emailStats['spam_complaints'] = $activityResults->getSpamComplaints();
        }

        return array(
            'internal_id' => $report->getId(),
            'name' => $report->getName(),
            'account_name' => $this->getAccountName(),
            'subject' => $report->getSubject(),
            'from_name' => $report->getFromName(),
            'from_email' => $report->getFromEmail(),
            'sent' => $emailStats['sent'],
            'delivered' => $emailStats['delivered'],
            'hard_bounces' => $emailStats['hard_bounces'],
            'soft_bounces' => $emailStats['soft_bounces'],
            'spam_bounces' => $emailStats['spam_bounces'],
            'opens' => $emailStats['opens'],
            'clicks' => $emailStats['clicks'],
            'unsubs' => $emailStats['unsubs'],
            'spam_complaints' => $emailStats['spam_complaints']
        );

    }

    //we should make a function to return what a standard report is
    /**
     * @param $report
     * @return array
     */
    public function mapToStandardReport($report)
    {
        return array(
            "internal_id" => $report['internal_id'],
            "account_name" => $this->getAccountName(),
            "name" => $report['name'],
            "subject" => $report['subject'],
            "opens" => $report['opens'],
            "clicks" => $report['clicks']
        );
    }


    /**
     * @param $date
     * @return \App\Library\Campaigner\ArrayOfCampaign
     * @throws \Exception
     */
    public function retrieveReportStats($date)
    {
        $dateObject = Carbon::createFromTimestamp(strtotime($date));
        $manager = new CampaignManagement();
        $dateFilter = new DateTimeFilter();
        $dateFilter->setFromDate($dateObject->startOfDay());
        $dateFilter->setToDate($dateObject->endOfDay());
        $params = new GetCampaignRunsSummaryReport($this->getAuth(), null, false, $dateFilter);
        $results = $manager->GetCampaignRunsSummaryReport($params);
        $header = $this->parseOutResultHeader($manager);

        if ($header['errorFlag'] != "false" || $header['returnCode'] == self::NO_CAMPAIGNS) {
            throw new \Exception("{$header['errorFlag']} - {$this->getApiName()}::{$this->getAccountName()} Failed retrieveReportStats because {$header['returnMessage']} - {$header['returnCode']}");
        }
        return $results->getGetCampaignRunsSummaryReportResult();
    }

}