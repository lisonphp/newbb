<?php
namespace Newbee\Syuue\Transformers;
use Edu\Permission\Models\AdminUser;
use League\Fractal\TransformerAbstract;
use Newbee\Basics\Models\ErpAgent;
use Newbee\Basics\Models\ErpCountry;
use Newbee\Basics\Models\ErpCsm;
use Newbee\Basics\Models\ErpCurrency;
use Newbee\Basics\Models\ErpCustomer;
use Newbee\Basics\Models\ErpExpenseCategory;
use Newbee\Basics\Models\ErpGoodsCategory;
use Newbee\Basics\Models\ErpProblemCategory;
use Newbee\Offer\Models\ErpChannel;
use Newbee\Offer\Models\ErpChannelCategory;
use Newbee\Order\Models\ErpDistribution;
use Newbee\Order\Models\ErpWarehousingCost;
use Newbee\Order\Models\ErpWarehousingVolume;
use Newbee\Order\Models\Locus;
use Newbee\Order\Models\WaybillTrackingList;

class ErpReceiptCheckTransformers extends TransformerAbstract
{
    /**
     * 定义接口返回的字段
     * @param $item
     * @return array
     */
    public function transform(ErpDistribution $item)
    {
        return [
            'distribution_id' => $item->distribution_id,
            'site_id' => $item->site_id,
            'warehousing_code' => $item->warehousing_code,
            'distribution_no' => $item->distribution_no,
            'distribution_change_order' => $item->distribution_change_order,
            'distribution_Transfer_order' => $item->distribution_Transfer_order,
            'customer_number' => $item->customer_number,
            'customer_id' => $item->customer_id,
            'customer_name'  => ErpCustomer::where('customer_id',$item->customer_id)->value('customer_name'),
            'distribution_status' => $item->distribution_status,
            'status' => $item->status,
            'channel_category_id' => $item->channel_category_id,
            'channel_category_name' => ErpChannelCategory::where('channel_category_id',$item->channel_category_id)->value('channel_category_name'),
            'channel_id' => $item->channel_id,
            'channel_name' => ErpChannel::where('channel_id',$item->channel_id)->value('channel_chinese'),
            'country_id' => $item->country_id,
            'country_name' => ErpCountry::where('country_id',$item->country_id)->value('country_chinese'),
            'distribution_insert_at' => $item->distribution_insert_at,
            'distribution_end_at' => $item->distribution_end_at,
            'distribution_type_goods' => $item->distribution_type_goods,
            'distribution_number' => $item->distribution_number,
            'goods_spec_id' => $item->goods_spec_id,
            'goods_spec_name' => ErpGoodsCategory::whereIn('goods_category_id', explode(',', $item->goods_spec_id))->get('goods_category_ch_query'),
            'freight' => $item->freight,
            'warehousing_weight' => $item->warehousing_weight,
            'warehousing_square_division' => $item->warehousing_square_division,
            'warehousing_settlement_weight' => $item->warehousing_settlement_weight,
            'distribution_square_number' => $item->distribution_square_number,
            'square_division_division' => $item->square_division_division,
            'warehousing_money' => $item->warehousing_money,
            'warehousing_cost' => $item->warehousing_cost,
            'distribution_goods_name' => $item->distribution_goods_name,
            'currency_id' => $item->currency_id,
            'currency_name' => ErpCurrency::where('currency_id',$item->currency_id)->value('currency_name'),
            'waybill_tracking_list' => WaybillTrackingList::where('waybill_no',$item->distribution_no)->orderBy('waybill_tracking_list_id', 'desc')->get()->map(function ($item) {
                return array_merge($item->toArray(), [
                    'reminder_name' => AdminUser::where('id', $item->reminder_id)->value('name'),
                    'operator_name' => AdminUser::where('id', $item->operator_id)->value('name'),
                    'problem_category_name' => ErpProblemCategory::where('problem_category_id', $item->problem_category_id)->value('problem_category_name'),
                ]);
            }),
            'locus_list' => Locus::where('waybill_no',$item->distribution_no)->orderBy('locus_id', 'desc')->get()->map(function ($item) {
                return array_merge($item->toArray(), [
                    'operator_name'  => AdminUser::where('id',$item->operator_id)->value('name'),
                ]);
            }),
            'distribution_mark' => $item->distribution_mark,
            'agent_id' => $item->agent_id,
            'agent_name' => ErpAgent::where('agent_id',$item->agent_id)->value('agent_name'),
            'distribution_channel_id' => $item->distribution_channel_id,
            'distribution_channel_name'  => ErpChannel::where('channel_id',$item->distribution_channel_id)->value('channel_chinese'),
            'distribution_weight' => $item->distribution_weight,
            'distribution_square_division' => $item->distribution_square_division,
            'distribution_settlement_weight' => $item->distribution_settlement_weight,
            'distribution_amount' => $item->distribution_amount,
            'problem_category_id' => $item->problem_category_id,
            'problem_category_name' => ErpProblemCategory::where('problem_category_id',$item->problem_category_id)->value('problem_category_name'),
            'warehousing_volume' => ErpWarehousingVolume::where('warehousing_waybill_no',$item->distribution_no)->get(),
            'other_expenses_list' => ErpWarehousingCost::where('warehousing_waybill_no',$item->distribution_no)->get()->map(function ($item) {
                return array_merge($item->toArray(), [
                    'category_name' => ErpExpenseCategory::where('expense_category_id',$item->category)->value('expense_category_name'),
                    'csm_name'  => ErpCurrency::where('currency_id',$item->csm_id)->value('currency_name'),
                ]);
            }),
            'csm_id' => $item->csm_id,
            'csm_name' => ErpCsm::where('csm_id',$item->csm_id)->value('csm_name'),
            'unit_price' => $item->unit_price,
            'verify' => $item->verify,
            'postcode' => $item->postcode,
            'distribution_gross_profit' => $item->distribution_gross_profit,
            'distribution_value' => $item->distribution_value,
            'other_expenses' => $item->other_expenses,
            'desc' => $item->desc,
            'inside_desc' => $item->inside_desc,
            'shipping_notes' => $item->shipping_notes,
            'customs_type' => $item->customs_type,
            'country_code' => $item->country_code,
            'pick_clerk_id' => $item->pick_clerk_id,
            'pick_clerk_name' => AdminUser::where('id',$item->pick_clerk_id)->value('name'),
            'salesman_id' => $item->salesman_id,
            'waybill_status' => $item->waybill_status,
            'salesman_name' => AdminUser::where('id',$item->salesman_id)->value('username'),
            'detention_explain' => $item->detention_explain,
            'identification' => $item->identification,
            'producer_id' => $item->producer_id,
            'examine_id' => $item->examine_id,
            'producer_name' => AdminUser::where('id',$item->producer_id)->value('name'),
            'examine_name' => AdminUser::where('id',$item->examine_id)->value('name'),
            'producer_at' => $item->producer_at,
            'distribution_update_at' => $item->distribution_update_at->toDateTimeString(),
            'distribution_created_at' => $item->distribution_created_at->toDateTimeString(),
        ];
    }
}
