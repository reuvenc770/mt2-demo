<?php

namespace App\Http\Controllers;

use App\Models\MT1Models\ClientGroup;
use App\Services\MT1Services\ClientGroupService;
use App\Services\WizardService;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class WizardController extends Controller
{
    protected $service;
    protected $request;
    protected $type;
    public function __construct(Request $request)
    {

        $this->request = $request;
    }

    public function index($wizardName)
    {
        $this->type = $wizardName;
        return response()->view('pages.wizard.wizard-index', array ("type" => $wizardName));
    }

    public function getPage($wizardName, $pageNumber){
        $service = new WizardService($wizardName);
        return $service->getPage($pageNumber);
    }

}