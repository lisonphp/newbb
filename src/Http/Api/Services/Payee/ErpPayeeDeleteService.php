<?php
namespace Newbee\Finance\Http\Api\Services\Payee;

use Illuminate\Support\Facades\DB;
use Newbee\Finance\Models\ErpPayeeList;
use Newbee\Finance\Repository\Contract\ErpPayeeRepository;
use Newbee\Finance\Requests\Payee\DeleteErpPayeeRequest;
use Newbee\Order\Models\ErpDistribution;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ErpPayeeDeleteService
{
    /**
     * @var DeleteErpPayeeRequest $request
     */
    protected $request;
    /**
     * @var ErpPayeeRepository
     */
    protected $repository;

    function __construct(DeleteErpPayeeRequest $request, ErpPayeeRepository $repository)
    {
        $this->request = $request;
        $this->repository = $repository;
    }

    /**
     * 删除
     * @return mixed
     */
    public function delete($payee_list_id)
    {
        $this->request->validated();
        DB::beginTransaction();
        try {
            // $payee_list_no = ErpPayeeList::where('payee_id', $payee_id)->get('payee_list_no')->toArray();
            // DB::table('erp_payee')->where('payee_id',$payee_id)->delete();
            // DB::table('erp_payee_list')->where('payee_id',$payee_id)->delete();
            $payee_list_no = ErpPayeeList::where('payee_list_id', $payee_list_id)->get('payee_list_no')->toArray();
            DB::table('erp_payee_list')->where('payee_list_id',$payee_list_id)->delete();
            ErpDistribution::whereIn('distribution_no', $payee_list_no)->update(['is_used' => 1]);
            DB::commit();
            return Response::make(['msg' => 'success', 'data' => [],  'status' => 200], 200);
        } catch(\Exception $e) {
            throw new HttpException(422, $e->getMessage());
        }
    }
}
