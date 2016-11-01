<?php

namespace App\Reports;
use Cache;
use Maknz\Slack\Facades\Slack;
use App\Repositories\SuppressionRepo;
use App\Services\MT1Services\CompanyService;
use Log;
use League\Csv\Writer;
use App\Reports\Interfaces\IFormatStrategy;

class ZxSuppressionExportReport {
    private $repo;
    private $unsubs;
    const SLACK_CHANNEL = "#mt2-daily-reports";
    private $destination;
    private $advertisers;
    private $mt1CompanyService;
    private $today;
    private $formatStrategy;

    public function __construct(CompanyService $mt1CompanyService, SuppressionRepo $repo, IFormatStrategy $formatStrategy, $advertisers, $destination) {
        $this->repo = $repo;
        $this->destination = $destination;
        $this->advertisers = $advertisers;
        $this->mt1CompanyService = $mt1CompanyService;
        $this->today = strftime('%Y-%m-%d');
        $this->formatStrategy = $formatStrategy;
    }

    public function execute($date) {

        foreach ($this->advertisers as $advertiser) {
            $deploys = $this->getDeploysForAdvertiser($advertiser);
            $deploys = $deploys ? array_map([$this, 'extractDeploy'], $deploys->toArray()) : [];
            $unsubs = $this->repo->getSuppressedForDeploys($deploys, $date, $this->repo->getUnsubId())->toArray();

            $writer = Writer::createFromFileObject(new \SplTempFileObject());

            $writer->setDelimiter( $this->formatStrategy->getDelimiter() );

            foreach ($unsubs as $row) {
                $writer->insertOne( $this->formatStrategy->formatFile($row) );
            }

            $location = "$advertiser/unsub/" . $this->formatStrategy->formatFileName($date);
            $this->destination->put($location, $writer->__toString());
        }

    }

    public function notify() {
        $offers = implode(', ', $this->advertisers);
        $output = "*##### {$offers} Offer Unsub Report for {$this->today} ready ####*\n\n";
        Slack::to(self::SLACK_CHANNEL)->send($output);
    }

    protected function getRecordsByDateEsp($espAccountId, $date, $typeId){
        try{
            $operator = $this->range ? '>=' : '=';
            return $this->repo->getRecordsByDateIntervalEspType($typeId, $espAccountId, $date, $operator);
        } 
        catch (\Exception $e) {
            Log::error($e->getMessage(). ": while trying get Suppression Records for $typeId");
            throw new \Exception($e);
        }
    }

    protected function getDeploysForAdvertiser($advertiserName) {
        return $this->mt1CompanyService->getDeploysForAdvertiser($advertiserName);
    }

    protected function extractDeploy($item) {
        return $item['subAffiliateID'];
    }
    
}
