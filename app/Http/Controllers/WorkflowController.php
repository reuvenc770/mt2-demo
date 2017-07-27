<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Services\EspWorkflowService;
use App\Services\EntityCacheService;

class WorkflowController extends Controller {
    private $service;
    
    public function __construct(EspWorkflowService $service) {
        $this->service = $service;
    }

    public function listAll() {
        return response()->view('pages.workflow.workflow-index');
    }

    public function edit() {
        $feeds = EntityCacheService::get(\App\Repositories\FeedRepo::class, 'shortname');
        return response()->view('pages.workflow.workflow-edit', ['feeds' => $feeds]);
    }

    public function add() {}

    public function activate($id) {
        $this->service->setStatus($id, 1);
        return $id;
    }

    public function pause($id) {
        $this->service->setStatus($id, 0);
        return $id;
    }
}