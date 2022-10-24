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
        Schema::create('pengeluaran', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal')->required();
            $table->string('deskripsi', 100)->required();
            $table->integer('jumlah')->nullable();
            $table->string('pemasukan')->nullable();
            $table->string('divisi_id');
            $table->string('status');
            $table->date('tanggal_respon')->nullable();
            $table->string('kategori')->nullable();
            $table->string('pembebanan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pengeluaran');
    }
};
