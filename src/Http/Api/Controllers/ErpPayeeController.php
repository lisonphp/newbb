<?php

namespace Newbee\Finance\Http\Api\Controllers;

use DB;
use Illuminate\Http\Request;
use Newbee\Finance\Exports\PayeeExport;
use Newbee\Finance\Models\ErpPayeeList;
use Newbee\Finance\Models\ErpReconciliation;
use Newbee\Finance\Models\ErpPayee;
use Newbee\Finance\Models\ErpSettlement;
use Newbee\Basics\Models\ErpCustomer;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Newbee\Finance\Http\Api\Services\Payee\ErpPayeeDeleteService;
use Newbee\Finance\Http\Api\Services\Payee\ErpPayeeShowService;
use Newbee\Finance\Http\Api\Services\Payee\ErpPayeeStoreService;
use Newbee\Finance\Http\Api\Services\Payee\ErpPayeeNoneStoreService;
use Newbee\Finance\Http\Api\Services\Payee\ErpPayeeUpdateService;
use Newbee\Finance\Http\Api\Base\Controller;
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
     *     description="应收单据收款-保存",
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
     * @OA\Post (
     *     path="/api/none_payee",
     *     tags={"收款管理"},
     *     description="无应收单据收款-保存",
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
    public function none_store(ErpPayeeNoneStoreService $service)
    {
        return $this->response->item($service->none_store(), $this->transformer);
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
     *      path="/api/payee/{payee_list_id}",
     *      tags={"收款管理"},
     *      description="保是单据 删除",
     *      @OA\Parameter(
     *          name="payee_list_id",
     *          description="单据id",
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
     *          in="query",
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
            $payee_id_arr = explode(',', $input['payee_id']);
            $code =  ErpPayee::whereIn('payee_id',$payee_id_arr)->where('examine', 2)->count();
            if (!empty($code))
            {
                throw new HttpException(422, '已审核的单据，不能进行审核！');
            }
            foreach ($payee_id_arr as $key => $v) {
                $payee_res=ErpPayee::where('payee_id',$v)->first()->toarray();
                // var_dump($customer_id);
                $customer_id=$payee_res['customer_id'];
                $is_res=ErpSettlement::where('customer_id',$customer_id)
                ->orderBy('settlement_id','desc')
                ->first();
                // ->toarray();
                // var_dump($is_res);
                // return 222;
                if($is_res){
                    //累加

                    // $input['csm_id']=$;
                    // $input['currency_id']=$;
                    //余额= 收款金额 - 核销金额
                    $input['balance']=$payee_res['payee_amount'] - $payee_res['write_amount'] + $is_res['balance'];
                    //未核销 = 核销金额 - 收款金额
                    $input['no_write_amount']=$payee_res['write_amount'] - $payee_res['payee_amount'] + $is_res['no_write_amount'];
                    //核销金额
                    $input['write_amount']=$payee_res['write_amount'] + $is_res['write_amount'];
                    //合计 = 余额 + 未核销
                    $input['total']=$input['balance'] + $input['no_write_amount'];
                    //未对账 = 未核销 转正数
                    $input['no_reconciliation_amout']=abs($input['no_write_amount']);
                    unset($input['payee_id']);
                    ErpSettlement::where('settlement_id',$is_res['settlement_id'])->update($input);
                }else{
                    //新增
                    $customer_res=ErpCustomer::where('customer_id',$customer_id)->first()->toarray();
                    $input['customer_id']=$customer_id;
                    $input['customer_coding']=$customer_res['customer_coding'];
                    $input['customer_name']=$customer_res['customer_name'];
                    $input['csm_id']=1;//现金
                    $input['currency_id']=$payee_res['currency_id'];
                    $input['balance']=$payee_res['payee_amount'] - $payee_res['write_amount'];
                    $input['no_write_amount']=$payee_res['write_amount'] - $payee_res['payee_amount'];
                    $input['write_amount']=$payee_res['write_amount'];
                    $input['total']=$input['balance'] + $input['no_write_amount'];
                    $input['no_reconciliation_amout']=abs($input['no_write_amount']);
                    unset($input['payee_id']);
                    ErpSettlement::insert($input);
                }
                //已审核
                ErpPayee::where('payee_id',$v)->update([
                    'examine_id' => \Auth::user()['id'], 
                    'examine' => 2, 
                    'examine_at' => date('Y-m-d H:i:s')
                ]);
            }

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
     *     description="批量反核",
     *     @OA\Parameter(
     *          name="payee_id",
     *          description="列表id(多个逗号隔开)",
     *          required=false,
     *          in="query",
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
        // var_dump($input);
        // return 333;
        DB::beginTransaction();
        try {
            $payee_id_arr = explode(',', $input['payee_id']);
            $code =  ErpPayee::whereIn('payee_id',$payee_id_arr)->where('examine', 1)->count();
            if (!empty($code))
            {
                throw new HttpException(422, '未审核的单据，不能进行反审核！');
            }
            foreach ($payee_id_arr as $key => $v) {
                $payee_res=ErpPayee::where('payee_id',$v)->first()->toarray();
                $customer_id=$payee_res['customer_id'];
                $is_res=ErpSettlement::where('customer_id',$customer_id)
                ->orderBy('settlement_id','desc')
                ->first();
                // ->toarray();
                // $input['total']=$;
                // $input['no_reconciliation_amout']=$;
                //余额
                $data['balance']=$is_res['balance'] - ($payee_res['payee_amount'] - $payee_res['write_amount']);
                //未核销金额
                $data['no_write_amount']=$is_res['no_write_amount'] - ($payee_res['write_amount'] - $payee_res['payee_amount']);
                //已核销金额
                $data['write_amount']=$is_res['write_amount'] - $payee_res['write_amount'];
                //合计
                $data['total']=$data['balance'] + $data['no_write_amount'];
                //未对账金额
                $data['no_reconciliation_amout']=abs($data['no_write_amount']);
                ErpSettlement::where('settlement_id',$is_res['settlement_id'])->update($data);

                ErpPayee::where('payee_id',$v)->update([
                    'examine_id' => null, 
                    'examine' => 1, 
                    'examine_at' => null
                ]);
            }
            //Settlement表 客户对应的收款单 一个都没有了的时候
            $is_payee=ErpPayee::where([
                'examine'=>2,
                'customer_id'=>$customer_id
            ])
            ->first();
            // var_dump($is_payee);
            // return 555;
            if(!$is_payee){
                ErpSettlement::where('customer_id',$customer_id)->forceDelete();//forceDelete强力删除
            }

            /* ErpPayee::whereIn('payee_id',$payee_id)->update(['examine_id' => null, 'examine' => 1, 'examine_at' => null]);
            $payee_list_no = \Arr::flatten(ErpPayeeList::whereIn('payee_id',$payee_id)->get('payee_list_no')->toArray());
            ErpReconciliation::whereIn('reconciliation_no', $payee_list_no)->update(['examine' => 1]); */
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
