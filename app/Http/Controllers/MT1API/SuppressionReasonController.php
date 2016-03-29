<?php
namespace App\Http\Controllers\MT1API;
use App\Services\MT1Services\SuppressionReasonService;
use App\Http\Controllers\Controller;

/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 2/11/16
 * Time: 2:25 PM
 */
class SuppressionReasonController extends Controller
{
    protected $suppressionService;

    public function __construct(SuppressionReasonService $suppressionReasonService)
    {
        $this->suppressionService = $suppressionReasonService;
    }

    public function index()
    {
        return $this->suppressionService->listAll();
    }

    public function create()
    {
        return response('Unauthorized.', 401);
    }

    public function store()
    {
        return response('Unauthorized.', 401);

    }

    public function show($id)
    {
        return response('Unauthorized.', 401);
    }


    public function edit()
    {
        return response('Unauthorized.', 401);
    }


    public function update($id)
    {
        return response('Unauthorized.', 401);
    }

}
