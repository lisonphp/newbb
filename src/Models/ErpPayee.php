<?php

namespace Newbee\Finance\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DateTimeInterface;

/**
 * Class ErpPayee
 *
 * @package Petstore30
 *
 * @author  xiaoyi
 *
 * @OA\Schema(
 *     title="ErpPayee model",
 *     description="ErpPayee model",
 * )
 */

class ErpPayee extends Model
{
    use SoftDeletes;

    protected $table = 'erp_payee';

    const UPDATED_AT = 'payee_update_at';
    const CREATED_AT = 'payee_created_at';

    /**
     * 重定义主键
     *
     * @var string
     */
    protected $primaryKey = 'payee_id';

    /**
     * Prepare a date for array / JSON serialization.
     *
     * @param  \DateTimeInterface  $date
     * @return string
     */
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function payee_list()
    {
        return $this->hasMany(ErpPayeeList::class,'payee_id');
    }

    /**
     * 可以被批量赋值的属性。
     *
     * @var array
     */
    protected $fillable = [
        'accmonth', 'customer_id', 'payee_amount', 'currency_id', 'payee_account', 'write_currency', 'rate', 'write_amount', 'write_difference',
        'water_bill_img', 'invoice_img', 'desc', 'inside_desc', 'examine', 'producer_id', 'examine_id', 'examine_at'
    ];
}
