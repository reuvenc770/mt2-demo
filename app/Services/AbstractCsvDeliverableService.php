<?php

namespace App\Services;
use App\Services\AbstractCsvDeliverableService;
use App\Repositories\EmailActionsRepo;
use App\Repositories\EmailRepo;
use App\Repositories\ActionRepo;
use Illuminate\Support\Facades\Event;
use App\Services\Interfaces\IDataService; #change this
use League\Csv\Reader;

abstract class AbstractCsvDeliverableService {

    protected $emailRepo;
    protected $emailActionsRepo;
    protected $actionTypeRepo;
    protected $actionMap = array(
        'clicks' => 'clicker',
        'opens' => 'opener',
        'delivered' => 'deliverable',
    );

    protected $actionId;


    public function __construct(EmailActionsRepo $emailActionsRepo, EmailRepo $emailRepo, ActionRepo $actionTypeRepo) {
        $this->emailActionsRepo = $emailActionsRepo;
        $this->emailRepo = $emailRepo;
        $this->actionTypeRepo = $actionTypeRepo;
    }

    public function setCsvToFormat($espAccountId, $action, $filePath) {

        
        $returnArray = array();
        echo $filePath;
        #$mapping = $this->grabCsvDeliverableMapping($espAccountId);
        $reader = Reader::createFromPath($filePath);
        $data = $reader->fetchAssoc();

        foreach ($data as $row) {
            $returnArray[] = $row;
        }

        return $returnArray;
    }

    protected function mapActionToActionTableName($action) {
        return $this->actionMap[$action];
    }

    abstract public function insertDeliverableCsvActions($data);
    abstract protected function grabCsvDeliverableMapping($someId);
  
}