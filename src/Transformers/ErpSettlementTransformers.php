<?php
namespace Newbee\Report\Transformers;

use Edu\Permission\Models\AdminUser;
use League\Fractal\TransformerAbstract;
use Newbee\Basics\Models\ErpAccount;
use Newbee\Basics\Models\ErpAgent;
use Newbee\Basics\Models\ErpCsm;
use Newbee\Basics\Models\ErpCurrency;
use Newbee\Basics\Models\ErpCustomer;
use Newbee\Report\Models\ErpSettlement;
use Newbee\Report\Models\ErpPayee;

class ErpSettlementTransformers extends TransformerAbstract
{
    /**
     * 定义接口返回的字段
     * @param $item
     * @return array
     */
    public function transform(ErpSettlement $item)
    {
        return [
            'settlement_id'=>$item->settlement_id,
            'customer_coding'=>$item->customer_coding,
            'customer_name'=>$item->customer_name,
            'csm_id'=>ErpCsm::where('csm_id',$item->csm_id)->value('csm_name'),
            'currency_id'=>ErpCurrency::where('currency_id',$item->currency_id)->value('currency_name'),
            'balance'=>$item->balance,
            'no_write_amount'=>$item->no_write_amount,
            'write_amount'=>$item->write_amount,
            'total'=>$item->total,
            'no_reconciliation_amout'=>$item->no_reconciliation_amout,
            'desc'=>$item->desc,
            'payee_list'=>ErpPayee::where('customer_id',$item->customer_id)
            ->orderBy('payee_id', 'desc')
            ->where('examine',2) //2已审核
            ->get()
            ->map(function ($item) {
                // return array_merge($item->toArray(), [
                //     'operator_name'  => AdminUser::where('id',$item->operator_id)->value('name'),
                // ]);
                return $item;
            }),
        ];
        /* return [
            'customer_name'  => ErpCustomer::where('customer_id',$item->customer_id)->value('customer_name'),
            'currency_name' => ErpCurrency::where('currency_id',$item->currency_id)->value('currency_name'),
            'account_name' => ErpAccount::where('account_id', $item->payee_account)->value('account_name'),
            'write_currency_name' => ErpCurrency::where('currency_id',$item->write_currency)->value('currency_name'),
            'producer_name' => AdminUser::where('id',$item->producer_id)->value('name'),
            'payee_list' => $item->payee_list->map(function ($item) {
                return array_merge($item->toArray(), [
                    'customer_name'  => ErpCustomer::where('customer_id',$item->customer_id)->value('customer_name'),
                    'agent_name' => ErpAgent::where('agent_id',$item->agent_id)->value('agent_name'),
                    'agent_csm_name' => ErpCsm::where('csm_id',$item->agent_csm_id)->value('csm_name'),
                    'currency_name' => ErpCurrency::where('currency_id',$item->currency_id)->value('currency_name'),
                ]);
            }),
            'examine_name' => AdminUser::where('id',$item->examine_id)->value('name'),
            'examine_at' => $item->examine_at,
            'payee_update_at' => $item->payee_update_at,
            'payee_created_at' => $item->payee_created_at->toDateTimeString(),
        ]; */
    }
}
