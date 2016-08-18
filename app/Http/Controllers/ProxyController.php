<?php

namespace App\Http\Controllers;

use App\Services\EspApiService;
use App\Services\ProxyService;

use Illuminate\Http\Request;

use App\Http\Requests;
use Laracasts\Flash\Flash;
use Log;
class ProxyController extends Controller
{
    protected $proxyService;
    protected $espService;
    public function __construct(ProxyService $proxyService, EspApiService $espService)
    {
        $this->proxyService = $proxyService;
        $this->espService = $espService;
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
        $proxys = $this->proxyService->getAll();
        $return = array();
        foreach ($proxys as $proxy) {
            $return[] = array(
                $proxy->id,
                $proxy->name,
                $proxy->ip_address,
                $proxy->provider_name

            );
        }
        return response()->json($return);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $esps = $this->espService->getAllEsps();
        return view('pages.proxy.proxy-add',['esps' => $esps]);
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
        $esps = $this->espService->getAllEsps();
        return response()
            ->view('pages.proxy.proxy-edit',['esps' => $esps]);
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
        $this->proxyService->updateAccount($id, $request->toArray());
        Flash::success("Proxy Account was Successfully Updated");
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

}