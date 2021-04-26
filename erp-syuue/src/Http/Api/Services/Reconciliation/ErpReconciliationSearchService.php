<?php
namespace Newbee\Finance\Http\Api\Services\Reconciliation;

use Newbee\Finance\Http\Api\Base\AbstractSearchService;
use Newbee\Finance\Repository\Contract\ErpReconciliationRepository;
use Newbee\Finance\Repository\Criteria\ErpReconciliationSearchCriteria;
use Newbee\Finance\Requests\Reconciliation\SearchErpReconciliationRequest;

class ErpReconciliationSearchService extends AbstractSearchService
{
    /**
     *@var SearchErpReconciliationRequest $request
     */
    protected $request;

    /**
     *@var ErpReconciliationRepository $repository
     */
    protected $repository;

    public function __construct(SearchErpReconciliationRequest $request, ErpReconciliationRepository $repository)
    {
        $this->request = $request;
        $this->repository = $repository;
    }

    protected function getSearchCriteriaClassName(): string
    {
        return ErpReconciliationSearchCriteria::class;
    }

    /**
     * MySQL WHERE OR
     * @return array|string[]
     */
    protected function getOrConditionFields(): array
    {
        return [
            'reconciliation_no'
        ];
    }

    /**
     * MySQL WHERE AND
     * @return array|string[]
     */
    protected function getAndConditionFields(): array
    {
        return [
            'customer_id',
            'examine'
        ];
    }

    /*
     * 区间，模糊，大小查询参数
     */
    protected function fieldStorage(): array
    {
        return [
            'reconciliation_date',
        ];
    }

    /*
     * 区间，模糊，大小查询
     */
    protected function fieldSearchable(): array
    {
        return [
            'reconciliation_date' => 'between',
        ];
    }
}
