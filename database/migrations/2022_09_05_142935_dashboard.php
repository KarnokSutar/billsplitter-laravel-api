<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        {
            Schema::create('dashboard', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('bill_id');
                $table->unsignedBigInteger('creditor_id');
                $table->unsignedBigInteger('debtor_id');
                $table->unsignedBigInteger('group_id');
                $table->integer('amount');
                $table->foreign('bill_id')->references('id')->on('bill-splitter');
                $table->foreign('creditor_id')->references('id')->on('users'); 
                $table->foreign('debtor_id')->references('id')->on('users'); 
                $table->foreign('group_id')->references('id')->on('groups');  
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
