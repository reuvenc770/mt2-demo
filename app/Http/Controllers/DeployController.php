<?php

namespace App\Http\Controllers;

use App\Services\DeployService;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Response;

class DeployController extends Controller
{
    protected $deployService;

    public function __construct(DeployService $deployService)
    {
        $this->deployService = $deployService;
    }

    public function listAll(){
        return response()->view('pages.deploy.deploy-index');
    }

    public function returnCakeAffiliates(){
        $data = $this->deployService->getCakeAffiliates();
        return response()->json($data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Requests\AddDeployRequest $request)
    {
      $deploy =  $this->deployService->insertDeploy($request->all());
        return response()->json(["deploy_id" => $deploy->id]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return response()->json($this->deployService->getDeploy($id));
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
    public function update(Requests\EditDeployRequest $request, $id)
    {
        $data = $request->except(["deploy_id","_method"]);
         $this->deployService->updateDeploy($data, $id);
        return response()->json(["success" => true]);
    }


    public function exportCsv(Request $request){

        $data = $request->get("ids");
        $rows = explode(',',$data);
        $csv = $this->deployService->exportCsv($rows);
        $headers = array(
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="tweets.csv"',
        );

        // our response, this will be equivalent to your download() but
        // without using a local file
        return Response::make(rtrim($csv, "\n"), 200, $headers);

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
