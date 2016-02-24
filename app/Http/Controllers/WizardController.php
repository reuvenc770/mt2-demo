<?php

namespace App\Http\Controllers;


use App\Services\WizardService;
use Illuminate\Http\Request;
use Route;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class WizardController extends Controller
{
    protected $service;
    protected $request;
    protected $type;
    public function __construct(Request $request)
    {

        $this->service = new WizardService($request->type);
    }

    public function index($wizardName)
    {
        $this->type = $wizardName;

        return response()->view('pages.wizard.wizard-index', array ("type" => $wizardName, "files" => $this->service->getFiles()));
    }

    public function getPage($wizardName, $pageNumber){
        return $this->service->getPage($pageNumber);
    }

}