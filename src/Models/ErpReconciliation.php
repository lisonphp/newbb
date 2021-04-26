<?php

namespace Newbee\Finance\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DateTimeInterface;

/**
 * Class ErpReconciliation
 *
 * @package Petstore30
 *
 * @author  xiaoyi
 *
 * @OA\Schema(
 *     title="ErpReconciliation model",
 *     description="ErpReconciliation model",
 * )
 */

class ErpReconciliation extends Model
{
    use SoftDeletes;

    protected $table = 'erp_reconciliation';

    const UPDATED_AT = 'reconciliation_update_at';
    const CREATED_AT = 'reconciliation_created_at';

    /**
     * 重定义主键
     *
     * @var string
     */
    protected $primaryKey = 'reconciliation_id';

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

    /**
     * 可以被批量赋值的属性。
     *
     * @var array
     */
    protected $fillable = [];
}
