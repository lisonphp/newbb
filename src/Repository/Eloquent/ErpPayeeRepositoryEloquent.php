<?php

namespace Newbee\Finance\Repository\Eloquent;

use Newbee\Finance\Models\ErpPayee;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;

/**
 * Class TrackRepositoryEloquent.
 *
 * @package namespace Erp\Finance\Repository\Eloquent;
 */
class ErpPayeeRepositoryEloquent extends BaseRepository implements \Newbee\Finance\Repository\Contract\ErpPayeeRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return ErpPayee::class;
    }



    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

}
