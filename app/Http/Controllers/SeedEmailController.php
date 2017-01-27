<?php

namespace App\Http\Controllers;

use App\Services\SeedEmailService;
use Illuminate\Http\Request;

use App\Http\Requests;

class SeedEmailController extends Controller
{
    protected $seedService;

    public function __construct(SeedEmailService $service)
    {
        $this->seedService = $service;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $seeds = $this->seedService->getAllSeeds();
        return response()
            ->view("pages.seed.seed-index", ["seeds" => $seeds]);
    }



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
       $return = $this->seedService->addSeed($request->get("email_address"));
        $returnCode = 200;
        if(!$return){
            $returnCode = 500;
        }
        return response()->json($return,$returnCode);
    }




    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return $this->seedService->deleteSeed($id);
    }
}
