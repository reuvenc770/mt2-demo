<?php

namespace App\Http\Controllers;

use App\Library\Bronto\readAccounts;
use App\Services\AWeberListService;
use App\Facades\EspApiAccount;
use Illuminate\Http\Request;

use App\Http\Requests;

class AWeberListController extends Controller
{
    protected $listService;


    public function __construct(AWeberListService $listService)
    {
        $this->listService = $listService;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      $ids = $request->input('ids');
        if (empty($ids)){
            return response()->json(["success" => "failed"], 500);
        } 
        return response()->json($this->listService->updateListStatuses($ids));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit()
    {
        $esps = EspApiAccount::getAllAccountsByESPName("AWeber");
        return view('bootstrap.pages.tools.aweber.listmangagement', ['espAccounts' => $esps]);
    }

    public function getList(Request $request, $id){
        return response()->json($this->listService->getAllListsByAccount($id));
    }

}
