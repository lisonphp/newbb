<?php

namespace Newbee\Finance\Requests\Payee;

use Illuminate\Foundation\Http\FormRequest;

class UpdateErpPayeeRequest extends FormRequest
{
    /**
     * 获取应用于请求的验证规则.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'accmonth' => 'required|string',
            'payee_amount' => 'required|string',
            'currency_id' => 'nullable|integer',
            'payee_account' => 'required|integer',
            'write_currency' => 'nullable|integer',
            'rate' => 'nullable|string',
            'write_amount' => 'nullable|string',
            'write_difference' => 'nullable|string',
            'water_bill_img' => 'nullable|string',
            'invoice_img' => 'nullable|string',
            'desc' => 'nullable|string',
            'inside_desc' => 'nullable|string',
            'payee_list_no' => 'required|string',
        ];
    }

    /**
     * 获取已定义验证规则的错误消息
     *
     * @return array
     */
    public function messages()
    {
        return [
            'accmonth.required' => '会计期间不能为空！',
            'payee_amount.required' => '收款金额不能为空！',
            'payee_account.required' => '收款账户不能为空！',
            'payee_list_no.required' => '运单号不能为空！',
        ];
    }
}
