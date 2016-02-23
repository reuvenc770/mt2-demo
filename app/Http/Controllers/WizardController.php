<?php

namespace App\Http\Controllers;

use App\Models\MT1Models\ClientGroup;
use App\Services\MT1Services\ClientGroupService;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class WizardController extends Controller
{
    protected $service;
    public function __construct(ClientGroupService $service)
    {
        $this->service = $service;
    }

    public function test()
    {
        $test = view()->make('pages.role.role-index');
        $sections = $test->renderSections(); // returns an associative array of 'content', 'head' and 'footer'

        return $sections['content']; // this w
    }

    public function woo()
    {
        return response()->view('pages.wizard.wizard-index');
    }
}