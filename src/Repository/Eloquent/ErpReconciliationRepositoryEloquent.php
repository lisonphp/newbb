<?php

namespace Newbee\Report\Repository\Eloquent;

use Newbee\Report\Models\ErpReconciliation;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;

/**
 * Class TrackRepositoryEloquent.
 *
 * @package namespace Erp\Report\Repository\Eloquent;
 */
class ErpReconciliationRepositoryEloquent extends BaseRepository implements \Newbee\Report\Repository\Contract\ErpReconciliationRepository
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
