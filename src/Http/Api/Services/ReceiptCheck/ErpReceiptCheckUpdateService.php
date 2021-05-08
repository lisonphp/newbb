<?php
namespace Newbee\Finance\Http\Api\Services\ReceiptCheck;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Newbee\Finance\Repository\Contract\ErpReceiptCheckRepository;
use Newbee\Finance\Requests\ReceiptCheck\UpdateErpReceiptCheckRequest;
use Newbee\Order\Models\ErpDistribution;
use Newbee\Order\Models\ErpWarehousingCost;
use Newbee\Order\Models\ErpWarehousingVolume;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ErpReceiptCheckUpdateService
{
    /**
     * @var UpdateErpReceiptCheckRequest $request
     */
        protected $request;
        /**
         * @var ErpReceiptCheckRepository
         */
        protected $repository;

        function __construct(UpdateErpReceiptCheckRequest $request, ErpReceiptCheckRepository $repository)
        {
            $this->request = $request;
            $this->repository = $repository;
        }

        /**
         * 单票入仓核单-调整-保存
         * @return mixed
         */
        public function update($receipt_check_id)
        {
            $containers = 0;
            $container = 0;
            $this->request->validated();
            $data = $this->request->input();
            // var_dump($data);
            $lists = ErpDistribution::where('distribution_id',$receipt_check_id)->where('verify', 1)->value('distribution_no');
            // var_dump($lists);
            // return 99;
            if(empty($lists)){
                return Response::make(['message' => '已核单的单据，不能再次调整，如需调整请先反核单！', 'status_code' => 422], 422);
            }
            DB::beginTransaction();
            try {
                //材积修改
                if (!empty($this->request->input('warehousing_volume'))) {
                    $warehousing_volume = json_decode($this->request->input('warehousing_volume'),true);
                    foreach ($warehousing_volume as $field){
                        \Validator::make($field, [
                            'warehousing_volume_actual_weight' => 'required',
                            'warehousing_volume_length' => 'required',
                            'warehousing_volume_width' => 'required',
                            'warehousing_volume_high' => 'required',
                            'warehousing_volume_number' => 'required',
                            'warehousing_volume_material_area' => 'required'
                        ])->validate();
                    }
                    foreach ($warehousing_volume as $k => &$v){
                        $container[$k] = ($v['warehousing_volume_length']/100) * ($v['warehousing_volume_width']/100) * ($v['warehousing_volume_high']/100) * $v['warehousing_volume_number'];
                        $v['warehousing_waybill_no'] = $lists;//运单号
                    }
                    ErpWarehousingVolume::where('warehousing_waybill_no',$lists)->delete();
                    ErpWarehousingVolume::insert($warehousing_volume);
                    if($container){
                        $containers = array_sum($container);
                    }
                }else{
                    ErpWarehousingVolume::where('warehousing_waybill_no',$lists)->delete();
                }
                //其他费用
                if (!empty($this->request->input('other_expenses_list'))) {
                    $warehousing_cost = json_decode($this->request->input('other_expenses_list'), true);
                    foreach ($warehousing_cost as $field) {
                        \Validator::make($field, [
                            'category' => 'required',
                            'csm_id' => 'required',
                            'cost' => 'required'
                        ])->validate();
                    }
                    foreach ($warehousing_cost as $k => &$v) {
                        $v['warehousing_waybill_no'] = $lists;
                    }
                    ErpWarehousingCost::where('warehousing_waybill_no',$lists)->delete();
                    ErpWarehousingCost::insert($warehousing_cost);
                }else{
                    ErpWarehousingCost::where('warehousing_waybill_no',$lists)->delete();
                }
                unset($data['distribution_no']);
                unset($data['other_expenses_list']);
                unset($data['warehousing_volume']);
                //主表
                $data['warehousing_square_division'] = sprintf('%.3f',$containers);//分货材重
                ErpDistribution::where('distribution_id',$receipt_check_id)->update($data);
                DB::commit();
                return Response::make(['msg' => 'success', 'data' => [],  'status' => 200], 200);
            } catch(\Exception $e) {
                throw new HttpException(422, $e->getMessage());
            }
        }
}
