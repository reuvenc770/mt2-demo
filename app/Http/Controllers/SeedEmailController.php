<?php

namespace App\Http\Controllers;

use App\Services\SeedEmailService;
use Illuminate\Http\Request;
use Laracasts\Flash\Flash;

use App\Http\Requests;
use App\Http\Requests\SeedEmailRequest;

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
    public function store(SeedEmailRequest $request)
    {
        Flash::success( 'Seed email was successfully added.' );

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
        Flash::success( 'Seed email was successfully deleted.' );

        return $this->seedService->deleteSeed($id);
    }
}
