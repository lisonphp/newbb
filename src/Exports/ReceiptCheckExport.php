<?php

namespace Newbee\Finance\Exports;

use Edu\Permission\Auth\AdminUser;
use Illuminate\Contracts\Support\Responsable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Excel;
use Illuminate\Http\Request;
use Newbee\Basics\Models\ErpAgent;
use Newbee\Basics\Models\ErpCountry;
use Newbee\Basics\Models\ErpCsm;
use Newbee\Basics\Models\ErpCurrency;
use Newbee\Basics\Models\ErpCustomer;
use Newbee\Basics\Models\ErpGoodsCategory;
use Newbee\Basics\Models\ErpProblemCategory;
use Newbee\Basics\Models\ErpSite;
use Newbee\Offer\Models\ErpChannel;
use Newbee\Offer\Models\ErpChannelCategory;
use Newbee\Order\Models\ErpDistribution;

class ReceiptCheckExport implements FromQuery, WithHeadings, Responsable, WithMapping
{
    /**
     * @var 工作表名称
     */
    private $title = '工作表';

    /**
     * @var 表头
     */
    protected $headings = [];

    /**
     * @var 字段过滤
     */
    protected $filter;

    /**
     * @var request
     */
    protected $request;

    /**
     * @var 导出文件格式
     */
    private $writerType = Excel::CSV;

    use Exportable;

    function __construct(Request $request)
    {
        ini_set ("memory_limit","-1");
        ini_set ("max_execution_time","-1");
        $heads = array_column(json_decode($request->input('data'),true),'headings');
        $filters = array_column(json_decode($request->input('data'),true),'filter');
        list($this->request, $this->headings, $this->filter, $writerType) = [$request->input(), $heads, $filters, ['xlsx'=> Excel::XLSX, 'csv'=> Excel::CSV, 'xls'=> Excel::XLS]];
        isset($writerType[$request->input('writerType')]) && $this->writerType = $writerType[$request->input('writerType')];
    }



    /**
     * 查询
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder
     */
    public function query()
    {
        $input = Request()->input();
        $model =  ErpDistribution::query();
        !empty($input['csm_id']) && $model->where('csm_id',$input['csm_id']);
        !empty($input['verify']) && $model->where('verify',$input['verify']);
        !empty($input['pick_clerk_id']) && $model->where('pick_clerk_id',$input['pick_clerk_id']);
        !empty($input['keyword']) && $model->where('distribution_no',$input['keyword']);
        !empty($input['salesman_id']) && $model->where('salesman_id',$input['salesman_id']);
        !empty($input['channel_category_id']) && $model->where('channel_category_id',$input['channel_category_id']);
        !empty($input['channel_id']) && $model->where('channel_id',$input['channel_id']);
        !empty($input['problem_category_id']) && $model->where('problem_category_id',$input['problem_category_id']);
        !empty($input['country_id']) && $model->where('country_id',$input['country_id']);
        !empty($input['agent_id']) && $model->where('agent_id',$input['agent_id']);
        !empty($input['site_id']) && $model->where('site_id',$input['site_id']);
        !empty($input['status']) && $model->where('status',$input['status']);
        !empty($input['waybill_status']) && $model->where('waybill_status',$input['waybill_status']);
        !empty($input['identification']) && $model->where('identification',$input['identification']);
        !empty($input['customs_type']) && $model->where('customs_type',$input['customs_type']);
        !empty($input['distribution_change_order']) && $model->where('distribution_change_order',$input['distribution_change_order']);
        !empty($input['card_number']) && $model->where('card_number',$input['card_number']);
        !empty($input['customer_id']) && $model->where('customer_id',$input['customer_id']);
        !empty($input['site_id']) && $model->where('site_id',$input['site_id']);
        !empty($input['distribution_insert_at']) && $model->where('distribution_insert_at', '>=', $input['distribution_insert_at']);
        !empty($input['distribution_end_at']) && $model->where('distribution_end_at', '<=', $input['distribution_end_at']);
        !empty($input['desc']) && $model->where('desc', 'like', '%'.$input['desc'].'%');
        return $model;
    }

    /**
     * 表头
     * @return string[]
     */
    public function headings(): array
    {
        return $this->headings;
    }

    /**
     * @param mixed $row
     * @return array
     */
    public function map($row): array
    {
        $column = [];

        /**
         * 处理你的业务和字段赋值
         */
        $row->site_id = ErpSite::where('site_id',$row->site_id)->value('site_name');
        $row->agent_id = ErpAgent::where('agent_id',$row->agent_id)->value('agent_name');
        $row->customer_id = ErpCustomer::where('customer_id',$row->customer_id)->value('customer_name');
        $row->status = $row->status == 1 ? '正常' : '扣货';
        $row->distribution_status =  $row->distribution_status == 1 ? '未生成出仓单' : '已生成出仓单';
        $row->channel_category_id = ErpChannelCategory::where('channel_category_id',$row->channel_category_id)->value('channel_category_name');
        $row->channel_id = ErpChannel::where('channel_id',$row->channel_id)->value('channel_chinese');
        $row->country_id = ErpCountry::where('country_id',$row->country_id)->value('country_chinese');
        $row->distribution_type_goods = $row->distribution_type_goods == 1 ? '包裹' : ($row->distribution_type_goods == 2 ? '文件' : 'PAK袋');
        $row->goods_spec_id = implode(',', \Arr::flatten(ErpGoodsCategory::whereIn('goods_category_id', explode(',', $row->goods_spec_id))->get('goods_category_ch_query')->ToArray()));
        $row->currency_id = ErpCurrency::where('currency_id',$row->currency_id)->value('currency_name');
        $row->distribution_mark = $row->distribution_mark == 1 ? '未分货' : ($row->distribution_mark == 2 ? '系统自动分货' : '手工调整分货');
        $row->distribution_channel_id = ErpChannel::where('channel_id',$row->distribution_channel_id)->value('channel_chinese');
        $row->csm_id = ErpCsm::where('csm_id',$row->csm_id)->value('csm_name');
        $row->problem_category_id = ErpProblemCategory::where('problem_category_id',$row->problem_category_id)->value('problem_category_name');
        $row->customs_type = $row->customs_type == 1 ? '不需要报关' : '一般贸易报关';
        $row->pick_clerk_id = AdminUser::where('id',$row->pick_clerk_id)->value('name');
        $row->salesman_id = AdminUser::where('id',$row->salesman_id)->value('name');
        $row->identification = $row->identification == 1 ? '清关延误标志' : '问题提醒标志';
        $row->verify = $row->verify == 1 ? '未核单' : '已核单';
        switch ($row->waybill_status)
        {
            case 1 : $row->waybill_status = '入仓';
                break;
            case 2 : $row->waybill_status = '装箱转运';
                break;
            case 3 : $row->waybill_status = '退货';
                break;
            case 4 : $row->waybill_status = '出仓';
                break;
            case 5 : $row->waybill_status = '准备转运';
                break;
            case 6 : $row->waybill_status = '转运中';
                break;
            case 7 : $row->waybill_status = '到达转运地';
                break;
            default:
        }

        // 内部排序
        foreach ($this->filter as $request){
            array_push($column,$row->$request);
        }

        return $column;
    }

}
