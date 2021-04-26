<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateErpPayeeListTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('erp_payee_list', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('payee_list_id')->comment('收款表副表自增id');
            $table->bigInteger('payee_id')->comment('收款表外键id');
            $table->string('payee_list_code','100')->comment('单据编号');
            $table->Integer('customer_id')->comment('客户id');
            $table->tinyInteger('out_warehouse_type')->comment('单据类型(1.单票入仓单,2.单票出仓单,3.问题工单)')->default(1);
            $table->string('payee_list_no','100')->index('payee_list_no')->unique('payee_list_no')->comment('运单号');
            $table->string('payee_list_order','100')->comment('订单号')->nullable(true);
            $table->string('payee_list_transfer_order','100')->comment('转单号')->nullable(true);
            $table->smallInteger('expense_category')->comment('应收费用类别（1.运费）')->default(1);
            $table->Integer('agent_id')->index('agent_id')->comment('代理id')->nullable(true);
            $table->smallInteger('agent_csm_id')->comment('结算方式')->default(1);
            $table->string('bill_no','100')->comment('提单号')->nullable(true);
            $table->string('inside_no','100')->comment('内部编号')->nullable(true);
            $table->timestamp('bill_at')->comment('单据日期');
            $table->timestamp('settlement_at')->comment('应结日期');
            $table->double('payee_list_amount', 10 ,3)->comment('应收金额');
            $table->tinyInteger('currency_id')->comment('应收币别id')->default( 1);
            $table->timestamp('payee_list_update_at')->comment('修改时间')->nullable(true);
            $table->timestamp('payee_list_created_at')->comment('添加时间')->useCurrent();
            $table->softDeletes('deleted_at');
        });
        DB::statement("ALTER TABLE `erp_payee_list` comment '新蜂-收款表副表'"); //表注释
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('erp_payee_list');
    }
}
