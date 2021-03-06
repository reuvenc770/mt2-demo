<?php

namespace App\Http\Controllers;

use App\DataModels\CacheReportCard;
use App\Jobs\ExportDeployCombineJob;
use App\Services\DeployService;
use App\Services\EspService;
use App\Services\ListProfileCombineService;
use App\Services\PackageZipCreationService;
use App\Services\EspApiAccountService;
use App\Services\MailingTemplateService;
use App\Services\DomainService;
use Illuminate\Http\Request;
use App\Exceptions\ValidationException;

use App\Http\Requests;
use Illuminate\Support\Facades\Response;
use League\Csv\Reader;
use Artisan;
use App\Models\Deploy;
use Illuminate\Foundation\Bus\DispatchesJobs;

class DeployController extends Controller
{
    use DispatchesJobs;

    protected $deployService;
    protected $packageService;
    protected $combineService;
    protected $espApiService;
    protected $mailingTemplateService;
    protected $domainService;
    const LIST_PROFILE_QUEUE = 'ListProfile';

    public function __construct(DeployService $deployService, PackageZipCreationService $packageService, ListProfileCombineService $combineService, EspApiAccountService $espApiService, MailingTemplateService $mailingTemplateService, DomainService $domainService )
    {
        $this->deployService = $deployService;
        $this->packageService = $packageService;
        $this->combineService = $combineService;
        $this->espApiService = $espApiService;
        $this->mailingTemplateService = $mailingTemplateService;
        $this->domainService = $domainService;

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

        $missingHeaders = $this->deployService->getMissingHeaders($headers);

        if ( count($missingHeaders) > 0 ){
            $returnData = ['rows' => [], 'errors' => true ];
            $returnData["rows"][] = array_fill_keys( $this->deployService->returnCsvHeader() , '') + ['valid' => ['CSV file is missing the following required headers: ' . implode(', ', $missingHeaders) ] ];
        } else {

            $results = $reader->fetchAssoc($headers);

            foreach ($results as $key => $row) {

                $deploy = $row;

                if ( !is_numeric( $deploy['esp_account_id'] ) ){
                    try  {
                        $deploy['esp_account_id'] = $this->espApiService->getEspAccountIdFromName( $deploy['esp_account_id'] );
                    } catch (\Exception $e) {
                        $deploy['esp_account_id'] = 0;
                        // ok if this fails when validating as user will be shown ui error
                    }
                }

                if ( !is_numeric( $deploy['template_id'] ) ){
                    $deploy['template_id'] = $this->mailingTemplateService->getTemplateIdFromName( $deploy['template_id'] );
                }

                if ( !is_numeric( $deploy['mailing_domain_id'] ) ){
                    $deploy['mailing_domain_id'] = $this->domainService->getDomainIdByTypeAndName( 1 , $deploy['mailing_domain_id'] );
                }

                if ( !is_numeric( $deploy['content_domain_id'] ) ){
                    $deploy['content_domain_id'] = $this->domainService->getDomainIdByTypeAndName( 2, $deploy['content_domain_id'] );
                }

                $deploy['valid'] = $this->deployService->validateDeploy($deploy);
                if (count($deploy['valid']) > 0) {
                    $flag = true;
                }

                $returnData['rows'][] = [
                    'send_date' => $deploy['send_date'] ,
                    'esp_account_id' => ( $deploy['esp_account_id'] > 0 ) ? $this->espApiService->getEspAccountName( $deploy['esp_account_id'] ) : '' ,
                    'list_profile_name' => $deploy['list_profile_name'] ,
                    'offer_id' => $deploy['offer_id'] ,
                    'creative_id' => $deploy['creative_id'] ,
                    'from_id' => $deploy['from_id'] ,
                    'subject_id' => $deploy['subject_id'] ,
                    'template_id' => ( $deploy['template_id'] > 0 ) ? $this->mailingTemplateService->retrieveTemplate( $deploy['template_id'] )['template_name'] : '',
                    'mailing_domain_id' => ( $deploy['mailing_domain_id'] > 0 ) ? $this->domainService->getDomain( $deploy['mailing_domain_id'] )['domain_name'] : '',
                    'content_domain_id' => ( $deploy['content_domain_id'] > 0 ) ? $this->domainService->getDomain( $deploy['content_domain_id'] )['domain_name'] : '',
                    'cake_affiliate_id' => isset( $deploy['cake_affiliate_id'] ) ? $deploy['cake_affiliate_id'] : '',
                    'encrypt_cake' => $deploy['encrypt_cake'],
                    'fully_encrypt' => $deploy['fully_encrypt'] ,
                    'url_format' => $deploy['url_format'],
                    'notes' => isset( $deploy['notes'] ) ? $deploy['notes'] : '',
                    'valid' => $deploy['valid']
                ];
                $returnData['errors'] = $flag;
            }
        }
        return response()->json($returnData);
    }

    public function massupload(Request $request)
    {
        $data = $request->all();
        $dataCopy = $data;

        foreach ( $dataCopy as $index => $current ) {
            try {
                $data[ $index ][ 'send_date' ] = \Carbon\Carbon::parse( $current[ 'send_date' ] )->toDateString();
            } catch ( \Exception $e ) {
                $data[ $index ][ 'send_date' ] = \Carbon\Carbon::now()->toDateString();
            }

            if ( !is_numeric( $current['esp_account_id'] ) ){
                $data[ $index ]['esp_account_id'] = $this->espApiService->getEspAccountIdFromName( $current['esp_account_id'] );
            }

            if ( !is_numeric( $current['template_id'] ) ){
                $data[ $index ]['template_id'] = $this->mailingTemplateService->getTemplateIdFromName( $current['template_id'] );
            }

            if ( !is_numeric( $current['mailing_domain_id'] ) ){
                $data[ $index ]['mailing_domain_id'] = $this->domainService->getDomainIdByTypeAndName( 1 , $current['mailing_domain_id'] );
            }

            if ( !is_numeric( $current['content_domain_id'] ) ){
                $data[ $index ]['content_domain_id'] = $this->domainService->getDomainIdByTypeAndName( 2 , $current['content_domain_id'] );
            }

            if (!isset($current['external_deploy_id'])) {
                $data[$index]['external_deploy_id'] = '';
            }

            if (!isset($current['offer_id'])) {
                $data[$index]['offer_id'] = 0;
            }

            if (!isset($current['creative_id'])) {
                $data[$index]['creative_id'] = 0;
            }

            if (!isset($current['from_id'])) {
                $data[$index]['from_id'] = 0;
            }

            if (!isset($current['subject_id'])) {
                $data[$index]['subject_id'] = 0;
            }

            if (!isset($current['cake_affiliate_id'])) {
                $data[$index]['cake_affiliate_id'] = 0;
            }

            if (!isset($current['url_format'])) {
                $data[$index]['url_format'] = 'short';
            }

            if (!isset($current['notes'])) {
                $data[$index]['notes'] = '';
            }
            
            if (!isset($current['deployment_status'])) {
                $data[$index]['deployment_status'] = 0;
            }
            
            if (!isset($current['user_id'])) {
                $user = \Sentinel::getUser();
                if ($user) {
                    $data[$index]['user_id'] = $user->id;
                }
                else {
                    $data[$index]['user_id'] = 0;
                }
            }
        }

        return response()->json(['success' => $this->deployService->massUpload($data)]);
    }

    public function checkProgress(Request $request)
    {
        return response()->json($this->deployService->getPendingDeploys());
    }

    public function deployPackages(Request $request)
    {
        $user = \Sentinel::getUser();
        $username = $user->username;

        $data = $request->except("username");
        $filePath = false;
        $deploys = [];

        foreach($data as $deployId) {
            $deploys[] = Deploy::find($deployId);
        }

        try {
            //Only one package is selected return the filepath and make it a download response
            if (count($deploys) === 1) {
                $filePath = $this->packageService->createPackage($data);
            }
            else {
                Artisan::call('deploys:sendToOps', ['deploysCommaList' => join(",", $data), 'username' => $username]);
            }

            foreach ($deploys as $d) {
                $ran = str_random(10);
                $reportCard = CacheReportCard::makeNewReportCard("Deploys-{$username}-{$ran}");
                $reportCard->setOwner($username);
                $this->dispatch((new ExportDeployCombineJob([$d], $reportCard, str_random(16), 5000))->onQueue(self::LIST_PROFILE_QUEUE));
            }        
            
            //Update deploy status to pending
            $this->deployService->deployPackages($data);

            if($filePath){
                return response()->download($filePath);
            }
            return response()->json(['success' => true] );
        } catch ( ValidationException $e ) {
            return response()->json( [ 'status' => false , 'message' => 'Validation error: ' . $e->getMessage() ] , 422 );
        } catch (\Exception $e) {
            return response()->json( [ 'status' => false , 'message' => $e->getMessage() ] , 422 );
        }
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
