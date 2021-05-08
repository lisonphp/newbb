<?php

namespace Newbee\Report\Repository\Eloquent;

use Newbee\Report\Models\ErpSettlement;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;

/**
 * Class TrackRepositoryEloquent.
 *
 * @package namespace Erp\Distribution\Repository\Eloquent;
 */
class ErpSettlementRepositoryEloquent extends BaseRepository implements \Newbee\Report\Repository\Contract\ErpSettlementRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return ErpSettlement::class;
    }



    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

}
