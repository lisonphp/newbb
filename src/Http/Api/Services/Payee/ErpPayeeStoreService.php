<?php
namespace Newbee\Finance\Http\Api\Services\Payee;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Newbee\Finance\Repository\Contract\ErpPayeeRepository;
use Newbee\Finance\Requests\Payee\StoreErpPayeeRequest;
use Newbee\Order\Models\ErpDistribution;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ErpPayeeStoreService
{
    /**
     * @var StoreErpPayeeRequest $request
     */
    protected $request;
    /**
     * @var ErpPayeeRepository
     */
    protected $repository;

    function __construct(StoreErpPayeeRequest $request, ErpPayeeRepository $repository)
    {
        $this->request = $request;
        $this->repository = $repository;
    }

    /**
     * 应收单据和无应收单据 结合在一起
     * @return mixed
     */
    public function store()
    {
        $money = 0.000;
        $data = [];
        // write_currency
        $this->request->validated();
        DB::beginTransaction();
        try {
            $receipt_id=$this->request->input('receipt_id');
            if($receipt_id){
                //应收单据
                $receipt_id = explode(',', $receipt_id);
                $ids = count($receipt_id);
                $count = ErpDistribution::whereIn('distribution_id', $receipt_id)->where('verify', 2)->where('customer_id', $this->request->input('customer_id'))->count();
                if ($count != $ids)
                {
                    return Response::make(['message' => '请不要选择多个客户的单据！', 'status_code' => 422], 422);
                }
                $list = json_decode(json_encode(DB::table('erp_distribution')->whereIn('distribution_id', $receipt_id)->get()), true);
                foreach ($list as $k => $v)
                {
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
                    $money += $v['warehousing_money'];
                }
            }else{
                $money=0;
            }
            //无应收单据
            $post_data = $this->request->except(['receipt_id']);
            $post_data['producer_id'] = \Auth::user()['id'];
            if (isset($post_data['write_difference']) && $post_data['write_difference'] > 0)
            {
                $post_data['difference'] = 2;
            }
            $post_data['accounts_receivable'] = $money;
            $post_data['payee_difference'] = $money - $this->request->input('payee_amount');
            $id = DB::table('erp_payee')->insertGetId($post_data);
            $arr['payee_id'] = $id;
            array_walk($data,function (&$value, $key, $arr) {
                $value = array_merge($value, $arr);
            }, $arr);
            if($receipt_id){
                DB::table('erp_payee_list')->insert($data);
                ErpDistribution::whereIn('distribution_id', $receipt_id)->update(['is_used' => 2]);
            }
            DB::commit();
            return Response::make(['msg' => 'success', 'data' => [],  'status' => 200], 200);
        } catch(\Exception $e) {
            throw new HttpException(422, $e->getMessage());
        }
    }


}
