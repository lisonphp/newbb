<?php

namespace Newbee\Finance\Http\Api\Controllers;

use DB;
use Illuminate\Http\Request;
use Newbee\Finance\Exports\PayeeExport;
use Newbee\Finance\Models\ErpPayeeList;
use Newbee\Finance\Models\ErpReconciliation;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Newbee\Finance\Http\Api\Services\Payee\ErpPayeeDeleteService;
use Newbee\Finance\Http\Api\Services\Payee\ErpPayeeShowService;
use Newbee\Finance\Http\Api\Services\Payee\ErpPayeeStoreService;
use Newbee\Finance\Http\Api\Services\Payee\ErpPayeeUpdateService;
use Newbee\Finance\Http\Api\Base\Controller;
use Newbee\Finance\Models\ErpPayee;
use Newbee\Finance\Transformers\ErpPayeeTransformers;
use Newbee\Finance\Http\Api\Services\Payee\ErpPayeeSearchService;

class ErpPayeeController extends Controller
{
    protected $transformer;

    public function __construct(ErpPayeeTransformers $transformer)
    {
        $this->transformer = $transformer;
    }

    /**
     * @OA\Get(
     *     path="/api/payee",
     *     tags={"收款管理"},
     *     description="列表",
     *     @OA\Parameter(
     *          name="keyword",
     *          description="单据编号",
     *          required=false,
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
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
     *          name="created_at",
     *          description="制单日期起",
     *          required=false,
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *     @OA\Parameter(
     *          name="examine_at",
     *          description="制单日期止",
     *          required=false,
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *     @OA\Parameter(
     *          name="desc",
     *          description="备注",
     *          required=false,
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *     @OA\Parameter(
     *          name="examine",
     *          description="审核标识(1未审核，2已审核)'",
     *          required=false,
     *          in="query",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *     @OA\Parameter(
     *          name="payee_account",
     *          description="账户",
     *          required=false,
     *          in="query",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *     @OA\Parameter(
     *          name="difference",
     *          description="存在差额(1.不存在差额，2.存在差额)",
     *          required=false,
     *          in="query",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *     @OA\Parameter(
     *          name="producer_id",
     *          description="制单人id",
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
    public function index(ErpPayeeSearchService $service)
    {
        return $this->response->paginator($service->search(), $this->transformer);
    }

    /**
     * @OA\Post (
     *     path="/api/payee",
     *     tags={"收款管理"},
     *     description="添加",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="accmonth",
     *                     description="会计期间",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="customer_id",
     *                     description="客户id",
     *                     type="integer"
     *                 ),
     *                 @OA\Property(
     *                     property="payee_amount",
     *                     description="收款金额",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="currency_id",
     *                     description="币别id",
     *                     type="integer"
     *                 ),
     *                 @OA\Property(
     *                     property="payee_account",
     *                     description="收款账户",
     *                     type="integer"
     *                 ),
     *                 @OA\Property(
     *                     property="write_currency",
     *                     description="核销币别id",
     *                     type="integer"
     *                 ),
     *                 @OA\Property(
     *                     property="rate",
     *                     description="核销汇率",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="write_amount",
     *                     description="核销金额",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="write_difference",
     *                     description="核销差额",
     *                     type="string"
     *                 ),
     *                  @OA\Property(
     *                     property="water_bill_img",
     *                     description="水单图片",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="invoice_img",
     *                     description="发票图片",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="desc",
     *                     description="备注",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="inside_desc",
     *                     description="内部备注",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="receipt_id",
     *                     description="单票入仓核单id（多个用逗号隔开）",
     *                     type="string"
     *                 ),
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation"
     *       )
     * )
     */
    public function store(ErpPayeeStoreService $service)
    {
        return $this->response->item($service->store(), $this->transformer);
    }

    /**
     * @OA\Get (
     *      path="/api/payee/{payee_id}",
     *      tags={"收款管理"},
     *      description="详情",
     *      @OA\Parameter(
     *          name="payee_id",
     *          description="收款id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/ErpPayee")
     *       )
     * )
     */
    public function show(ErpPayeeShowService $service, int $payee_id)
    {
        // var_dump(888);
        // return;
        return $this->response->item($service->show($payee_id), $this->transformer);
    }

    /**
     * @OA\Put(
     *      path="/api/payee/{payee_id}",
     *      tags={"收款管理"},
     *      description="修改",
     *      @OA\Parameter(
     *          name="payee_id",
     *          description="收款id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *     @OA\Parameter(
     *          name="payee_list_no",
     *          description="运单号(多个逗号隔开)",
     *          required=true,
     *          in="query",
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
    public function update(ErpPayeeUpdateService $service, int $payee_id)
    {
        return $this->response->item($service->update($payee_id), $this->transformer);
    }

    /**
     * @OA\Delete (
     *      path="/api/payee/{payee_id}",
     *      tags={"收款管理"},
     *      description="删除",
     *      @OA\Parameter(
     *          name="payee_id",
     *          description="收款id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation"
     *       )
     * )
     */
    public function destroy(ErpPayeeDeleteService $service, int $payee_id)
    {
        return $service->delete($payee_id);
    }

    /**
     * @OA\Put (
     *     path="/api/payee_audit",
     *     tags={"收款管理"},
     *     description="批量审核",
     *     @OA\Parameter(
     *          name="payee_id",
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
    public function payee_audit()
    {
        $input = Request()->all();
        Request()->validate([
            'payee_id' => 'required',
        ]);
        DB::beginTransaction();
        try {
            $payee_id = explode(',', $input['payee_id']);
            $code =  ErpPayee::whereIn('payee_id',$payee_id)->where('examine', 2)->count();
            if (!empty($code))
            {
                throw new HttpException(422, '已审核的单据，不能进行审核！');
            }
            ErpPayee::whereIn('payee_id',$payee_id)->update(['examine_id' => \Auth::user()['id'], 'examine' => 2, 'examine_at' => date('Y-m-d H:i:s')]);
            $payee_list_no = \Arr::flatten(ErpPayeeList::whereIn('payee_id',$payee_id)->get('payee_list_no')->toArray());
            ErpReconciliation::whereIn('reconciliation_no', $payee_list_no)->update(['examine' => 2]);
            DB::commit();
            return $this->response->array(['status' => 200, 'msg' => 'success', 'data' => []])->setStatusCode(200);
        } catch(\Exception $e) {
            throw new \Symfony\Component\HttpKernel\Exception\HttpException(422, $e->getMessage());
        }
    }

    /**
     * @OA\Put (
     *     path="/api/payee_de_audit",
     *     tags={"收款管理"},
     *     description="批量反审核",
     *     @OA\Parameter(
     *          name="payee_id",
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
    public function payee_de_audit()
    {
        $input = Request()->all();
        Request()->validate([
            'payee_id' => 'required',
        ]);
        DB::beginTransaction();
        try {
            $payee_id = explode(',', $input['payee_id']);
            $code =  ErpPayee::whereIn('payee_id',$payee_id)->where('examine', 1)->count();
            if (!empty($code))
            {
                throw new HttpException(422, '未审核的单据，不能进行反审核！');
            }
            ErpPayee::whereIn('payee_id',$payee_id)->update(['examine_id' => null, 'examine' => 1, 'examine_at' => null]);
            $payee_list_no = \Arr::flatten(ErpPayeeList::whereIn('payee_id',$payee_id)->get('payee_list_no')->toArray());
            ErpReconciliation::whereIn('reconciliation_no', $payee_list_no)->update(['examine' => 1]);
            DB::commit();
            return $this->response->array(['status' => 200, 'msg' => 'success', 'data' => []])->setStatusCode(200);
        } catch(\Exception $e) {
            throw new \Symfony\Component\HttpKernel\Exception\HttpException(422, $e->getMessage());
        }
    }

    /**
     * @OA\Get (
     *      path="/api/payee_export",
     *      tags={"收款管理"},
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
