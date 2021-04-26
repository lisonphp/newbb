<?php
namespace Newbee\Finance\Transformers;

use Edu\Permission\Models\AdminUser;
use League\Fractal\TransformerAbstract;
use Newbee\Basics\Models\ErpAccount;
use Newbee\Basics\Models\ErpAgent;
use Newbee\Basics\Models\ErpCsm;
use Newbee\Basics\Models\ErpCurrency;
use Newbee\Basics\Models\ErpCustomer;
use Newbee\Finance\Models\ErpPayee;

class ErpPayeeTransformers extends TransformerAbstract
{
    /**
     * 定义接口返回的字段
     * @param $item
     * @return array
     */
    public function transform(ErpPayee $item)
    {
        return [
            'payee_id' => $item->payee_id,
            'payee_code' => $item->payee_code,
            'accmonth' => $item->accmonth,
            'customer_id' => $item->customer_id,
            'customer_name'  => ErpCustomer::where('customer_id',$item->customer_id)->value('customer_name'),
            'accounts_receivable' => $item->accounts_receivable,
            'payee_difference' => $item->payee_difference,
            'payee_amount' => $item->payee_amount,
            'currency_id' => $item->currency_id,
            'currency_name' => ErpCurrency::where('currency_id',$item->currency_id)->value('currency_name'),
            'payee_account' => $item->payee_account,
            'account_name' => ErpAccount::where('account_id', $item->payee_account)->value('account_name'),
            'write_currency' => $item->write_currency,
            'write_currency_name' => ErpCurrency::where('currency_id',$item->write_currency)->value('currency_name'),
            'rate' => $item->rate,
            'write_amount' => $item->write_amount,
            'write_difference' => $item->write_difference,
            'water_bill_img' => $item->water_bill_img,
            'invoice_img' => $item->invoice_img,
            'desc' => $item->desc,
            'inside_desc' => $item->inside_desc,
            'examine' => $item->examine,
            'producer_id' => $item->producer_id,
            'producer_name' => AdminUser::where('id',$item->producer_id)->value('name'),
            'payee_list' => $item->payee_list->map(function ($item) {
                return array_merge($item->toArray(), [
                    'customer_name'  => ErpCustomer::where('customer_id',$item->customer_id)->value('customer_name'),
                    'agent_name' => ErpAgent::where('agent_id',$item->agent_id)->value('agent_name'),
                    'agent_csm_name' => ErpCsm::where('csm_id',$item->agent_csm_id)->value('csm_name'),
                    'currency_name' => ErpCurrency::where('currency_id',$item->currency_id)->value('currency_name'),
                ]);
            }),
            'examine_id' => $item->examine_id,
            'examine_name' => AdminUser::where('id',$item->examine_id)->value('name'),
            'examine_at' => $item->examine_at,
            'payee_update_at' => $item->payee_update_at,
            'payee_created_at' => $item->payee_created_at->toDateTimeString(),
        ];
    }
}
