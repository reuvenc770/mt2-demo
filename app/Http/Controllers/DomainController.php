<?php

namespace App\Http\Controllers;

use App\Models\Domain;
use App\Services\DoingBusinessAsService;
use App\Services\DomainService;
use Laracasts\Flash\Flash;
use App\Services\RegistrarService;
use App\Services\ProxyService;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Services\EspService;
class DomainController extends Controller
{
    public $service;
    public $espService;
    public $dbaService;
    public $registrarService;
        //Todo maybe DeployService wraps these smaller ones.
    public function __construct(DomainService $domainService, EspService $espService, DoingBusinessAsService $doingBusinessAsService, RegistrarService $registrarService, ProxyService $proxyService)
    {
        $this->service = $domainService;
        $this->espService = $espService;
        $this->dbaService = $doingBusinessAsService;
        $this->registrarService = $registrarService;
        $this->proxyService = $proxyService;
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
        $regs = $this->registrarService->getAllActive();
        $esps = $this->espService->getAllEsps();
        $dbas = $this->dbaService->getAllActive();
        $proxies = $this->proxyService->getAllActive();
        return response()->view( 'pages.domain.domain-index',  [ 'esps' => $esps , 'dbas' => $dbas, 'regs' => $regs , 'proxies' => $proxies] );
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $esps = $this->espService->getAllEsps();
        $dbas = $this->dbaService->getAllActive();
        $regs = $this->registrarService->getAllActive();
        $proxies = $this->proxyService->getAllActive();
        return response()->view('pages.domain.domain-add', [ 'esps' => $esps , 'dbas' => $dbas, 'regs' => $regs , 'proxies' => $proxies ]);
    }


    public function listView()
    {
        $regs = $this->registrarService->getAllActive();
        $esps = $this->espService->getAllEsps();
        $dbas = $this->dbaService->getAllActive();
        $proxies = $this->proxyService->getAllActive();
      return response()->view('pages.domain.domain-listview', [ 'esps' => $esps , 'dbas' => $dbas, 'regs' => $regs , 'proxies' => $proxies ]);
}

    /**
     * Store a newly created resource in storage.
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
                    $mailDomainValues = explode(",",$domain);

                    if ( count( $mailDomainValues ) === 3 ){
                        list($domainName,$mainSite,$expires) = array_map( 'trim' , $mailDomainValues );

                        try {
                            Carbon::createFromFormat( 'Y-m-d' , $expires );
                        } catch ( \Exception $e ) {
                            return response()->json( ['domains' => ["This domain's expiration date is invalid."] ] , 422 );
                        }

                        if ( $this->service->domainExistsAsDomainType( $domainName , 2 ) ) {
                            return response()->json( ['domains' => ["This domain already exists as a content domain."] ] , 422 );
                        }
                        if ($domainName === $mainSite ){
                            return response()->json( ['domains' => ["Domain Name and Main Site cannot be the same."] ] , 422 );
                        }
                    } else {
                        return response()->json( ['domains' => ["Some domain info is missing."] ] , 422 );
                    }
                    break;
                case Domain::CONTENT_DOMAIN:
                    $contentDomainValues = explode(",",$domain);

                    if ( count( $contentDomainValues) === 2 ){
                        list($domainName,$expires) = array_map( 'trim' , $contentDomainValues );

                        try {
                            Carbon::createFromFormat( 'Y-m-d' , $expires );
                        } catch ( \Exception $e ) {
                            return response()->json( ['domains' => ["This domain's expiration date is invalid."] ] , 422 );
                        }

                        if ( $this->service->domainExistsAsDomainType( $domainName , 1 ) ) {
                            return response()->json( ['domains' => ["This domain already exists as a mailing domain."] ] , 422 );
                        }
                    } else {
                        return response()->json( ['domains' => ["Some domain info is missing."] ] , 422 );
                    }
            }

            $insertArray[] = [
                "domain_type" => $type,
                "proxy_id"    => $request->input("proxy") == '' ? 0 : $request->input("proxy"),
                "doing_business_as_id"      => $request->input("dba"),
                "esp_account_id" => $request->input("espAccountId"),
                "created_at"    => Carbon::now()->toDateString(),
                "expires_at"      => $expires,
                "registrar_id"  => $request->input("registrar"),
                "domain_name"  => $domainName,
                "main_site"   => $mainSite,
                "status"      => 1,
                "live_a_record"  => $request->input("live_a_record"),
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
        return response()->json($this->service->getDomain($id));
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
        $domain = $request->toArray();

        try {
            Carbon::createFromFormat( 'Y-m-d' , $domain[ 'expires_at' ] );
        } catch ( \Exception $e ) {
            return response()->json( ['expires_at' => ["This domain's expiration date is invalid."] ] , 422 );
        }

        $bool = $this->service->updateDomain($domain);
        return response()->json(['success' => $bool]);
    }

    public function searchDomains(Request $request){
        $regs = $this->registrarService->getAllActive();
        $esps = $this->espService->getAllEsps();
        $dbas = $this->dbaService->getAllActive();
        $domains = $this->service->searchDomains($request->toArray());
        $proxies = $this->proxyService->getAllActive();
        return response()->view('pages.domain.domain-searchview', [ 'esps' => $esps , 'dbas' => $dbas, 'regs' => $regs, 'domains' => json_encode($domains) , 'proxies' => $proxies ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $this->service->toggleRow($id,$request->get("direction"));
    }

    public function getDomainsByTypeAndESP($type,$espAccountId){
        return $this->service->getDomainsByTypeAndEsp($type, $espAccountId);
    }

    public function getActiveDomainsByTypeAndESP($type,$espAccountId){
        return $this->service->getActiveDomainsByTypeAndEsp($type, $espAccountId);
    }
}
