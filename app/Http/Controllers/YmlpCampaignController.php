<?php

namespace App\Http\Controllers;

use App\Facades\EspApiAccount;
use App\Services\EspApiService;
use App\Services\ServiceTraits\PaginationCache;
use Illuminate\Http\Request;
use App\Http\Requests\YmlpFormRequest;
use App\Services\YmlpCampaignService;
use App\Http\Requests;
use Flash;
class YmlpCampaignController extends Controller
{
    protected $campaignService;

    public function __construct(YmlpCampaignService $service, EspApiService $espService)
    {
        $this->campaignService = $service;
        $this->espService = $espService;
    }
    /**
     *
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        return $this->campaignService->getAllCampaigns();

    }

    public function listAll()
    {
        return response()
            ->view( 'pages.tools.ymlp.manager.ymlpcampaigns-list' );
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['espAccounts'] = EspApiAccount::getAllAccountsByESPName("YMLP");
        return response()
            ->view( 'pages.tools.ymlp.manager.ymlpcampaigns-add', $data );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(YmlpFormRequest $request)
    {
        Flash::success("YMLP Campaign was Successfully Created");
        $this->campaignService->insertCampaign($request->all());
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $return = $this->campaignService->getCampaignById($id);
        $return['espAccounts'] = EspApiAccount::getAllAccountsByESPName("YMLP");
       return $return;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data['espAccounts'] = EspApiAccount::getAllAccountsByESPName("YMLP");
        return response()
            ->view( 'pages.tools.ymlp.manager.ymlpcampaigns-edit', $data );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(YmlpFormRequest $request, $id)
    {
        Flash::success("YMLP Campaign was Successfully Updated");
        $this->campaignService->updateCampaign( $id , $request->toArray() );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
