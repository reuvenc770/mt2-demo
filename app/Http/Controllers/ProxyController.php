<?php

namespace App\Http\Controllers;


use App\Services\EspApiAccountService;

use App\Services\EspService;
use App\Services\ProxyService;
use App\Services\DomainGroupService;

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
    public function __construct(ProxyService $proxyService, EspApiAccountService $espAccountService, EspService $espService , DomainGroupService $domainGroupService )
    {
        $this->proxyService = $proxyService;
        $this->espAccountService = $espAccountService;
        $this->espService = $espService;
        $this->domainGroupService = $domainGroupService;
    }

    public function listAll()
    {
        return response()
            ->view('bootstrap.pages.proxy.proxy-index');
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
        $espAccounts = $this->espAccountService->getAllActiveAccounts();
        $esps = $this->espService->getAllEsps();
        $isps = $this->domainGroupService->getAllActive();
        return view('bootstrap.pages.proxy.proxy-add',[ 'espAccounts' => $espAccounts, 'esps' => $esps , 'isps' => $isps ] );
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
        $espAccounts = $this->espAccountService->getAllActiveAccounts();
        $esps = $this->espService->getAllEsps();
        $isps = $this->domainGroupService->getAllActive();
        return response()
            ->view('bootstrap.pages.proxy.proxy-edit',[ 'espAccounts' => $espAccounts, 'esps' => $esps , 'isps' => $isps ] );
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
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $this->proxyService->toggleRow($id,$request->get("direction"));
    }

}