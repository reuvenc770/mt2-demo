<?php

namespace App\Http\Controllers;

use App\Factories\ServiceFactory;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class PagingController extends Controller
{
    protected $serviceFactory;
    protected $request;
    public function __construct(ServiceFactory $factory, Request $request){
        $this->serviceFactory = $factory;
        $this->request = $request;
    }

    public function paginate($type){
        $service = $this->serviceFactory->createModelService($type);
        return response($service->getPaginatedJson( $this->request->input( 'page' ) , $this->request->input( 'count' )) );
    }
}
