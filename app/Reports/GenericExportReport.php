<?php

namespace App\Reports;
use App\Repositories\EmailActionsRepo;
use Maknz\Slack\Facades\Slack;
use League\Csv\Writer;

class GenericExportReport {

    protected $sourceRepo;
    const SLACK_CHANNEL = "#mt2-daily-reports";
    protected $espName;
    protected $espAccountIds;
    protected $destination;
    protected $today;


    public function __construct(EmailActionsRepo $sourceRepo, $espName, $espAccounts, $destination) {
        $this->sourceRepo = $sourceRepo;
        $this->espName = $espName;
        $this->espAccountIds = $this->getEspAccountIds($espAccounts);
        $this->destination = $destination;
        $this->today = date('Y-m-d');
    }


    public function execute($date) {        
        $this->data = $this->sourceRepo->pullEspAccount($this->espAccountIds, $date);
        $writer = Writer::createFromFileObject(new \SplTempFileObject());

        var_dump($this->data);

        foreach ($this->data as $row) {
            $array = [$row->email_id, $row->email_address];
            $writer->insertOne($array);
        }

        $this->destination->put("$this->espName/{$this->today}.csv", $writer->__toString());
    }


    public function notify() {
        $output = "*##### {$this->espName} Report for {$this->today} ready ####*\n\n";
        Slack::to(self::SLACK_CHANNEL)->send($output);
    }


    protected function getEspAccountIds($espAccounts) {
        $output = [];

        foreach ($espAccounts as $account) {
            $output[]= $account->id;
        }

        return $output;
    }
}