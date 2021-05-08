<?php

namespace Newbee\Finance\Http\Api\Controllers;

use DB;
use Illuminate\Http\Request;
use Newbee\Finance\Exports\ReceiptCheckExport;
use Newbee\Finance\Http\Api\Services\ReceiptCheck\ErpReceiptCheckUpdateService;
use Newbee\Finance\Http\Api\Base\Controller;
use Newbee\Finance\Models\ErpReconciliation;
use Newbee\Finance\Transformers\ErpReceiptCheckTransformers;
use Newbee\Order\Http\Api\Services\Distribution\ErpDistributionSearchService;
use Newbee\Order\Http\Api\Services\Distribution\ErpDistributionSearchChooseService;
use Newbee\Order\Models\ErpDistribution;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ErpReceiptCheckController extends Controller
{
    protected $transformer;

    public function __construct(ErpReceiptCheckTransformers $transformer)
    {
        $this->transformer = $transformer;
    }

    /**
     * @OA\Get(
     *      path="/api/receipt_check",
     *      tags={"单票入仓核单管理"},
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
        return $this->response->paginator($service->search(), $this->transformer);
    }

    /**
     * @OA\Get(
     *      path="/api/receipt_choose",
     *      tags={"收款管理"},
     *      description="收款管理 添加-选择单据列表",
     *      @OA\Parameter(
     *          name="customer_id",
     *          description="客户ID",
     *          required=true,
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Response(
     *         response=200,
     *         description="successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/ErpDistribution")
     *     )
     * )
     */
    public function choose(ErpDistributionSearchChooseService $service)
    {
        return $this->response->paginator($service->search(), $this->transformer);
    }

    /**
     * @OA\Put (
     *     path="/api/receipt_check/{receipt_check_id}",
     *     tags={"单票入仓核单管理"},
     *     description="单票入仓核单-调整-保存",
     *     @OA\Parameter(
     *          name="distribution_goods_name",
     *          description="商品名称(多个逗号隔开)",
     *          required=false,
     *          in="path",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *     @OA\Parameter(
     *          name="csm_id",
     *          description="结算方式id",
     *          required=false,
     *          in="query",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="channel_id",
     *          description="渠道id",
     *          required=false,
     *          in="query",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="country_id",
     *          description="目的地id",
     *          required=false,
     *          in="query",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="distribution_type_goods",
     *          description="货物类型默认包裹（1.包裹,2.文件,3.PAK袋）",
     *          required=false,
     *          in="query",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *       @OA\Parameter(
     *          name="distribution_number",
     *          description="件数",
     *          required=false,
     *          in="query",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="warehousing_weight",
     *          description="入仓实重",
     *          required=false,
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *     @OA\Parameter(
     *          name="warehousing_square_division",
     *          description="材积",
     *          required=false,
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *     @OA\Parameter(
     *          name="square_division_division",
     *          description="材积除",
     *          required=false,
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *     @OA\Parameter(
     *          name="warehousing_settlement_weight",
     *          description="结算重量",
     *          required=false,
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *     @OA\Parameter(
     *          name="freight",
     *          description="运费",
     *          required=false,
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *     @OA\Parameter(
     *          name="unit_price",
     *          description="单价",
     *          required=false,
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *     @OA\Parameter(
     *          name="other_expenses",
     *          description="其他费用",
     *          required=false,
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *     @OA\Parameter(
     *          name="currency_id",
     *          description="币别id",
     *          required=false,
     *          in="query",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *     @OA\Parameter(
     *          name="desc",
     *          description="入仓备注",
     *          required=false,
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *     @OA\Parameter(
     *          name="inside_desc",
     *          description="入仓内部备注",
     *          required=false,
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *     @OA\Parameter(
     *          name="verify",
     *          description="保存并核单的时候才能传（传2）",
     *          required=false,
     *          in="query",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *     @OA\Parameter(
     *          name="warehousing_volume",
     *          description="材积数据",
     *          required=false,
     *          in="query",
     *          @OA\Schema(
     *              type="object"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="other_expenses_list",
     *          description="其他费用数据",
     *          required=false,
     *          in="query",
     *          @OA\Schema(
     *              type="object"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation"
     *       )
     * )
     */
    public function update(ErpReceiptCheckUpdateService $service, int $receipt_check_id)
    {
        return $this->response->item($service->update($receipt_check_id), $this->transformer);
    }

    /**
     * @OA\Put (
     *     path="/api/adjust",
     *     tags={"单票入仓核单管理"},
     *     description="全部核单",
     *     @OA\Response(
     *          response=200,
     *          description="successful operation"
     *       )
     * )
     */
    public function adjust()
    {
        DB::beginTransaction();
        try {
            $list = ErpDistribution::where('verify',1)->get()->toArray();
            if (!empty($list))
            {
                $data = [];
                foreach ($list as $k => $v)
                {
                    $data[$k]['customer_id'] = $v['customer_id'];
                    $data[$k]['reconciliation_date'] = $v['distribution_created_at'];
                    $data[$k]['reconciliation_no'] = $v['distribution_no'];
                    $data[$k]['reconciliation_transfer_order'] = $v['distribution_Transfer_order'];
                    $data[$k]['reconciliation_number'] = $v['distribution_number'];
                    $data[$k]['reconciliation_weight'] = $v['warehousing_weight'];
                    $data[$k]['reconciliation_square_division'] = $v['warehousing_square_division'];
                    $data[$k]['reconciliation_settlement_weight'] = $v['warehousing_settlement_weight'];
                    $data[$k]['channel_id'] = $v['channel_id'];
                    $data[$k]['country_id'] = $v['country_id'];
                    $data[$k]['reconciliation_goods_name'] = $v['distribution_goods_name'];
                    $data[$k]['reconciliation_amount'] = $v['warehousing_money'];
                    $data[$k]['currency_id'] = $v['currency_id'];
                    $data[$k]['desc'] = $v['desc'];
                }
                ErpDistribution::where('verify',1)->update(['verify' => 2]);
                ErpReconciliation::insert($data);
            }
            DB::commit();
            return $this->response->array(['status' => 200, 'msg' => 'success', 'data' => []])->setStatusCode(200);
        } catch(\Exception $e) {
            throw new \Symfony\Component\HttpKernel\Exception\HttpException(422, $e->getMessage());
        }
    }

    /**
     * @OA\Put (
     *     path="/api/yes_adjust",
     *     tags={"单票入仓核单管理"},
     *     description="批量核单",
     *     @OA\Parameter(
     *          name="distribution_id",
     *          description="列表id(多个逗号隔开)",
     *          required=false,
     *          in="path",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *     @OA\Response(
     *          response=200,
     *          description="successful operation"
     *       )
     * )
     */
    public function yes_adjust()
    {
        $input = Request()->all();
        Request()->validate([
            'distribution_id' => 'required',
        ]);
        DB::beginTransaction();
        try {
            $distribution_ids = explode(',', $input['distribution_id']);
            $code =  ErpDistribution::whereIn('distribution_id',$distribution_ids)->where('verify',2)->count();
            if (!empty($code))
            {
                throw new HttpException(422, '已核单的单据，不能再进行核单！');
            }
            $list = ErpDistribution::whereIn('distribution_id',$distribution_ids)->where('verify',1)->get()->toArray();
            if (!empty($list)) {
                $data = [];
                foreach ($list as $k => $v) {
                    $data[$k]['customer_id'] = $v['customer_id'];
                    $data[$k]['reconciliation_date'] = $v['distribution_created_at'];
                    $data[$k]['reconciliation_no'] = $v['distribution_no'];
                    $data[$k]['reconciliation_transfer_order'] = $v['distribution_Transfer_order'];
                    $data[$k]['reconciliation_number'] = $v['distribution_number'];
                    $data[$k]['reconciliation_weight'] = $v['warehousing_weight'];
                    $data[$k]['reconciliation_square_division'] = $v['warehousing_square_division'];
                    $data[$k]['reconciliation_settlement_weight'] = $v['warehousing_settlement_weight'];
                    $data[$k]['channel_id'] = $v['channel_id'];
                    $data[$k]['country_id'] = $v['country_id'];
                    $data[$k]['reconciliation_goods_name'] = $v['distribution_goods_name'];
                    $data[$k]['reconciliation_amount'] = $v['warehousing_money'];
                    $data[$k]['currency_id'] = $v['currency_id'];
                    $data[$k]['desc'] = $v['desc'];
                }
                ErpReconciliation::insert($data);
                ErpDistribution::whereIn('distribution_id',$distribution_ids)->update(['verify' => 2]);
            }
            DB::commit();
            return $this->response->array(['status' => 200, 'msg' => 'success', 'data' => []])->setStatusCode(200);
        } catch(\Exception $e) {
            throw new \Symfony\Component\HttpKernel\Exception\HttpException(422, $e->getMessage());
        }
    }

    /**
     * @OA\Put (
     *     path="/api/no_adjust",
     *     tags={"单票入仓核单管理"},
     *     description="批量反核单",
     *     @OA\Parameter(
     *          name="distribution_id",
     *          description="列表id(多个逗号隔开)",
     *          required=false,
     *          in="path",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *     @OA\Response(
     *          response=200,
     *          description="successful operation"
     *       )
     * )
     */
    public function no_adjust()
    {
        $input = Request()->all();
        Request()->validate([
            'distribution_id' => 'required',
        ]);
        DB::beginTransaction();
        try {
            $distribution_ids = explode(',', $input['distribution_id']);
            $code =  ErpDistribution::whereIn('distribution_id',$distribution_ids)->where('verify',1)->count();
            if (!empty($code))
            {
                throw new HttpException(422, '未核单的单据，不能进行反核单！');
            }
            $distribution_no = \Arr::flatten(ErpDistribution::whereIn('distribution_id',$distribution_ids)->get('distribution_no')->toArray());
            DB::table('erp_reconciliation')->whereIn('reconciliation_no', $distribution_no)->delete();
            ErpDistribution::whereIn('distribution_id',$distribution_ids)->update(['verify' => 1]);
            DB::commit();
            return $this->response->array(['status' => 200, 'msg' => 'success', 'data' => []])->setStatusCode(200);
        } catch(\Exception $e) {
            throw new \Symfony\Component\HttpKernel\Exception\HttpException(422, $e->getMessage());
        }
    }

    /**
     * @OA\Get (
     *      path="/api/receiptcheck_export",
     *      tags={"单票入仓核单管理"},
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
        return (new ReceiptCheckExport($request))->download($request->input('fileName').'.'.$request->input('writerType'));
    }

}
