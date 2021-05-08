<?php
namespace Newbee\Finance\Http\Api\Services\Settlement;

use Newbee\Finance\Http\Api\Base\AbstractSearchService;
use Newbee\Finance\Repository\Contract\ErpSettlementRepository;
use Newbee\Finance\Repository\Criteria\ErpSettlementSearchCriteria;
use Newbee\Finance\Requests\Settlement\SearchErpSettlementRequest;

class ErpSettlementSearchService extends AbstractSearchService
{
    /**
     *@var SearchErpSettlementRequest $request
     */
    protected $request;

    /**
     *@var ErpSettlementRepository $repository
     */
    protected $repository;

    protected $ifbalance;

    public function __construct(SearchErpSettlementRequest $request, ErpSettlementRepository $repository)
    {
        $this->repository = $repository;
        //当前余额
        if($request['balance']=='2'){
            $this->ifbalance='<';
            $request['balance']=1;
        }elseif($request['balance']=='-1'){
            $this->ifbalance='<';
            $request['balance']=-0.0001;
        }elseif($request['balance']=='1'){
            $this->ifbalance='>';
            $request['balance']=0.0001;
        }
        $this->request = $request;
    }

    protected function getSearchCriteriaClassName(): string
    {
        return ErpSettlementSearchCriteria::class;
    }

    /**
     * MySQL WHERE OR
     * @return array|string[]
     */
    protected function getOrConditionFields(): array
    {
        return [];
    }

    /**
     * MySQL WHERE AND
     * @return array|string[]
     */
    protected function getAndConditionFields(): array
    {
        return [
        ];
    }

    /*
     * 区间，模糊，大小查询参数
     */
    protected function fieldStorage(): array
    {
        return [
            'balance',
            // 'customer_name',
        ];
    }

    /*
     * 区间，模糊，大小查询
     */
    protected function fieldSearchable(): array
    {
        return [
            // 'settlement_balance' =>$this->ifbalance,
            // 'balance' =>'<',
            'balance' =>$this->ifbalance,
            // 'customer_name' => 'like',
        ];
    }
}
