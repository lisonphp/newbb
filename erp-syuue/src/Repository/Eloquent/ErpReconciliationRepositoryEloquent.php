<?php

namespace Newbee\Finance\Repository\Eloquent;

use Newbee\Finance\Models\ErpReconciliation;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;

/**
 * Class TrackRepositoryEloquent.
 *
 * @package namespace Erp\Finance\Repository\Eloquent;
 */
class ErpReconciliationRepositoryEloquent extends BaseRepository implements \Newbee\Finance\Repository\Contract\ErpReconciliationRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return ErpReconciliation::class;
    }



    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

}
