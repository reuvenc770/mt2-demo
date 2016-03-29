<?php
namespace App\Services\MT1Services;
use App\Repositories\MT1Repositories\SuppressionReasonRepo;

/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 2/11/16
 * Time: 2:31 PM
 */
class SuppressionReasonService
{
    protected $suppressionRepo;

    public function __construct(SuppressionReasonRepo $reasonRepo)
    {
        $this->suppressionRepo = $reasonRepo;
    }

    public function listAll(){
        //Strip out data we dont need before returning
        return $this->suppressionRepo->getAllSuppressionReasons();
    }
}