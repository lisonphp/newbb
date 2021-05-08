<?php

namespace Newbee\Report\Http\Api\Controllers;

use DB;
use Illuminate\Http\Request;
use Newbee\Report\Exports\ReceiptCheckExport;
use Newbee\Report\Http\Api\Services\ReceiptCheck\ErpReceiptCheckUpdateService;
use Newbee\Report\Http\Api\Base\Controller;
use Newbee\Report\Models\ErpReconciliation;
use Newbee\Report\Transformers\ErpReceiptCheckTransformers;
use Newbee\Order\Http\Api\Services\Distribution\ErpDistributionSearchService;
use Newbee\Order\Models\ErpDistribution;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ErpAbcAuController extends Controller
{
    protected $transformer;

    public function __construct(ErpReceiptCheckTransformers $transformer)
    {
        $this->transformer = $transformer;
        return 4444;
    }

    /**
     * @OA\Get(
     *      path="/api/abc_auss",
     *      tags={"明天"},
     *      description="单票入仓核单列表",
     *      @OA\Response(
     *         response=200,
     *         description="successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/ErpDistribution")
     *     )
     * )
     */
    public function index(ErpDistributionSearchService $service)
    {
        return 89898;
        return $this->response->paginator($service->search(), $this->transformer);
    }


}
