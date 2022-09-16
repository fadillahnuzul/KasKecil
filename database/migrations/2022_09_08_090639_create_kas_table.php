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
        Schema::create('kas', function (Blueprint $table) {
            $table->id()->autoIncrement();
            $table->date('tanggal')->required();
            $table->string('deskripsi', 100)->required();
            $table->boolean('status')->nullable();
            $table->integer('debit')->nullable();
            $table->integer('kredit')->nullable();
            $table->string('mutasi')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('kas');
    }
};
