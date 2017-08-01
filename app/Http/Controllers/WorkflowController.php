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

    public function edit($id) {
        $data = [
            'id' => $id
        ];

        return response()->view('pages.workflow.workflow-edit', $data);
    }

    public function get($id) {
        $workflowFeeds = $this->service->getWorkflowFeeds($id);
        $steps = $this->service->getSteps($id);
        $name = $this->service->getName($id);
        $allFeeds = EntityCacheService::get(\App\Repositories\FeedRepo::class, 'shortname');

        $tmp = function($array) {
            $out = [];
            foreach($array as $i) {
                $out[] = $i['id'];
            }

            return function($x) use ($out) {
                return !in_array($x['id'], $out);
            };
        };

        $filterRule = $tmp($workflowFeeds);

        return [
            'id' => $id,
            'name' => $name,
            'selectedFeeds' => $workflowFeeds,
            'steps' => $steps,
            'availableFeeds' => array_values(array_filter($allFeeds, $filterRule))
        ];
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