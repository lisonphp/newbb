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
use Newbee\Basics\Models\ErpAccount;
use Newbee\Basics\Models\ErpCurrency;
use Newbee\Basics\Models\ErpCustomer;
use Newbee\Order\Models\ErpDistribution;

class PayeeExport implements FromQuery, WithHeadings, Responsable, WithMapping
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
        !empty($input['payee_code']) && $model->where('payee_code',$input['payee_code']);
        !empty($input['customer_id']) && $model->where('customer_id',$input['customer_id']);
        !empty($input['created_at']) && $model->where('created_at', '>=', $input['created_at']);
        !empty($input['examine_at']) && $model->where('examine_at', '<=', $input['examine_at']);
        !empty($input['examine']) && $model->where('examine',$input['examine']);
        !empty($input['producer_id']) && $model->where('producer_id',$input['producer_id']);
        !empty($input['payee_account']) && $model->where('payee_account',$input['payee_account']);
        !empty($input['difference']) && $model->where('difference',$input['difference']);

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
        $row->currency_id = ErpCurrency::where('currency_id',$row->currency_id)->value('currency_name');
        $row->examine = $row->examine == 1 ? '未审核' : '已审核';
        $row->write_currency = ErpCurrency::where('currency_id',$row->write_currency)->value('currency_name');
        $row->producer_id = AdminUser::where('id', 'producer_id')->value('name');
        $row->examine_id = AdminUser::where('id', 'examine_id')->value('name');
        $row->payee_account = ErpAccount::where('account_id', 'payee_account')->value('account_name');

        // 内部排序
        foreach ($this->filter as $request){
            array_push($column,$row->$request);
        }

        return $column;
    }

}
