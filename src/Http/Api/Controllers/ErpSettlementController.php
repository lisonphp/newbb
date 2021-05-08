<?php

namespace Newbee\Finance\Http\Api\Controllers;

use DB;
use Illuminate\Http\Request;
use Newbee\Finance\Exports\PayeeExport;
use Newbee\Finance\Http\Api\Services\Settlement\ErpSettlementSearchService;
use Newbee\Finance\Http\Api\Base\Controller;
use Newbee\Finance\Transformers\ErpSettlementTransformers;
use Newbee\Finance\Models\ErpSettlement;

class ErpSettlementController extends Controller
{
    protected $transformer;

    public function __construct(ErpSettlementTransformers $transformer)
    {
        $this->transformer = $transformer;
    }

    /**
     * @OA\Get(
     *     path="/api/settlement",
     *     tags={"综合结算"},
     *     description="列表",
     *     @OA\Parameter(
     *          name="customer_name",
     *          description="客户名称",
     *          required=false,
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *     @OA\Parameter(
     *          name="balance",
     *          description="余额类型(-1为欠款，1为有余额，2为无余额)",
     *          required=false,
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Response(
     *         response=200,
     *         description="successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/ErpPayee")
     *     )
     * )
     */
    public function index(ErpSettlementSearchService $service)
    {
        return $this->response->paginator($service->search(), $this->transformer);
    }

    /**
     * @OA\Get(
     *      path="/api/settlement_statistics",
     *      tags={"综合结算"},
     *      description="综合结算-统计",
     *      @OA\Parameter(
     *          name="customer_name",
     *          description="客户名称",
     *          required=false,
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="balance",
     *          description="余额类型(-1为欠款，1为有余额，2为无余额)",
     *          required=false,
     *          in="query",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *         response=200,
     *         description="successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/ErpPayee")
     *     )
     * )
     */
    public function settlement_statistics()
    {
        $table = DB::table('erp_settlement');
        // $table = new ErpSettlement;
        // ErpSettlement
        $input = Request()->all();
        isset($input['customer_name']) && $table->where('customer_name',$input['customer_name']);
        // isset($input['balance']) && $table->where('balance',$input['balance']);
        isset($input['insert_at']) && $table->where('settlement_created_at','>=',$input['insert_at']);
        isset($input['end_at']) && $table->where('settlement_created_at','<=',$input['end_at']);
        $d_res = $table->select('balance','no_write_amount','write_amount','total','no_reconciliation_amout')
        ->whereNull('deleted_at')
        ->get();
        // return $d_res;
        
        $data['balance']=0;
        $data['no_write_amount']=0;
        $data['write_amount']=0;
        $data['total']=0;
        $data['no_reconciliation_amout']=0;
        foreach ($d_res as $key => $v) {
            $data['balance'] +=$v->balance;
            $data['no_write_amount'] +=$v->no_write_amount;
            $data['write_amount'] +=$v->write_amount;
            $data['total'] +=$v->total;
            $data['no_reconciliation_amout'] +=$v->no_reconciliation_amout;
        }
        return $this->response()->array(['status' => 200, 'msg' => 'success', 'data' => $data])->setStatusCode(200);
    }

    /**
     * @OA\Get (
     *      path="/api/settlement_export",
     *      tags={"综合结算"},
     *      description="导出",
     *      @OA\Parameter(
     *          name="fileName",
     *          description="文件名称",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *     @OA\Parameter(
     *          name="data",
     *          description="内容",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="json"
     *          )
     *      ),
     *     @OA\Parameter(
     *          name="writerType",
     *          description="文件类型（只能传csv,xls,xlsx）",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation"
     *       )
     * )
     */
    public function export(Request $request)
    {
        $request->validate(['fileName'=> ['required'], 'data'=> ['required'], 'writerType'=> ['required']]);
        return (new PayeeExport($request))->download($request->input('fileName').'.'.$request->input('writerType'));
    }

}
