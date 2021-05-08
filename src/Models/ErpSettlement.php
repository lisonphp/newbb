<?php

namespace Newbee\Finance\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DateTimeInterface;

/**
 * Class ErpSettlement
 *
 * @package Petstore30
 *
 * @author  xiaoyi
 *
 * @OA\Schema(
 *     title="ErpSettlement model",
 *     description="ErpSettlement model",
 * )
 */

class ErpSettlement extends Model
{
    use SoftDeletes;

    protected $table = 'erp_settlement';
    // protected $table_prefix='erp_';

    const UPDATED_AT = 'settlement_update_at';
    const CREATED_AT = 'settlement_created_at';

    /**
     * 重定义主键
     *
     * @var string
     */
    protected $primaryKey = 'settlement_id';

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
        return $this->hasMany(ErpPayee::class,'customer_id');
    }


    /**
     * 可以被批量赋值的属性。
     *
     * @var array
     */
    protected $fillable = [
        'settlement_id', 'customer_id', 'customer_coding', 'customer_name', 'examine', 'csm_id', 'currency_id', 'balance', 'no_write_amount',
        'write_amount', 'total', 'no_reconciliation_amout', 'desc', 'settlement_update_at', 'settlement_created_at', 'deleted_at'
    ];
}
