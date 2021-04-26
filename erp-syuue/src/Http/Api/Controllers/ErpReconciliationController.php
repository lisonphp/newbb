<?php

namespace Newbee\Finance\Http\Api\Controllers;

use Newbee\Finance\Http\Api\Base\Controller;
use Newbee\Finance\Http\Api\Services\Reconciliation\ErpReconciliationSearchService;
use Newbee\Finance\Transformers\ErpReconciliationTransformers;

class ErpReconciliationController extends Controller
{
    protected $transformer;

    public function __construct(ErpReconciliationTransformers $transformer)
    {
        $this->transformer = $transformer;
    }

    /**
     * @OA\Get(
     *     path="/api/reconciliation",
     *     tags={"客户对账管理"},
     *     description="列表",
     *     @OA\Parameter(
     *          name="customer_id",
     *          description="客户id",
     *          required=false,
     *          in="query",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *     @OA\Parameter(
     *          name="examine",
     *          description="核销标识(1未核销，2已核销)",
     *          required=false,
     *          in="query",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *     @OA\Parameter(
     *          name="reconciliation_date",
     *          description="制单日期起",
     *          required=false,
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *     @OA\Parameter(
     *          name="keyword",
     *          description="运单号",
     *          required=false,
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Response(
     *         response=200,
     *         description="successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/ErpReconciliation")
     *     )
     * )
     */
    public function index(ErpReconciliationSearchService $service)
    {
        return $this->response->paginator($service->search(), $this->transformer);
    }
}