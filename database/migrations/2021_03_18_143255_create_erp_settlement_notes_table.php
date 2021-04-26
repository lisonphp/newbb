<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateErpSettlementNotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('erp_settlement_notes', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('settlement_notes_id');
            $table->bigInteger('reconciliation_id')->comment('客户对账表id');
            $table->bigInteger('producer_id')->comment('添加人id');
            $table->string('url',255)->comment('链接')->nullable(true);
            $table->string('desc',100)->comment('备注');
            $table->timestamp('settlement_notes_update_at')->comment('修改时间')->nullable(true);
            $table->timestamp('settlement_notes_created_at')->comment('添加时间')->useCurrent();
            $table->softDeletes('deleted_at');
        });
        DB::statement("ALTER TABLE `erp_settlement_notes` comment '新蜂-综合结算备注表'"); //表注释
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('erp_settlement_notes');
    }
}
