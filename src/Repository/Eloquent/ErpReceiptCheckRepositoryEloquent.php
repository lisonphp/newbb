<?php

namespace Newbee\Report\Repository\Eloquent;

use Newbee\Order\Models\ErpDistribution;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;

/**
 * Class TrackRepositoryEloquent.
 *
 * @package namespace Erp\Distribution\Repository\Eloquent;
 */
class ErpReceiptCheckRepositoryEloquent extends BaseRepository implements \Newbee\Report\Repository\Contract\ErpReceiptCheckRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return ErpDistribution::class;
    }



    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

}
