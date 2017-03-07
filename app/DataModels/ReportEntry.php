<?php
/**
 * Created by PhpStorm.
 * User: codedestroyer
 * Date: 2/23/17
 * Time: 2:48 PM
 */

namespace App\DataModels;


class ReportEntry
{
    protected $name;
    protected $originalTotal = 0;
    protected $fileName;
    protected $finalTotal = 0;
    protected $globallySuppressed = 0;
    protected $listOfferSuppressed = 0;
    protected $offersSuppressedAgainst = [];

    public function __construct($name)
    {
        $this->name = $name;
    }
    
    public function increaseListSuppressionCount(){
        $this->listOfferSuppressed++;
    }

    public function increaseGlobalSuppressionCount(){
        $this->globallySuppressed++;
    }

    public function increaseFinalRecordCount(){
        $this->finalTotal++;
    }

    public function addToOriginalTotal($count){
        $this->originalTotal+= $count;
    }
    public function addOffersSuppressedAgainst(array $offers){
        $this->offersSuppressedAgainst = $offers;
    }

    public function setFileName($fileName){
        $this->fileName = $fileName;
    }

    public function getFileName(){
        return $this->fileName;
    }
    
    public function getOriginalTotal(){
        return $this->originalTotal;
    }
    /**
     * @return int
     */
    public function getFinalTotal()
    {
        return $this->finalTotal;
    }

    /**
     * @return int
     */
    public function getGloballySuppressed()
    {
        return $this->globallySuppressed;
    }

    /**
     * @return int
     */
    public function getListOfferSuppressed()
    {
        return $this->listOfferSuppressed;
    }

    /**
     * @return array
     */
    public function getOffersSuppressedAgainst()
    {
        return $this->offersSuppressedAgainst;
    }
}