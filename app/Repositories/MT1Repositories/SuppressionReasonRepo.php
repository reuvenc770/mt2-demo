<?php
namespace App\Repositories\MT1Repositories;
use App\Models\MT1Models\SuppressionReason;

class SuppressionReasonRepo
{
    protected $suppressionReason;

    public function __construct(SuppressionReason $suppressionReason)
    {
        $this->suppressionReason = $suppressionReason;
    }

    public function getAllSuppressionReasons(){
        return $this->suppressionReason->all();
    }

}