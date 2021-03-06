<?php

namespace App\Http\Controllers;


use App\Services\EspApiAccountService;

use App\Services\EspService;
use App\Services\ProxyService;
use App\Services\DomainGroupService;
use App\Services\CakeAffiliateService;

use Illuminate\Http\Request;

use App\Http\Requests;
use Laracasts\Flash\Flash;
use Log;
class ProxyController extends Controller
{
    protected $proxyService;
    protected $espAccountService;
    protected $espService;
    protected $domainGroupService;
    protected $cakeAffiliateService;

    public function __construct(ProxyService $proxyService, EspApiAccountService $espAccountService, EspService $espService , DomainGroupService $domainGroupService , CakeAffiliateService $cakeAffiliateService )
    {
        $this->proxyService = $proxyService;
        $this->espAccountService = $espAccountService;
        $this->espService = $espService;
        $this->domainGroupService = $domainGroupService;
        $this->cakeAffiliateService = $cakeAffiliateService;
    }

    public function listAll()
    {
        return response()
            ->view('pages.proxy.proxy-index');
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json($this->proxyService->getAll());
    }

    public function listAllActive()
    {
        return response()->json($this->proxyService->getAllActive());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $espAccounts = $this->espAccountService->getAllAccounts();
        $esps = $this->espService->getAllEsps();
        $isps = $this->domainGroupService->getAllActive();
        $affs = $this->cakeAffiliateService->getAll();

        return view('pages.proxy.proxy-add',[ 'espAccounts' => $espAccounts, 'esps' => $esps , 'isps' => $isps , 'affiliates' => $affs ] );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Requests\AddProxyRequest $request)
    {
        Flash::success("Proxy was Successfully Created");
        $request = $this->proxyService->insertRow($request->all());
        return response()->json(['status' => $request]);
    }

    /**
     * Display the specified ESP Account.
     *
     * @param  int $id The ESP Account ID to lookup.
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return $this->proxyService->getProxy($id);

    }

    /**
     * Show the form for editing the specified ESP Account.
     *
     * @param  int $id The ESP Account ID to edit.
     * @return \Illuminate\Http\Response
     */
    public function edit()
    {
        $espAccounts = $this->espAccountService->getAllAccounts();
        $esps = $this->espService->getAllEsps();
        $isps = $this->domainGroupService->getAllActive();
        $affs = $this->cakeAffiliateService->getAll();

        return response()
            ->view('pages.proxy.proxy-edit',[ 'espAccounts' => $espAccounts, 'esps' => $esps , 'isps' => $isps , 'affiliates' => $affs ] );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\EspApiEditRequest $request
     * @param  int $id The ESP Account ID being updated.
     * @return \Illuminate\Http\Response
     */
    public function update(Requests\EditProxyRequest $request, $id)
    {
        $proxy = $request->toArray();
        $this->proxyService->updateAccount($id, $proxy);
        Flash::success("Proxy Account was Successfully Updated");
    }


    /**
     * toggle the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function toggle(Request $request, $id)
    {
        $this->proxyService->toggleRow($id,$request->get("direction"));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $response = $this->proxyService->tryToDelete($id);
        $code = $response !== true ? 500 : 200;
        return response()->json( [ 'delete' => $response ],$code );

    }

}
