<?php
namespace Newbee\Finance\Transformers;

use League\Fractal\TransformerAbstract;
use Newbee\Finance\Models\ErpReconciliation;

class ErpReconciliationTransformers extends TransformerAbstract
{
    /**
     * 定义接口返回的字段
     * @param $item
     * @return array
     */
    public function transform(ErpReconciliation $item)
    {
        return [
            'reconciliation_id' => $item->reconciliation_id,
            'customer_id' => $item->customer_id,
            'examine' => $item->examine,
            'reconciliation_date' => $item->reconciliation_date,
            'reconciliation_no' => $item->reconciliation_no,
            'reconciliation_transfer_order' => $item->reconciliation_transfer_order,
            'reconciliation_number' => $item->reconciliation_number,
            'reconciliation_weight' => $item->reconciliation_weight,
            'reconciliation_square_division' => $item->reconciliation_square_division,
            'reconciliation_settlement_weight' => $item->reconciliation_settlement_weight,
            'channel_id' => $item->channel_id,
            'country_id' => $item->country_id,
            'reconciliation_goods_name' => $item->reconciliation_goods_name,
            'reconciliation_amount' => $item->reconciliation_amount,
            'currency_id' => $item->currency_id,
            'expense_category' => $item->expense_category,
            'desc' => $item->desc,
            'reconciliation_update_at' => $item->reconciliation_update_at,
            'reconciliation_created_at' => $item->reconciliation_created_at->toDateTimeString(),
        ];
    }
}
