<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 2/23/16
 * Time: 4:18 PM
 */

namespace App\Services;

use Storage;
class WizardService
{
    protected $steps;
    protected $type;
    protected $nextStep;
    protected $stepCount;
    protected $currentStep;
    protected $files;

    public function __construct($wizardType)
    {
        $jsonObject = json_decode(Storage::get("wizards/{$wizardType}.config"));
        $this->steps = $jsonObject->steps;
        $this->stepCount = count($this->steps);
        $this->type = $wizardType;
        $this->files = $jsonObject->js;
    }

    public function getPage($pageNumber)
    {
        $this->currentStep =$pageNumber;
        try {
            $page = view()->make($this->steps[$pageNumber]);
            $sections = $page->renderSections(); // returns an associative array of 'content', 'head' and 'footer'
        } catch(\Exception $e){
            return false;
        }
        $returnArray = array(
            'type' => $this->type,
            'section' => $sections['content'],
            'nextPage' => $this->getNextPage(),
            'prevPage' => $this->getPreviousPage()
        );
        return $returnArray;
    }

    public function getNextPage(){
        $currentStep = $this->currentStep;
        if (($currentStep + 1) >= $this->stepCount){
            return false;
        } else {
            return $currentStep + 1;
        }
    }

    public function getPreviousPage(){
        $currentStep = $this->currentStep;
        if (($currentStep - 1)  < 0){
            return false;
        } else {
            return $currentStep - 1;
        }
    }
    /**
     * @return mixed
     */
    public function getFiles()
    {
        return $this->files;
    }
}