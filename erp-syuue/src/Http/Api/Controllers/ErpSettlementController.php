<?php

namespace Newbee\Finance\Http\Api\Controllers;

use DB;
use Illuminate\Http\Request;
use Newbee\Basics\Models\ErpCustomer;
use Newbee\Finance\Http\Api\Base\Controller;
use Newbee\Finance\Models\ErpReconciliation;
use Newbee\Finance\Models\ErpSettlementNotes;
use Newbee\Finance\Exports\SettlementExport;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ErpSettlementController extends Controller
{

    /**
     * @OA\Get(
     *     path="/api/settlement",
     *     tags={"综合结算管理"},
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
     *          name="reconciliation_date",
     *          description="应收单据止",
     *          required=false,
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *     @OA\Parameter(
     *          name="reconciliation_created_at",
     *          description="应结日期止",
     *          required=false,
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *     @OA\Parameter(
     *          name="客户类型",
     *          description="直接传中文，同行或直客",
     *          required=false,
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *     @OA\Parameter(
     *          name="业务员id",
     *          description="customer_salesman_id",
     *          required=false,
     *          in="query",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *     @OA\Parameter(
     *          name="unsettled",
     *          description="存在未核销单据(存在未对账单据)不勾字段都不传过来",
     *          required=false,
     *          in="query",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *         response=200,
     *         description="successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/ErpReconciliation")
     *     )
     * )
     */
    public function index()
    {
        $input = Request()->input();
        $model =  ErpReconciliation::query();
        !empty($input['customer_id']) && $model->where('customer_id',$input['customer_id']);
        !empty($input['reconciliation_date']) && $model->where('reconciliation_date', '<=', $input['reconciliation_date']);
        !empty($input['reconciliation_created_at']) && $model->where('reconciliation_created_at', '<=', $input['reconciliation_created_at']);
        $list = $model->select('customer_id', 'reconciliation_id', DB::raw('SUM( CASE WHEN `examine` = 1 THEN IFNULL(`reconciliation_amount`,0) ELSE 0 END) AS unsettled'), DB::raw('SUM( CASE WHEN `examine` = 2 THEN IFNULL(`reconciliation_amount`,0) ELSE 0 END) AS settled'))
            ->groupBy('customer_id')
            ->orderBy('reconciliation_id', 'desc')
            ->get()
            ->toArray();
        $customer_id = array_column( $list, 'customer_id');
        $reconciliation_id = array_column( $list, 'reconciliation_id');
        $reconciliation = ErpSettlementNotes::whereIn('reconciliation_id', $reconciliation_id)->get()->toArray();
        $customer = ErpCustomer::whereIn('customer_id', $customer_id)->get()->toArray();
        foreach ($list as $k => &$v)
        {
            foreach ($customer as $ks => $vs)
            {
                if ($v['customer_id'] == $vs['customer_id'])
                {
                    $v['customer_type'] = $vs['customer_type'];
                    $v['customer_coding'] = $vs['customer_coding'];
                    $v['customer_name'] = $vs['customer_name'];
                    $v['customer_salesman_id'] = $vs['customer_salesman_id'];
                }
            }
            //客户类型筛选
            if (!empty($input['customer_type']) && $v['customer_type'] != $input['customer_type'])
            {
                unset($list[$k]);
            }
            //业务员筛选
            if (!empty($input['customer_salesman_id']) && $v['customer_salesman_id'] != $input['customer_salesman_id'])
            {
                unset($list[$k]);
            }
            //存在未核销单据
            if (!empty($input['unsettled']) && $v['unsettled'] != 0)
            {
                unset($list[$k]);
            }
            foreach ($reconciliation as $ke => $ve)
            {
                if ($v['reconciliation_id'] == $ve['reconciliation_id'])
                {
                    $v['list'] = $ve;
                }
            }
            $v['total'] = $v['unsettled'];
            $v['incomplete'] = $v['unsettled'];
        }
        return $this->response->array(['status' => 200, 'msg' => 'success', 'data' => $list])->setStatusCode(200);
    }

    /**
     * @OA\Post(
     *     path="/api/settlement",
     *     tags={"综合结算管理"},
     *     description="添加备注",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="reconciliation_id",
     *                     description="列表id",
     *                     type="integer"
     *                 ),
     *                 @OA\Property(
     *                     property="url",
     *                     description="链接",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="desc",
     *                     description="备注",
     *                     type="string"
     *                 ),
     *              )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(ref="#/components/schemas/ErpOrder")
     *     )
     * )
     */
    public function add_desc(int $reconciliation_id)
    {
        $input = Request()->input();
        if (empty($input['desc'])){
            throw new HttpException(422, '请填写备注！');
        }
        $list = [
            'reconciliation_id' => $reconciliation_id,
            'producer_id' => \Auth::user()['id'],
            'url' => $input['url'],
            'desc' => $input['desc']
        ];
        DB::table('erp_settlement_notes')->insert($list);
        return $this->response->array(['status' => 200, 'msg' => 'success', 'data' => []])->setStatusCode(200);
    }

    /**
     * @OA\Get (
     *      path="/api/settlement_export",
     *      tags={"综合结算管理"},
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
        return (new SettlementExport($request))->download($request->input('fileName').'.'.$request->input('writerType'));
    }

}