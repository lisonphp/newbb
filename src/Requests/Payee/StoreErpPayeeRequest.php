<?php

namespace Newbee\Finance\Requests\Payee;

use Illuminate\Foundation\Http\FormRequest;

class StoreErpPayeeRequest extends FormRequest
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
            'customer_id' => 'required|integer',
            'payee_amount' => 'required',
            'currency_id' => 'nullable|integer',
            'payee_account' => 'required|integer',
            'write_currency' => 'nullable|integer',
            'rate' => 'nullable',
            'write_amount' => 'nullable',
            'write_difference' => 'nullable',
            'water_bill_img' => 'nullable|string',
            'invoice_img' => 'nullable|string',
            'desc' => 'nullable|string',
            'inside_desc' => 'nullable|string',
            // 'receipt_id' => 'required|string',
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
            'customer_id.required' => '客户id不能为空！',
            'payee_amount.required' => '收款金额不能为空！',
            'payee_account.required' => '收款账户不能为空！',
            // 'receipt_id.required' => '单票入仓核单id不能9为空！',
        ];
    }

    /**
     * 配置验证实例
     *
     * @param \Illuminate\Validation\Validator $validator
     * @return void
     */
    public function withValidator($validator)
    {
        if ($validator->errors()->all()) {
            return;
        }
        $payee_code = 'FRE'.date('YmdHis');
        $validator->after(function ($validator) use ($payee_code) {
            $this->merge([
                'payee_code' => $payee_code
            ]);
        });
    }

}
