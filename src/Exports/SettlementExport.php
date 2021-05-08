<?php

namespace Newbee\Finance\Exports;

use Illuminate\Contracts\Support\Responsable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Excel;
use Illuminate\Http\Request;
use Newbee\Basics\Models\ErpCountry;
use Newbee\Basics\Models\ErpCurrency;
use Newbee\Basics\Models\ErpCustomer;
use Newbee\Basics\Models\ErpGoodsCategory;
use Newbee\Offer\Models\ErpChannel;

class SettlementExport implements FromQuery, WithHeadings, Responsable, WithMapping
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
        $model =  \DB::table('erp_reconciliation')->join('erp_customer', 'erp_reconciliation.customer_id', '=', 'erp_customer.customer_id')->orderBy('erp_reconciliation.reconciliation_id');
        !empty($input['customer_id']) && $model->where('erp_reconciliation.customer_id',$input['customer_id']);
        !empty($input['reconciliation_date']) && $model->where('erp_reconciliation.reconciliation_date', '<=', $input['reconciliation_date']);
        !empty($input['reconciliation_created_at']) && $model->where('erp_reconciliation.reconciliation_created_at', '<=', $input['reconciliation_created_at']);
        !empty($input['customer_type']) && $model->where('erp_customer.customer_type', $input['customer_type']);
        !empty($input['customer_salesman_id']) && $model->where('erp_customer.customer_salesman_id', $input['customer_salesman_id']);
        !empty($input['unsettled']) && $model->where('erp_reconciliation.unsettled', '!=', $input['unsettled']);
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
        $row->customer_id = ErpCustomer::where('customer_id',$row->customer_id)->value('customer_name');
        $row->examine = $row->examine == 1 ? '未核销' : '已核销';
        $row->channel_id = ErpChannel::where('channel_id',$row->channel_id)->value('channel_chinese');
        $row->country_id = ErpCountry::where('country_id',$row->country_id)->value('country_chinese');
        $row->goods_spec_id = implode(',', \Arr::flatten(ErpGoodsCategory::whereIn('goods_category_id', explode(',', $row->reconciliation_goods_name))->get('goods_category_ch_query')->ToArray()));
        $row->currency_id = ErpCurrency::where('currency_id',$row->currency_id)->value('currency_name');
        $row->expense_category = '运费';

        // 内部排序
        foreach ($this->filter as $request){
            array_push($column,$row->$request);
        }

        return $column;
    }

}
