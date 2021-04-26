<?php
$api = app(\Dingo\Api\Routing\Router::class);
$api->version('v1',function ($api){
    $api->group(['middleware' => 'jwt.auth'], function ($api) {

        //lison 测试 
        $api->get('abc_b', [
            'uses' => '\Newbee\Syuue\Http\Api\Controllers\ErpReceiptCheckController@index',
            'middleware' => ['rbac.auth:can,ReceiptCheck.index']
        ]);


        /*
         * 单票入仓核单调整
         */
        $api->put('receipt_check/{receipt_check_id}', [
            'uses' => '\Newbee\Finance\Http\Api\Controllers\ErpReceiptCheckController@update',
            'middleware' => ['rbac.auth:can,ReceiptCheck.update']
        ]);

        /*
         * 收款添加
         */
        $api->post('payee', [
            'uses' => '\Newbee\Finance\Http\Api\Controllers\ErpPayeeController@store',
            'middleware' => ['rbac.auth:can,Payee.store']
        ]);

        /*
         * 收款删除
         */
        $api->delete('payee/{payee_id}', [
            'uses' => '\Newbee\Finance\Http\Api\Controllers\ErpPayeeController@destroy',
            'middleware' => ['rbac.auth:can,Payee.destroy']
        ]);

        /*
         * 综合结算添加备注
         */
        $api->post('settlement/{reconciliation_id}', [
            'uses' => '\Newbee\Finance\Http\Api\Controllers\ErpSettlementController@add_desc',
            'middleware' => ['rbac.auth:can,Settlement.add_desc']
        ]);

    });

    /**
     * 单票入仓核单导出
     */
    $api->get('receiptcheck_export',[
        'uses'=> '\Newbee\Finance\Http\Api\Controllers\ErpReceiptCheckController@export',
    ]);

    /**
     * 收款导出
     */
    $api->get('payee_export',[
        'uses'=> '\Newbee\Finance\Http\Api\Controllers\ErpPayeeController@export',
    ]);

    /**
     * 综合结算导出
     */
    $api->get('settlement_export',[
        'uses'=> '\Newbee\Finance\Http\Api\Controllers\ErpSettlementController@export',
    ]);

});
