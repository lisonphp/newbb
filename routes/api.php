<?php
$api = app(\Dingo\Api\Routing\Router::class);
$api->version('v1',function ($api){
    $api->group(['middleware' => 'jwt.auth'], function ($api) {
        
        exit('888888');
        //lison 测试 s
        $api->get('abc_au', [
            'uses' => '\Newbee\Report\Http\Api\Controllers\ErpAbcAuController@index',
            // 'middleware' => ['rbac.auth:can,AbcAu.index']
        ]);

        // 收款管理 添加-选择单据列表
        // $api->get('receipt_choose', [
        //     'uses' => '\Newbee\Finance\Http\Api\Controllers\ErpReceiptCheckController@choose',
        //     'middleware' => ['rbac.auth:can,ReceiptCheck.choose']
        // ]);

        

    });



});
