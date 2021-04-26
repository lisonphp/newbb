<?php

namespace Newbee\Finance\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DateTimeInterface;

/**
 * Class ErpSettlementNotes
 *
 * @package Petstore30
 *
 * @author  xiaoyi
 *
 * @OA\Schema(
 *     title="ErpSettlementNotes model",
 *     description="ErpSettlementNotes model",
 * )
 */

class ErpSettlementNotes extends Model
{
    use SoftDeletes;

    protected $table = 'erp_settlement_notes';

    const UPDATED_AT = 'settlement_notes_update_at';
    const CREATED_AT = 'settlement_notes_created_at';

    /**
     * 重定义主键
     *
     * @var string
     */
    protected $primaryKey = 'settlement_notes_id';

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
