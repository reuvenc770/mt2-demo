<?php

namespace App\Http\Controllers;

use App\DataModels\CacheReportCard;
use App\Jobs\ExportListProfileJob;
use App\Services\DeployService;
use App\Services\EspService;
use App\Services\PackageZipCreationService;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Response;
use League\Csv\Reader;

class DeployController extends Controller
{
    protected $deployService;
    protected $packageService;

    public function __construct(DeployService $deployService, PackageZipCreationService $packageService)
    {
        $this->deployService = $deployService;
        $this->packageService = $packageService;

    }

    public function listAll(EspService $espService)
    {
        $esps = $espService->getAllEsps();
        return response()->view('pages.deploy.deploy-index', ['esps' => $esps]);
    }

    public function returnCakeAffiliates()
    {
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
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Requests\AddDeployRequest $request)
    {
        $deploy = $this->deployService->insertDeploy($request->all());
        return response()->json(["deploy_id" => $deploy->id]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return response()->json($this->deployService->getDeploy($id));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Requests\EditDeployRequest $request, $id)
    {
        $data = $request->except(["deploy_id", "_method"]);
        $this->deployService->updateDeploy($data, $id);
        return response()->json(["success" => true]);
    }


    public function exportCsv(Request $request)
    {

        $data = $request->get("ids");
        $rows = explode(',', $data);
        $csv = $this->deployService->exportCsv($rows);
        $random = str_random(10);
        $filename = "deployExport{$random}";
        $headers = array(
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}.csv\"",
        );

        // our response, this will be equivalent to your download() but
        // without using a local file
        return Response::make(rtrim($csv, "\n"), 200, $headers);

    }

    public function validateMassUpload(Request $request)
    {
        $fileName = $request->get("filename");
        $returnData = array();
        $dateFolder = date('Ymd');
        $path = storage_path() . "/app/files/uploads/deploys/$dateFolder/$fileName";

        $reader = Reader::createFromPath($path);
        $headers = $reader->fetchOne();
        $reader->setOffset(1);
        $flag = false;
        $results = $reader->fetchAssoc($headers);

        foreach ($results as $key => $row) {
            $row['valid'] = $this->deployService->validateDeploy($row);
            if (count($row['valid']) > 0) {
                $flag = true;
            }
            $returnData['rows'][] = $row;
            $returnData['errors'] = $flag;

        }
        return response()->json($returnData);
    }

    public function massupload(Request $request)
    {
        $data = $request->all();

        return response()->json(['success' => $this->deployService->massUpload($data)]);
    }

    public function checkProgress(Request $request)
    {
        return response()->json($this->deployService->getPendingDeploys());
    }

    public function deployPackages(Request $request)
    {
        $username = $request->get("username");
        $data = $request->except("username");
        $filePath = false;
        $ran = str_random(10);
        $reportCard = CacheReportCard::makeNewReportCard("{$username}-{$ran}");
        //Only one package is selected return the filepath and make it a download response
        if (count($data) == 1) {
            $reportCard->setNumberOfEntries(1);
            $filePath = $this->packageService->createPackage($data);
            $deploy = $this->deployService->getDeploy($data);
            $this->dispatch(
                new ExportListProfileJob(
                    $deploy->list_profile_combine_id,
                    $deploy->offer_id,
                    str_random(16),
                    $reportCard->getName()
                )
            );
        } else {
            //more then 1 package selection create the packages on the FTP and kick off the OPS file job
            foreach ($data as $id) { 
                $reportCard->setNumberOfEntries(count($data));
                $this->packageService->uploadPackage($id);
                $deploy = $this->deployService->getDeploy($id);
                $this->dispatch(
                    new ExportListProfileJob(
                        $deploy->list_profile_combine_id,
                        $deploy->offer_id,
                        str_random(16),
                        $reportCard->getName()
                    )
                );
            }
            Artisan::call('deploys:sendtoops', ['deploysCommaList' => join(",",$data), 'username' => $username]);
        }
        //Update deploy status to pending
        $this->deployService->deployPackages($data);

        if($filePath){
            return response()->download($filePath);
        }
        return response()->json(['success' => true] );
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

    public function previewDeploy(Request $request ,$deployId){
        //currently void method
        try {
            $html  = $this->packageService->createHtml($deployId);

            return response()
                ->view( 'html', ["html" => $html] );
        }
        catch (\Exception $e){
            return $e->getMessage();
        }

    }

    public function downloadHtml(Request $request ,$deployId){
        try {
            //currently void method
            $html  = $this->packageService->createHtml($deployId);

            return response()
                ->view( 'pages.deploy.deploy-preview', ["html" => $html] );
        }
        catch (\Exception $e) {
            return $e->getMessage();
        }

    }


    public function copyToFuture(Request $request){
        $data = $request->all();
        $errors = $this->deployService->copyToFutureDate($data);
        $statusCode = count($errors) > 0 ? 500:200;
        return response()->json(['errors' => $errors],$statusCode);
    }

}
