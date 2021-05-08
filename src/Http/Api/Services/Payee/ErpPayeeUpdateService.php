<?php
namespace Newbee\Finance\Http\Api\Services\Payee;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Newbee\Finance\Models\ErpPayee;
use Newbee\Finance\Models\ErpPayeeList;
use Newbee\Finance\Repository\Contract\ErpPayeeRepository;
use Newbee\Finance\Requests\Payee\UpdateErpPayeeRequest;
use Newbee\Order\Models\ErpDistribution;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ErpPayeeUpdateService
{
    /**
     * @var UpdateErpPayeeRequest $request
     */
    protected $request;
    /**
     * @var ErpPayeeRepository
     */
    protected $repository;

    function __construct(UpdateErpPayeeRequest $request, ErpPayeeRepository $repository)
    {
        $this->request = $request;
        $this->repository = $repository;
    }

    /**
     * 收款管理 编辑
     * @return mixed
     */
    public function update($payee_id)
    {
        $data = [];
        $this->request->validated();
        DB::beginTransaction();
        try {
            $input = Request()->all();
            $payee_codes = \Arr::flatten(ErpPayeeList::where('payee_id', $payee_id)->get('payee_list_no')->toArray());
            $payee_list_codes = explode(',', $input['payee_list_no']);
            //先比较原来的，做删除  差集
            $a = array_diff($payee_codes, $payee_list_codes);
            if (!empty($a))
            {
                DB::table('erp_payee_list')->whereIn('payee_list_no', $a)->delete();
                ErpDistribution::whereIn('distribution_no', $a)->update(['is_used' => 1]);//是否被使用(1未使用，2已使用)',
            }
            //现在新增的
            $b = array_diff($payee_list_codes, $payee_codes);
            if (!empty($b))
            {
                $ids = count($b);
                $count = ErpDistribution::whereIn('distribution_no', $b)->where('verify', 2)->where('customer_id', $this->request->input('customer_id'))->count();
                if ($count != $ids) {
                    return Response::make(['message' => '请不要选择多个客户的单据！', 'status_code' => 422], 422);
                }
                $list = json_decode(json_encode(DB::table('erp_distribution')->whereIn('distribution_no', $b)->get()), true);
                foreach ($list as $k => $v)
                {
                    //运单重复 去重
                    $is_no=DB::table('erp_payee_list')->where('payee_list_no',$v['distribution_no'])->first();
                    if($is_no){
                        continue;
                    }
                    $data[$k]['payee_id'] = $payee_id;
                    $data[$k]['payee_list_code'] = $v['warehousing_code'];
                    $data[$k]['customer_id'] = $v['customer_id'];
                    $data[$k]['payee_list_no'] = $v['distribution_no'];
                    $data[$k]['payee_list_order'] = $v['customer_number'];
                    $data[$k]['payee_list_transfer_order'] = $v['distribution_Transfer_order'];
                    $data[$k]['agent_csm_id'] = $v['csm_id'];
                    $data[$k]['bill_at'] = date('Y-m-d H:i:s');
                    $data[$k]['settlement_at'] = date('Y-m-d H:i:s');
                    $data[$k]['payee_list_amount'] = $v['freight'];
                    $data[$k]['currency_id'] = $v['currency_id'];
                }
                DB::table('erp_payee_list')->insert($data);
                ErpDistribution::whereIn('distribution_no', $b)->update(['is_used' => 2]);
            }
            unset($input['payee_code']);
            unset($input['customer_id']);
            unset($input['payee_id']);
            unset($input['payee_list_no']);
            unset($input['receipt_id']);
            if (isset($input['write_difference']) && $input['write_difference'] > 0)
            {
                $input['difference'] = 2;
            }else{
                $input['difference'] = 1;
            }
            ErpPayee::where('payee_id', $payee_id)->update($input);
            $payee_list_amount = array_sum(\Arr::flatten(ErpPayeeList::where('payee_id', $payee_id)->get('payee_list_amount')->toArray()));
            $input['accounts_receivable'] = $payee_list_amount;
            $input['payee_difference'] = $payee_list_amount - $this->request->input('payee_amount');
            ErpPayee::where('payee_id', $payee_id)->update($input);
            DB::commit();
            return Response::make(['msg' => 'success', 'data' => [], 'status' => 200], 200);
        } catch (\Exception $e) {
            throw new HttpException(422, $e->getMessage());
        }
    }

}
