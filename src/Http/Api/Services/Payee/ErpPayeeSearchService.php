<?php
namespace Newbee\Finance\Http\Api\Services\Payee;

use Newbee\Finance\Http\Api\Base\AbstractSearchService;
use Newbee\Finance\Repository\Contract\ErpPayeeRepository;
use Newbee\Finance\Repository\Criteria\ErpPayeeSearchCriteria;
use Newbee\Finance\Requests\Payee\SearchErpPayeeRequest;

class ErpPayeeSearchService extends AbstractSearchService
{
    /**
     *@var SearchErpPayeeRequest $request
     */
    protected $request;

    /**
     *@var ErpPayeeRepository $repository
     */
    protected $repository;

    public function __construct(SearchErpPayeeRequest $request, ErpPayeeRepository $repository)
    {
        $this->request = $request;
        $this->repository = $repository;
    }

    protected function getSearchCriteriaClassName(): string
    {
        return ErpPayeeSearchCriteria::class;
    }

    /**
     * MySQL WHERE OR
     * @return array|string[]
     */
    protected function getOrConditionFields(): array
    {
        return [
            'payee_code'
        ];
    }

    /**
     * MySQL WHERE AND
     * @return array|string[]
     */
    protected function getAndConditionFields(): array
    {
        return [
            'payee_account',
            'examine',
            'producer_id',
            'customer_id',
            'difference'
        ];
    }

    /*
     * 区间，模糊，大小查询参数
     */
    protected function fieldStorage(): array
    {
        return [
            'created_at',
            'examine_at',
            'desc',
            'payee_list__agent_id',
        ];
    }

    /*
     * 区间，模糊，大小查询
     */
    protected function fieldSearchable(): array
    {
        return [
            'created_at' => '>=',
            'examine_at' => '<=',
            'desc' => 'like',
            'payee_list__agent_id' => '=',
        ];
    }
}
