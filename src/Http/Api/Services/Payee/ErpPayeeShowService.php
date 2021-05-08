<?php
namespace Newbee\Finance\Http\Api\Services\Payee;

use Newbee\Finance\Repository\Contract\ErpPayeeRepository;
use Newbee\Finance\Requests\Payee\ShowErpPayeeRequest;

class ErpPayeeShowService
{
    /**
     * @var ShowErpPayeeRequest $request
     */
    protected $request;
    /**
     * @var ErpPayeeRepository
     */
    protected $repository;

    function __construct(ShowErpPayeeRequest $request, ErpPayeeRepository $repository)
    {
        $this->request = $request;
        $this->repository = $repository;
    }

    /**
     * 详情
     * @return mixed
     */
    public function show($payee_id)
    {
        $this->request->validated();
        return $this->repository->find($payee_id);
    }
}
