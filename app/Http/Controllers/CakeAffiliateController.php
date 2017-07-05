<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Services\CakeAffiliateService;
use App\Services\OfferPayoutService;

class CakeAffiliateController extends Controller
{
    protected $service;
    protected $payoutService;

    public function __construct ( CakeAffiliateService $service , OfferPayoutService $payoutService) {
        $this->service = $service;
        $this->payoutService = $payoutService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view( 'pages.cake_affiliate' , [
            'affiliateList' => $this->service->getAll() ,
            'offerTypeList' => $this->payoutService->getTypes()
        ] );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = json_decode( ( $request->all() )[ 'data' ] , true );

        return response()->json( $this->service->updateOrCreate( $data ) );
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
        $data = $request->all();
        unset( $data[ 'created_at' ] );
        unset( $data[ 'updated_at' ] );

        return response()->json( $this->service->updateOrCreate( $data ) );
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
