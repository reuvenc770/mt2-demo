<?php

namespace App\Http\Controllers;

use App\Models\Domain;
use App\Services\DoingBusinessAsService;
use App\Services\DomainService;
use Laracasts\Flash\Flash;
use App\Services\RegistrarService;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Services\EspApiService;
class DomainController extends Controller
{
    public $service;
    public $espService;
    public $dbaService;
    public $registrarService;
        //Todo maybe DeployService wraps these smaller ones.
    public function __construct(DomainService $domainService, EspApiService $espService, DoingBusinessAsService $doingBusinessAsService, RegistrarService $registrarService)
    {
        $this->service = $domainService;
        $this->espService = $espService;
        $this->dbaService = $doingBusinessAsService;
        $this->registrarService = $registrarService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    public function listAll(){
        return response()->view( 'pages.domain.domain-index' );
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $esps = $this->espService->getAllEsps();
        $dbas = $this->dbaService->getAll();
        $regs = $this->registrarService->getAll();
        return response()->view('pages.domain.domain-add', [ 'esps' => $esps , 'dbas' => $dbas, 'regs' => $regs]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Requests\AddDomainForm $request)
    {
        $type = $request->input("domain_type");
        $domains = explode("\n",$request->input("domains"));
        $insertArray = [];
        $expires = $domainName = $mainSite = "";
        foreach($domains as $domain){

            switch($type){
                case Domain::MAILING_DOMAIN:
                    list($domainName,$mainSite,$expires) = explode(",",$domain);
                    break;
                case Domain::CONTENT_DOMAIN:
                    list($domainName,$expires) = explode(",",$domain);
            }

            $insertArray[] = [
                "domain_type" => $type,
                "proxy_id"    => $request->input("proxy"),
                "doing_business_as_id"      => $request->input("dba"),
                "esp_account_id" => $request->input("espAccountId"),
                "created_at"    => Carbon::now()->toDateString(),
                "expires_at"      => $expires,
                "registrar_id"  => $request->input("registrar"),
                "domain_name"  => $domainName,
                "main_site"   => $mainSite,
                "active"      => 1,
            ];
        }
        Flash::success("Domain was Successfully Added");
        $bool = $this->service->insertDomains($insertArray);
            return response()->json(['success' => $bool]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
       return $this->service->inactivateDomain($id);
    }

    public function getDomainsByTypeAndESP($type,$espAccountId){
        return $this->service->getDomainsByTypeAndEsp($type, $espAccountId);
    }
}