<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateErpPayeeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('erp_payee', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('payee_id')->comment('收款表自增id');
            $table->string('payee_code','100')->comment('单据编号');
            $table->string('accmonth','11')->comment('会计期间');
            $table->Integer('customer_id')->comment('客户id');
            $table->double('accounts_receivable', 10 ,3)->comment('应收金额')->default(0.000);
            $table->double('payee_difference', 10 ,3)->comment('收款差额')->default(0.000);
            $table->double('payee_amount', 10 ,3)->comment('收款金额');
            $table->tinyInteger('currency_id')->comment('币别id')->default(1);
            $table->tinyInteger('payee_account')->comment('收款账户');
            $table->tinyInteger('write_currency')->comment('核销币别id')->default(1);
            $table->double('rate',10,8)->comment('核销汇率')->default(1.00000000);
            $table->double('write_amount', 10 ,3)->comment('核销金额')->nullable(true);
            $table->double('write_difference', 10 ,3)->comment('核销差额')->default(0.000);
            $table->tinyInteger('difference')->comment('是否存在差异(1.不存在，2存在)')->default(1);
            $table->string('water_bill_img','100')->comment('水单图片')->nullable(true);
            $table->string('invoice_img','100')->comment('发票图片')->nullable(true);
            $table->text('desc')->comment('备注')->nullable(true);
            $table->text('inside_desc')->comment('内部备注')->nullable(true);
            $table->tinyInteger('examine')->comment('审核标识(1未审核，2已审核)')->default(1);
            $table->Integer('producer_id')->comment('制单人id');
            $table->Integer('examine_id')->comment('审核人id')->nullable(true);
            $table->timestamp('examine_at')->comment('审核时间')->nullable(true);
            $table->timestamp('payee_update_at')->comment('修改时间')->nullable(true);
            $table->timestamp('payee_created_at')->comment('添加时间')->useCurrent();
            $table->softDeletes('deleted_at');
        });
        DB::statement("ALTER TABLE `erp_payee` comment '新蜂-收款表'"); //表注释
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('erp_payee');
    }
}
