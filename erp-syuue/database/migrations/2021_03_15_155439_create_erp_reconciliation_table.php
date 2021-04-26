<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateErpReconciliationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('erp_reconciliation', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('reconciliation_id');
            $table->string('customer_id',50)->comment('客户id');
            $table->tinyInteger('examine')->comment('核销标识(1未核销，2已核销)')->default(1);
            $table->timestamp('reconciliation_date')->comment('日期');
            $table->string('reconciliation_no','100')->index('reconciliation_no')->unique('reconciliation_no')->comment('运单号');
            $table->string('reconciliation_transfer_order','100')->comment('转单号')->nullable(true);
            $table->bigInteger('reconciliation_number')->comment('件数');
            $table->double('reconciliation_weight',10,3)->index('reconciliation_weight')->comment('实重')->default(0.000);
            $table->double('reconciliation_square_division',10,3)->comment('材积')->default(0.000);
            $table->double('reconciliation_settlement_weight',10,3)->comment('结算重')->default(0.000);
            $table->Integer('channel_id')->index('channel_id')->comment('渠道id');
            $table->Integer('country_id')->index('country_id')->comment('目的地id');
            $table->text('reconciliation_goods_name')->comment('商品名称(多个逗号隔开)');
            $table->double('reconciliation_amount',10,3)->comment('金额')->default(0.000);
            $table->tinyInteger('currency_id')->comment('币别id')->default(1);
            $table->smallInteger('expense_category')->comment('应收费用类别（1.运费）')->default(1);
            $table->string('desc',100)->comment('备注')->nullable(true);
            $table->timestamp('reconciliation_update_at')->comment('修改时间')->nullable(true);
            $table->timestamp('reconciliation_created_at')->comment('添加时间')->useCurrent();
            $table->softDeletes('deleted_at');
        });
        DB::statement("ALTER TABLE `erp_reconciliation` comment '新蜂-客户对账表'"); //表注释
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('erp_reconciliation');
    }
}
