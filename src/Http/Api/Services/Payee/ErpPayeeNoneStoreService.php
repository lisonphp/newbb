<?php
namespace Newbee\Finance\Http\Api\Services\Payee;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Newbee\Finance\Repository\Contract\ErpPayeeRepository;
use Newbee\Finance\Requests\Payee\StoreErpPayeeRequest;
use Newbee\Finance\Requests\Payee\NoneStoreErpPayeeRequest;
use Newbee\Order\Models\ErpDistribution;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ErpPayeeNoneStoreService
{
    /**
     * @var StoreErpPayeeRequest $request
     */
    protected $request;
    /**
     * @var ErpPayeeRepository
     */
    protected $repository;

    function __construct(NoneStoreErpPayeeRequest $request, ErpPayeeRepository $repository)
    {
        $this->request = $request;
        $this->repository = $repository;
    }

    /**
     * 无应收单据收款-保存
     * @return mixed
     */
    public function none_store()
    {
        $money = 0.000;
        $data = [];
        // write_currency
        $this->request->validated();
        DB::beginTransaction();
        try {
            $receipt_id = explode(',', $this->request->input('receipt_id'));
            $ids = count($receipt_id);
            // $count = ErpDistribution::whereIn('distribution_id', $receipt_id)->where('verify', 2)->where('customer_id', $this->request->input('customer_id'))->count();
            // var_dump($receipt_id);
            // echo 'a';
            // var_dump($ids);
            // echo 'a';
            // var_dump($count);
            // return Response::make(['message' => '截断', 'status_code' => 422], 422);
            // if ($count != $ids)
            // {
            //     return Response::make(['message' => '请不要选择多个客户的单据！', 'status_code' => 422], 422);
            // }
            $post_data = $this->request->except(['receipt_id']);
            // var_dump($post_data);
            // return Response::make(['message' => '截断222', 'status_code' => 422], 422);
            $post_data['producer_id'] = \Auth::user()['id'];
            if (isset($post_data['write_difference']) && $post_data['write_difference'] > 0)
            {
                $post_data['difference'] = 2;
            }
            
            //money=入仓金额，accounts_receivable=应收金额
            $post_data['accounts_receivable'] = $money;
            $post_data['payee_difference'] = $money - $this->request->input('payee_amount');
            $id = DB::table('erp_payee')->insertGetId($post_data);
            
            ErpDistribution::whereIn('distribution_id', $receipt_id)->update(['is_used' => 2]);
            DB::commit();
            return Response::make(['msg' => 'success', 'data' => [],  'status' => 200], 200);
        } catch(\Exception $e) {
            throw new HttpException(422, $e->getMessage());
        }
    }
}
