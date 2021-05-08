<?php

namespace Newbee\Finance\Requests\Payee;

use Illuminate\Foundation\Http\FormRequest;

class DeleteErpPayeeRequest extends FormRequest
{

    /**
     * 获取应用于请求的验证规则.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'id'=>'int',
            'Finance_no'=>'string',
            'platform_Finance_no'=>'string'
        ];
    }
}
