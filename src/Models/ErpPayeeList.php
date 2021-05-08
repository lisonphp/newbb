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
 *     title="ErpPayeeList model",
 *     description="ErpPayeeList model",
 * )
 */

class ErpPayeeList extends Model
{
    use SoftDeletes;

    protected $table = 'erp_payee_list';

    const UPDATED_AT = 'payee_list_update_at';
    const CREATED_AT = 'payee_list_created_at';

    /**
     * 重定义主键
     *
     * @var string
     */
    protected $primaryKey = 'payee_list_id';

}
